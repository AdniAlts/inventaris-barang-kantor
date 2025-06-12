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
- [Composer](https://getcomposer.org/) - Manajer dependensi untuk PHP.
- [NPM](https://www.npmjs.com/) - Manajer paket untuk JavaScript.

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
   - Proyek ini menggunakan file `.env` untuk variabel lingkungan. Anda dapat menyalin file `.env-test` menjadi `.env` dan mengubahnya sesuai kebutuhan Anda. File ini akan berisi kredensial basis data dan konfigurasi lainnya.

6. **Server Web**
   - Arahkan server web Anda (misalnya Apache, Nginx) ke direktori publik proyek.
   - Pastikan server web Anda dikonfigurasi untuk menangani file PHP.
   - Untuk XAMPP, Anda dapat menempatkan folder ini di `htdocs` dan mengaksesnya melalui `http://localhost/inventaris-barang-kantor`.

7. **Database**
   - Buat basis data baru dan impor tabel yang diperlukan. Skema SQL dapat ditemukan di `src/config/sql/akupergi.sql`

## Anggota Kelompok

- Reyhan Putra Ariutama - 3124600061
- Abubakar Adni - 3124600064
- Muhammad Fathoni Widyawanto - 3124600075
- James Eugene Sarongallo Palisungan - 3124600077
- Adryan Fahmi Ramadhan - 3124600082
- Candra Putra Pratama - 3124600086
- Ahmad Izzudin Arrosyid - 3124600087
