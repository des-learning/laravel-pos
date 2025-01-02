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

## 001. Seed user

Kita akan mengubah [seeder](https://laravel.com/docs/11.x/seeding) untuk membuat
user admin dengan password default, password.

Jalankan seeder dengan perintah:

```sh
php artisan db:seed --class=AdminUserSeeder
```

Setelah meng-seed database dengan user admin, kita bisa login dengan menggunakan
user `admin@localhost` dan password `password`.

## 002. Menghapus fitur self registration

Berikutnya kita akan menghapus fitur self registration. Kita akan membuat fitur
untuk me-manage user pada task yang akan datang.

- Hapus link regiter pada halaman index
  `routes/auth.php` hapus routing untuk registrasi.

## 003. Membuat fitur product

Pada tahapan ini kita akan memulai membuat fitur product.

1. Buat model + database migration untuk model `Product`
   `php artisan make:model Product -m -f`

2. Model akan terbentuk di `app/Models/Product.php`

3. File database migration akan terbentuk di `database/migrations/XXXX_XX_XX_XXXXXX_create_products_table.php`

4. File factory akan terbentu di `database/factories/ProductFactory.php`

`php artisan` merupakan perintah CLI pada framework Laravel untuk memudahkan kita
mengelola aplikasi. Salah satu fitur yang kita gunakan pada tahapan ini adalah untuk
men-_generate_ model sebagai class yang digunakan untuk interaksi dengan data.
Dan database migration untuk membuat struktur database.

Factory nanti akan kita gunakan untuk membuat test/seeder Product.

5. File `database/seeders/ProductSeeder.php` untuk membuat sample data product.

Jalankan seeder untuk membuat sample product.

```sh
php artisan db:seed --class=ProductSeeder
```

## 004. Memasang filament

Untuk mempercepat development, aplikasi ini menggunakan [Filament](https://filamentphp.com/)
sebagai framework halaman admin. Dengan menggunakan framework ini,
kita bisa dengan cepat membuat form-form dan halaman lain yang biasanya diguankan
oleh admin.

URL untuk mengakses aplikasi POS menggunakan filament berada di:
`http://localhost:8000/point-of-sale`

## 005. Membuat halaman fitur product menggunakan filament

Pada task ini kita akan membuat fitur halaman Product:

1. List Product
2. Create new Product
3. Edit Product

Task ini akan dibuat menjadi beberapa commit:

1. inisialisasi resource product
2. halaman list product
3. halaman new product
4. halaman edit product

## 006. Migrasi untuk table Sale Transaction dan Item

Berikutnya kita mempersiapkan data store untuk menyimpan informasi penjualan.

SaleTransaction merupakan master/header table untuk menyimpan informasi penjualan.
Sedangkan SaleTransactionItem merupakan masing-masing item yang terjual pada
transaksi penjualan.

## 007. Draft Transaction sale Form

Pada tahapan ini kita membuat halaman untuk menginput sale transaction.

Fitur ini agak rumit karena kita harus membuat halaman custom pada filament
dengan tujuan bisa menginput form master detail.

Kita akan membuat satu input SKU, dan quantity barang dan kemudian
menampilkan daftar barang yang dijual pada table.

Data pada tahapan ini belum disimpan ke database hanya berupa tampilan di web.
Logic untuk menyimpan data akan dilakukan pada tahap berikutnya.

## 008. Simpan transaksi penjualan

Berikutnya kita melakukan finishing terhadap form sale transaction dan
menyimpan data transaksi penjualan yang telah diinput.
