# Sistem Informasi Inventaris & Pengadaan Laboratorium (InApp Lab Inventory)

Aplikasi manajemen inventaris, bahan habis pakai (BHP), pengadaan barang, dan log pemeliharaan aset laboratorium berbasis Node.js, Express, Pug, dan MySQL. Sistem ini dirancang menggunakan arsitektur MVC (Model-View-Controller) dengan inspirasi struktur rute, migrasi, dan seeder seperti Laravel.

---

## 🚀 Fitur Utama & Pembaruan Sistem

### 1. Struktur Database Modular & Artisan-like Runner
*   **11 Tabel Migrasi (`database/migrations/`):** Terdiri dari tabel website penjualan (`products`, `sales`) dan 9 tabel operasional laboratorium (`roles`, `users`, `user_roles`, `ruangan`, `bhp`, `draft_pengadaan`, `detail_draft`, `inventaris`, `maintenance_log`).
*   **Runner Script Database (`database/runner.js`):** Script CLI kustom untuk mengelola siklus database dengan perintah cepat yang didaftarkan di `package.json`:
    *   `npm run migrate` - Menjalankan file migrasi secara berurutan.
    *   `npm run migrate:rollback` - Membatalkan (rollback) migrasi secara terbalik.
    *   `npm run db:seed` - Mengisi tabel dengan data seeder dummy.
    *   `npm run migrate:fresh` - Menghapus semua tabel dan membangun ulang database bersih.

### 2. Autentikasi Sesi & Multi-Role (5 Peran)
Sistem diatur dengan alur **Login-First** di mana seluruh halaman utama terproteksi middleware autentikasi dan dialihkan ke `/signin`. Navigasi sidebar disajikan secara dinamis sesuai peran akun yang sedang aktif:

1.  **Administrator:**
    *   Menambah dan menghapus akun pengguna sistem.
    *   Mengelola database lokasi ruangan laboratorium.
2.  **Kepala Laboratorium:**
    *   Membuat draf pengadaan tahunan baru.
    *   Menambahkan item inventaris/BHP beserta link pembelian & memilih aset lama yang akan digantikan.
    *   Mengunci draf sebelum dikirim ke Ketua Prodi.
3.  **Ketua Program Studi:**
    *   Melakukan verifikasi, menyetujui (`approved`), atau menolak (`rejected`) item barang pengadaan.
    *   Memfinalisasi draf pengadaan.
4.  **Staf Administrasi:**
    *   Melihat draf pengadaan yang telah difinalisasi.
    *   Mencatat tanggal penerimaan barang, label nomor inventaris, penempatan ruangan, dan generate kode QR.
5.  **Staf Laboratorium:**
    *   Mengelola stok ketersediaan barang habis pakai (BHP).
    *   Mencatat log perawatan aset (*maintenance*) yang otomatis mengurangi stok BHP yang terpakai.

### 3. Perbaikan Responsivitas & Resolusi Jalur Aset (Pembaruan Terkini)
*   **Perbaikan Jalur Aset Absolut:** Mengubah pemanggilan berkas favicon, CSS (`/assets/css/style.css`), dan modul JavaScript (`/assets/js/main.js`) pada master layout menjadi jalur absolut. Hal ini mencegah error 404 (file CSS/JS rusak/tidak termuat) saat mengakses halaman di dalam sub-rute bertingkat seperti `/admin/users` atau `/staf-lab/maintenance`.
*   **Pencegahan Pemblokiran Layar (Overlay Blocker):** Menambahkan CSS media query kustom di master layout (`layout.pug`) untuk menyembunyikan elemen `.overlay` secara paksa (`display: none !important`) pada resolusi layar lebar/desktop (lebar $\ge 992\text{px}$). Juga dilengkapi event penutup inline `onclick` langsung pada elemen overlay sebagai pengaman tambahan.
*   **Sinkronisasi Sidebar Aktif:** Memperbarui fungsi pencocokan tautan aktif pada `assets/js/sidebar.js` agar membandingkan `window.location.pathname` penuh (bukan parsing nama berkas), sehingga indikator visual sidebar aktif (`.active`) selalu cocok dan konsisten di setiap halaman.

---

## 🔑 Kredensial Akun Pengujian (Password: `password`)

Halaman `/signin` dilengkapi fitur pengisian cepat (*autofill click*) untuk akun berikut:
*   **Admin:** `admin@mail.com`
*   **Kepala Lab:** `kepala@mail.com`
*   **Ketua Prodi:** `prodi@mail.com`
*   **Staf Administrasi:** `stafadmin@mail.com`
*   **Staf Lab:** `staflab@mail.com`

---

## 💻 Cara Menjalankan Aplikasi

1.  **Konfigurasi Database:**
    Sesuaikan kredensial koneksi MySQL XAMPP Anda di berkas [config/db.js](file:///c:/Users/ASUS/Downloads/Capstone2/config/db.js). Secara default:
    ```javascript
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'dblab'
    ```

2.  **Instalasi Dependensi:**
    ```bash
    npm install
    ```

3.  **Setup Database (Migrasi & Seeding):**
    ```bash
    npm run migrate:fresh
    npm run db:seed
    ```

4.  **Jalankan Server Lokal:**
    ```bash
    npm run dev
    ```
    Aplikasi akan berjalan di [http://localhost:3000](http://localhost:3000).
