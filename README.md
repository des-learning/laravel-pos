# Point of Sale

Aplikasi point of sale sederhana menggunakan framework Laravel.

Menggunakan starter kit [Breeze dan Livewire](https://laravel.com/docs/11.x/starter-kits).

- Breeze, menyediakan authentication system.
- Livewire (Volt Functional API), menyediakan fitur untuk menulis
  aplikasi Single Page Application dengan minimal Javascript.
- AlpineJS, menyediakan fitur untuk menambahkan operasi pada frontend
  yang tidak bisa di-cover oleh Livewire.

## Lingkungan pengembangan

Lingkungan pengembangan mengikuti [dokumentasi Laravel](https://laravel.com/docs/11.x/installation)

- [PHP 8.3](https://www.php.net/manual/en/install.php)
  Runtime aplikasi PHP, untuk menjalankan script pada bagian backend.
  - [Composer](https://getcomposer.org/doc/00-intro.md)
      Package manager untuk aplikasi PHP
- [NodeJS](https://nodejs.org/en)
  Runtime untuk aplikasi Javascript, digunakan untuk memproses asset
  yang akan digunakan pada bagian frontend.

Konfigurasi default bisa dilihat pada `.env.example`.
Secara default, database yang digunakan adalah SQLite.
Copy file `.env.example` ke `.env` dan edit [sesuai kebutuhan](https://laravel.com/docs/11.x/configuration).
Jika menggunakan database SQLite, isikan konfigurasi `DB_DATABASE` dengan
absolute path file database, misalnya: `c:\\point-of-sale.db`.

Menggunakan code ini:

- Clone dari repository git.
- Jalankan `composer install` untuk menginstall semua dependensi PHP
  (memerlukan koneksi internet).
- Jalankan `npm install` untuk menginstall semua dependensi NodeJS
  (memerlukan koneksi internet).
- Jalankan `npm run build` untuk mengcompile asset frontend (Javascript & CSS).
- Copy file `.env.example` ke `.env`, edit sesuai kebutuhan.
  Jika menggunakan database SQLite, jalankan perintah berikut untuk
  membuat file database:
  - di Windows: `type > point_of_sale.db`
  - di Linux/Mac: `touch point_of_sale.db`
      Sesuaikan file `point_of_sale.db` sesuai dengan konfigurasi di `.env`
- Jalankan `php artisan migrate` untuk membuat schema database.
- Jalankan `composer run dev` untuk menjalankan aplikasi point of sale
  di localhost (default di port 8000).
- Akses aplikasi melalui browser di `http://localhost:8000`.
