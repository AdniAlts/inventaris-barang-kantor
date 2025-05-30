<?php
/**
 * File: search.php
 * Deskripsi: Modul ini bertanggung jawab untuk menangani logika pencarian data dari database.
 * Saat ini, mendukung pencarian berdasarkan 'barang' atau 'all'.
 */
require_once "../config/db.php";
require_once "../utils/get_names.php";

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
  public static function search($keyword, string $type, array $active_categories = [], array $active_states = [], array $active_statuses = []) {
    // Validasi input
    if (!$keyword && empty($active_categories) && empty($active_states) && empty($active_statuses))
      return null;

    // Koneksi dari modul db.php
    $db = new db(); 
    
    // Query utama
    $base_query = "SELECT b.kode_barang, b.nama, b.status, s.nama AS state, k.nama AS kategori
                  FROM barang b
                  JOIN state s ON b.state_id = s.id_state
                  JOIN kategori k ON b.kategori_id = k.id_kategori";
    
    list($where_clauses, $params, $param_types) = self::queryConstruct($keyword, $type, $active_categories, $active_states, $active_statuses);

    // Perancangan query SQL baru dengan kondisi filter
    $query = $base_query;
    if (!empty($where_clauses)) {
      $query .= " WHERE " . implode(" AND ", $where_clauses);
    } else {
      $db->conn->close();
      return null;
    }

    $sql = $db->conn->prepare($query);
    if (!$sql) {
      error_log("Persiapan query gagal. Error: " . $db->conn->error . ". Query: " . $query);
      $db->conn->close();
      return null;
    }

    if (!empty($param_types) && !empty($params)) {
      $sql->bind_param($param_types, ...$params);
    }

    // Eksekusi query
    if ($sql->execute()) 
      if ($result = $sql->get_result()) {
        $sql->close();
        $db->conn->close();
        return $result;
      } else {
        error_log("Gagal mengambil data. Error: " . $sql->error);
        $sql->close();
        $db->conn->close();
        return null;
      }
    else {
      error_log("Pelaksanaan query gagal. Error: " . $sql->error);
      $sql->close();
      $db->conn->close();
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
        // Mencari di kolom barang
        $where_clauses[] = "b.nama LIKE ?";
        $params[] = $searchTerm;
        $param_types .= "s";
      } elseif ($type === 'all') {
        // Mencari di beberapa kolom untuk tipe 'all'
        $where_clauses[] = "(b.kode_barang LIKE ? OR b.nama LIKE ? OR b.status LIKE ? OR s.nama LIKE ? OR k.nama LIKE ?)";
        
        // 5 (sesuai jumlah kolom) placeholder untuk pencarian keyword 'all'
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
}
?>