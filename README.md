# Inventaris Barang Kantor

Sebuah aplikasi berbasis web untuk mengelola inventaris barang kantor. Proyek ini dibuat sebagai tugas akhir praktikum Pemrograman Web.

## Fitur Utama

- **Manajemen Data Master**: Fitur ini memungkinkan administrator untuk mengelola data inti yang akan digunakan di seluruh sistem. Ini mencakup:
    - **Kategori**: Membuat, membaca, memperbarui, dan menghapus (CRUD) kategori barang (misalnya: Elektronik, ATK).
    - **Jenis**: Mengelola jenis barang dalam setiap kategori (misalnya: Laptop, Printer di bawah Elektronik).
    - **Kondisi**: Mendefinisikan kondisi barang (misalnya: Baik, Rusak Ringan, Rusak Berat).
    - **Barang**: Menambahkan data barang secara spesifik, mengaitkannya dengan kategori, jenis, dan kondisi yang ada.

- **Manajemen Peminjaman**: Memfasilitasi proses sirkulasi barang di dalam kantor.
    - **Peminjaman**: Pegawai dapat mengajukan peminjaman barang yang tersedia. Sistem akan mencatat siapa peminjamnya dan kapan barang tersebut dipinjam.
    - **Pengembalian**: Setelah selesai digunakan, pegawai dapat melakukan pengembalian barang. Status barang akan diperbarui kembali menjadi tersedia.

## Perpustakaan yang Digunakan

- [Tailwind CSS](https://tailwindcss.com/) - Kerangka kerja CSS yang mengutamakan utilitas.
- [Flowbite](https://flowbite.com/) - Pustaka komponen yang dibangun di atas Tailwind CSS.
- [Composer](https://getcomposer.org/) - Pengelola dependensi untuk PHP.
- [NPM](https://www.npmjs.com/) - Pengelola paket untuk JavaScript.

## Struktur Folder

```
.
├── src
│   ├── config
│   ├── images
│   ├── modules
│   ├── pages
│   ├── storages
│   └── utils
├── vendor
├── node_modules
├── .env-test
├── .gitignore
├── .htaccess
├── composer.json
├── composer.lock
├── index.php
├── package.json
└── package-lock.json
```

## Pengaturan Lokal

1. **Clone repositori**
   ```bash
   git clone https://github.com/AdniAlts/inventaris-barang-kantor.git
   cd inventaris-barang-kantor
   ```

2. **Instal dependensi PHP**
   ```bash
   composer install
   ```

3. **Instal dependensi JavaScript**
   ```bash
   npm install
   ```

4. **Build CSS**
    ```bash
    npm run build
    ```

5. **Siapkan Environment**
   - Proyek ini menggunakan file `.env` untuk environment variable.File `.env-test` dapat disalin dan di _rename_ menjadi `.env` dan mengubahnya sesuai kebutuhan. File ini akan berisi kredensial database dan konfigurasi lainnya.

6. **Server Web**
   - Arahkan server web (misalnya Apache, Nginx) ke direktori proyek.
   - Pastikan server web telah dikonfigurasi untuk menangani file PHP.
   - Untuk XAMPP, Folder proyek ini dapat diletakkan di `htdocs` dan mengaksesnya melalui `http://localhost/inventaris-barang-kantor`.

7. **Database**
   - Buat basis data baru dan impor tabel yang diperlukan. Skema SQL dapat ditemukan di `src/config/sql/akupergi.sql`. Serta perlu untuk menjalankan _seeder_ untuk menambahkan data _dummy_ dengan mengunjungi `http://localhost/inventaris-barang-kantor/src/config/sql/biji.php`.

## Anggota Kelompok

- Reyhan Putra Ariutama - 3124600061
- Abubakar Adni - 3124600064
- Muhammad Fathoni Widyawanto - 3124600075
- James Eugene Sarongallo Palisungan - 3124600077
- Adryan Fahmi Ramadhan - 3124600082
- Candra Putra Pratama - 3124600086
- Ahmad Izzudin Arrosyid - 3124600087
