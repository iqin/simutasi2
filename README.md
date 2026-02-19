# Simutasi

Simutasi adalah sistem informasi mutasi guru berbasis web yang dikembangkan menggunakan CodeIgniter 4.
Sistem ini membantu dalam pengelolaan mutasi guru secara efisien.

## Fitur Utama
- Manajemen usulan mutasi guru
- Verifikasi dan persetujuan mutasi oleh Dinas Pendidikan
- Pelacakan status mutasi secara real-time
- Sistem peran pengguna (admin, operator, kepala dinas, dll.)

## Teknologi yang Digunakan
- **Backend:** CodeIgniter 4 (PHP 8.1+)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap + AdminLTE
- **Version Control:** Git & GitHub

## Cara Instalasi
1. Clone repository ini:
   ```
   git clone https://github.com/pakiqin/simutasi.git
   ```
2. Masuk ke direktori proyek:
   ```
   cd simutasi
   ```
3. Install dependencies menggunakan Composer:
   ```
   composer install
   ```
4. Konfigurasi database di `.env` atau `app/Config/Database.php`
5. Jalankan migrasi database:
   ```
   php spark migrate
   ```
6. Jalankan aplikasi:
   ```
   php spark serve
   ```

## Kontribusi
Kontribusi sangat terbuka! Silakan fork repository ini, buat branch baru, lakukan perubahan, dan ajukan pull request.

## Lisensi
Proyek ini berlisensi di bawah MIT License.
