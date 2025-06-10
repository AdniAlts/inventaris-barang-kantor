<?php
/**
 * File: search.php
 * Deskripsi: Modul ini bertanggung jawab untuk menangani logika pencarian data dari database.
 * Saat ini, mendukung pencarian berdasarkan 'barang' atau 'all'.
 */
require_once __DIR__ . "/../utils/get_names.php";

class Search {
  /**
   * Fungsi `search()`:
   * Berfungsi dalam melakukan pencarian data di database berdasarkan kata kunci dan tipe yang diberikan,
   * serta dapat dikombinasikan dengan filter kategori yang aktif.
   *
   * @param string|null $keyword Kata kunci yang akan dicari. Jika null atau string kosong, fungsi akan mengembalikan null.
   * @param string $type Tipe pencarian. Saat ini mendukung 'barang' dan 'all'.
   *                     - 'barang': Pencarian dilakukan pada kolom 'nama' di tabel 'barang'.
   *                     - 'all': Pencarian dilakukan pada kolom 'kode_barang', 'nama', dan 'status' di tabel 'barang',
   *                              serta pada kolom 'nama' di tabel 'state' (kondisi) dan 'kategori'.
   * @param array $active_categories Array berisi nama kategori yang aktif (misal ['Alat Tulis', 'Elektronik']).
   * @param array $active_states Array berisi nama kondisi (state) yang aktif.
   * @param array $active_statuses Array berisi nilai status yang aktif.
   * @param mysqli $connDB Obyek koneksi database yang sudah ada.
   * @return mysqli_result|null Mengembalikan objek mysqli_result yang berisi hasil query jika berhasil dan ada data,
   *                            atau null jika kata kunci tidak valid, query gagal dipersiapkan, atau tidak ada hasil.
   *                            Fungsi akan menghentikan eksekusi (die) jika eksekusi query atau pengambilan hasil gagal.
   * 
   * @example search("kertas", "barang");
   *          => Akan mencari "kertas" yang merupakan nama dari sebuah "barang".
   *          => Mengembalikan hasil pencarian dari query SQL yang berupa kolom data dengan nama barang "kertas" sebagai `mysqli_result`,
   *             atau null jika tidak ada hasil.
   *
   * @example search("alat tulis", "all", ['Alat Tulis'], ['Baik'], ['Tersedia']);
   *          => Akan mencari "alat tulis" dalam berbagai kolom dan memfilter berdasarkan kategori 'Alat Tulis', kondisi 'Baik', dan status 'Tersedia'.
   *          => Mengembalikan hasil pencarian (kode_barang, nama, status, state, kategori) sebagai `mysqli_result`.
   * 
   * @example search(null, "all", [], ['Rusak'], []);
   *          => Akan mencari semua barang dengan kondisi 'Rusak'.
   *          => Mengembalikan hasil pencarian (kode_barang, nama, status, state, kategori) sebagai `mysqli_result`.
   */
  public static function search($keyword, string $type, array $active_categories = [], array $active_states = [], array $active_statuses = [], mysqli $connDB) {
    // Validasi input
    if (!$keyword && empty($active_categories) && empty($active_states) && empty($active_statuses))
      return null;
    
    // Query utama
    $base_query = "SELECT b.kode_barang, j.nama AS item_name, b.status, s.nama AS state, k.nama AS kategori
                  FROM barang b
                  JOIN jenis j ON b.jenis_id = j.id_jenis
                  JOIN state s ON b.state_id = s.id_state
                  JOIN kategori k ON j.kategori_id = k.id_kategori";
    
    list($where_clauses, $params, $param_types) = self::queryConstruct($keyword, $type, $active_categories, $active_states, $active_statuses);

    // Perancangan query SQL baru dengan kondisi filter
    $query = $base_query;
    if (!empty($where_clauses)) {
      $query .= " WHERE " . implode(" AND ", $where_clauses);
    } else
      return null;

    $sql = $connDB->prepare($query);
    if (!$sql) {
      error_log("Persiapan query gagal. Error: " . $connDB->error . ". Query: " . $query);
      return null;
    }

    if (!empty($param_types) && !empty($params))
      $sql->bind_param($param_types, ...$params);

    // Eksekusi query
    if ($sql->execute()) 
      if ($result = $sql->get_result()) {
        $sql->close();
        return $result;
      } else {
        error_log("Gagal mengambil data. Error: " . $sql->error);
        $sql->close();
        return null;
      }
    else {
      error_log("Pelaksanaan query gagal. Error: " . $sql->error);
      $sql->close();
      return null;
    }
  }

  /**
   * Fungsi `queryContruct()`:
   * Berfungsi dalam pembentukan query untuk fungsi utama `search()`.
   * 
   * @param string|null $keyword Kata kunci pencarian.
   * @param string $type Tipe pencarian ('barang' atau 'all');
   * @param array $active_categories Nama-nama kategori yang sedang aktif.
   * @param array $active_states Nama-nama kondisi yang sedang aktif.
   * @param array $active_statuses Nama-nama status yang sedang aktif.
   * 
   * @return array Mengembalikan array untuk keperluan pembentukan struktur query pada fungsi utama `search()`.
   */
  private static function queryConstruct($keyword, string $type, array $active_categories = [], array $active_states = [], array $active_statuses = []) {
    $where_clauses = [];
    $params = [];
    $param_types = "";

    // Kondisi untuk kata kunci
    if ($keyword) {
      $searchTerm = "%" . $keyword . "%";
      if ($type === 'barang') {
        // Mencari di kolom nama item (dari tabel jenis)
        $where_clauses[] = "j.nama LIKE ?";
        $params[] = $searchTerm;
        $param_types .= "s";
      } elseif ($type === 'all') {
        // Mencari di beberapa kolom untuk tipe 'all'
        // Kolom yang dicari: kode_barang, nama item (dari jenis), status barang, nama state, nama kategori
        $where_clauses[] = "(b.kode_barang LIKE ? OR j.nama LIKE ? OR b.status LIKE ? OR s.nama LIKE ? OR k.nama LIKE ?)";
        
        // 5 placeholder untuk pencarian keyword 'all'
        for ($i = 0; $i < 5; $i++) {
          $params[] = $searchTerm;
          $param_types .= "s";
        }
      }
    }

    // Kondisi untuk filter kategori
    $category_filter_data = self::getCategoryFilterConditions($active_categories);
    if ($category_filter_data) {
      $where_clauses[] = $category_filter_data['clause'];
      $params = array_merge($params, $category_filter_data['params']);
      $param_types .= $category_filter_data['types'];
    }

    // Kondisi untuk filter state (kondisi barang)
    $state_filter_data = self::getStateFilterConditions($active_states);
    if ($state_filter_data) {
      $where_clauses[] = $state_filter_data['clause'];
      $params = array_merge($params, $state_filter_data['params']);
      $param_types .= $state_filter_data['types'];
    }

    // Kondisi untuk filter status barang
    $status_filter_data = self::getStatusFilterConditions($active_statuses);
    if ($status_filter_data) {
      $where_clauses[] = $status_filter_data['clause'];
      $params = array_merge($params, $status_filter_data['params']);
      $param_types .= $status_filter_data['types'];
    }
    
    return [$where_clauses, $params, $param_types];
  }

  /**
   * Fungsi `getCategoryFilterConditions()`:
   * Berfungsi dalam pembuatan kondisi SQL untuk filter kategori.
   *
   * @param array $categories Nama-nama kategori yang sedang aktif.
   * @return array|null Mengembalikan array dengan 'clause', 'params', 'types' atau null jika tidak ada filter aktif.
   * 
   * @example getCategoryFilterConditions(['alat tulis', 'elektronik']);
   *          => Akan menentukan kondisi yang akan digunakan dalam query untuk fungsi `search()`.
   *          => Mengembalikan array [ 'clause' => "(k.nama = ? OR k.nama = ?)",
   *                                   'params' => ['alat tulis', 'elektronik'],
   *                                   'types' => "ss" ]
   */
  private static function getCategoryFilterConditions(array $categories = []) {
    // Validasi filter
    if (empty($categories))
      return null;

    $category_conditions = [];
    $filter_params = [];
    $filter_param_types = "";

    // Perancangan kondisi query WHERE untuk filter kategori
    foreach ($categories as $category) {
      $category_conditions[] = "k.nama = ?";
      $filter_params[] = $category;
      $filter_param_types .= "s";
    }

    // Pembentukan array sebagai kumpulan data kategori query SQL
    return [
      'clause' => "(" . implode(" OR ", $category_conditions) . ")",
      'params' => $filter_params,
      'types' => $filter_param_types
    ];
  }

  /**
   * Fungsi `getStateFilterConditions()`:
   * Berfungsi dalam pembuatan kondisi SQL untuk filter state.
   *
   * @param array $states Nama-nama state yang aktif.
   * @return array|null Mengembalikan array dengan 'clause', 'params', 'types' atau null jika tidak ada filter aktif.
   * 
   * @example getStateFilterCondition(['aman', 'rusak']);
   *          => Akan menentukan kondisi yang akan digunakan dalam query untuk fungsi `search()`.
   *          => Mengembalikan array [ 'clause' => "(s.nama = ? OR s.nama = ?)",
   *                                   'params' => ['aman', 'rusak'],
   *                                   'types' => "ss" ]
   */
  private static function getStateFilterConditions(array $states = []) {
    if (empty($states))
      return null;

    $state_conditions = [];
    $filter_params = [];
    $filter_param_types = "";
    
    // Perancangan kondisi query WHERE untuk filter kondisi
    foreach ($states as $state_name) {
      $state_conditions[] = "s.nama = ?";
      $filter_params[] = $state_name;
      $filter_param_types .= "s";
    }

    // Pembentukan array sebagai kumpulan data kondisi query SQL
    return [
      'clause' => "(" . implode(" OR ", $state_conditions) . ")",
      'params' => $filter_params,
      'types' => $filter_param_types
    ];
  }

  /**
   * Fungsi `getStatusFilterConditions()`:
   * Berfungsi dalam pembuatan kondisi SQL untuk filter status.
   *
   * @param array $statuses Nilai-nilai status yang aktif.
   * @return array|null Mengembalikan array dengan 'clause', 'params', 'types' atau null jika tidak ada filter aktif.
   * 
   * @example getStateFilterCondition(['tersedia', 'dipinjam']);
   *          => Akan menentukan kondisi yang akan digunakan dalam query untuk fungsi `search()`.
   *          => Mengembalikan array [ 'clause' => "(b.status = ? OR b.status = ?)",
   *                                   'params' => ['tersedia', 'dipinjam'],
   *                                   'types' => "ss" ]
   */
  private static function getStatusFilterConditions(array $statuses = []) {
    if (empty($statuses))
      return null;

    $status_conditions = [];
    $filter_params = [];
    $filter_param_types = "";

    // Perancangan kondisi query WHERE untuk filter status
    foreach ($statuses as $status_value) {
      $status_conditions[] = "b.status = ?"; // Filter berdasarkan kolom 'status' di tabel 'barang'
      $filter_params[] = $status_value;
      $filter_param_types .= "s";
    }

    // Pembentukan array sebagai kumpulan data status query SQL
    return [
      'clause' => "(" . implode(" OR ", $status_conditions) . ")",
      'params' => $filter_params,
      'types' => $filter_param_types
    ];
  }

  /**
   * Fungsi `getAllItems()`:
   * Mengambil semua barang beserta detailnya (jenis, state, kategori) untuk digunakan di client-side (JavaScript).
   *
   * @param mysqli $connDB Objek koneksi database.
   * @return array Array berisi semua data barang atau array kosong jika terjadi error atau tidak ada data.
   */
  public static function getAllItems(mysqli $connDB): array {
    $items = [];
    $query = "SELECT b.kode_barang, 
                     j.nama AS item_name, 
                     b.status, 
                     s.nama AS state_name, 
                     k.nama AS category_name,
                     b.jenis_id, 
                     b.state_id, 
                     j.kategori_id
              FROM barang b
              JOIN jenis j ON b.jenis_id = j.id_jenis
              JOIN state s ON b.state_id = s.id_state
              JOIN kategori k ON j.kategori_id = k.id_kategori
              ORDER BY j.nama ASC"; 

    $sql = $connDB->prepare($query);
    if (!$sql) {
      error_log("Persiapan query gagal untuk getAllItems. Error: " . $connDB->error . ". Query: " . $query);
      return [];
    }

    if ($sql->execute()) {
      $result = $sql->get_result();
      if ($result) {
        while ($row = $result->fetch_assoc()) {
          $items[] = $row;
        }
        $result->free();
      } else {
        error_log("Gagal mengambil data untuk getAllItems. Error: " . $sql->error);
      }
    }
    $sql->close();
    return $items;
  }

  public static function getAllTypes(mysqli $connDB) {
    $types = [];
    $query = "SELECT DISTINCT j.id_jenis, j.nama , j.stok_tersedia, k.nama AS nama_kategori FROM jenis j JOIN barang b ON j.id_jenis = b.jenis_id JOIN kategori k ON k.id_kategori = j.kategori_id WHERE b.status = 'tersedia' AND b.state_id = 1 AND j.stok > 0";
    
    $sql = $connDB->prepare($query);
    if (!$sql) {
      error_log("Persiapan query gagal untuk getAllTypes. Error: " . $connDB->error . ". Query: " . $query);
      return [];
    }

    if ($sql->execute()) {
      $result = $sql->get_result();
      if ($result) {
        $i = 0;
        while ($row = $result->fetch_assoc()) {
          $types[] = $row;
          $types[$i]['gambar_url'] = self::getImageForType($connDB, $row['id_jenis']);
          $i++;
        }
        $result->free();
      } else {
        error_log("Gagal mengambil data untuk getAllTypes. Error: " . $sql->error);
      }
    }
    $sql->close();
    return $types;
  }

  private static function getImageForType(mysqli $connDB, $id_jenis) {
    $gambar = null;
    $query = "SELECT gambar_url FROM barang WHERE jenis_id = ?";

    $sql = $connDB->prepare($query);
    if (!$sql) {
      error_log(message: "Persiapan query gagal untuk getImageForType. Error: " . $connDB->error . ". Query: " . $query);
      return [];
    }
    $sql->bind_param("s", $id_jenis);

    if ($sql->execute()) {
      $result = $sql->get_result();
      if ($result) {
        while ($row = $result->fetch_assoc()) {
          if (!empty($row['gambar_url'])) {
            $gambar = $row['gambar_url'];
            break;
          }
        }
        $result->free();
      } else {
        error_log("Gagal mengambil data untuk getImageForType. Error: " . $sql->error);
      }
    }
    $sql->close();
    return $gambar;
  }
}
?>