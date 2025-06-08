<?php
class GetFromDB {
  public static function peminjaman(mysqli $connDB) {
    $peminjaman = [];
    
    $query_peminjaman_list = "SELECT p.id_peminjaman, p.deskripsi, p.tgl_peminjaman, p.tgl_balik, p.total_pinjam, p.status
                              FROM peminjaman p
                              ORDER BY p.id_peminjaman ASC"; 
    $query_peminjaman_barang = "SELECT j.nama as nama_barang, COUNT(pd.barang_kode) AS jumlah
                                FROM peminjaman p
                                JOIN peminjaman_detail pd ON p.id_peminjaman = pd.peminjaman_id
                                JOIN barang b ON pd.barang_kode = b.kode_barang
                                JOIN jenis j ON j.id_jenis = b.jenis_id
                                WHERE p.id_peminjaman = ?
                                GROUP BY j.nama
                                ORDER BY j.nama ASC";

    $sql_list = $connDB->prepare($query_peminjaman_list);
    if (!$sql_list) {
      error_log("Persiapan query_peminjaman_list gagal untuk peminjaman. Error: " . $connDB->error);
      return [];
    }
    
    $sql_barang = $connDB->prepare($query_peminjaman_barang);
    if (!$sql_barang) {
      error_log("Persiapan query_peminjaman_barang gagal untuk peminjaman. Error: " . $connDB->error);
      $sql_list->close();
      return [];
    }

    if ($sql_list->execute()) {
      $result_list = $sql_list->get_result();
      if ($result_list) {
        while ($row = $result_list->fetch_assoc()) {
          $peminjaman_id = $row['id_peminjaman'];
          $sql_barang->bind_param('i', $peminjaman_id);

          if ($sql_barang->execute()) {
            $result_barang = $sql_barang->get_result();
            $barang_list = [];
            
            if($result_barang) {
              while ($barang_row = $result_barang->fetch_assoc()) {
                  $barang_list[] = $barang_row;
              }
              $result_barang->free();
            }
            $row['barang'] = $barang_list;
          } else {
            error_log("Gagal mengeksekusi query barang untuk peminjaman id: $peminjaman_id. Error: " . $sql_barang->error);
            $row['barang'] = []; // Set to empty array on failure
          }
          
          $peminjaman[] = $row;
        }
        $result_list->free();
      } else {
        error_log("Gagal mengambil data peminjaman. Error: " . $sql_list->error);
      }
    } else {
        error_log("Gagal mengeksekusi query peminjaman list. Error: " . $sql_list->error);
    }
    $sql_list->close();
    $sql_barang->close();
    return $peminjaman;
  }
}

?>