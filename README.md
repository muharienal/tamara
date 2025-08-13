**TAMARA - Install (Windows & Linux)**
Aplikasi PHP Native dengan Composer. Panduan ini hanya untuk instalasi & menjalankan.

**Prasyarat**
PHP 8.1+
Composer 2.x
MySQL/MariaDB
Apache (mod_rewrite aktif)
Web root diarahkan ke folder public/ (atau akses via …/public/)

**Windows (XAMPP)**
**Clone**
cd C:\xampp\htdocs
git clone https://github.com/<org-atau-username>/tamara.git
cd tamara
**Composer**
composer install
composer dump-autoload -o

1. Buat database (mis. tamara) di phpMyAdmin.
2. Edit config/database.php:
return ['host'=>'127.0.0.1','database'=>'tamara','username'=>'root','password'=>''];
3. Jalankan: Apache & MySQL di XAMPP.
4. Akses: http://localhost/tamara/public/

**Linux (Apache)**
**Clone**
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/<org-atau-username>/tamara.git
cd tamara
**Composer**
sudo composer install
sudo composer dump-autoload -o

1. Buat database (mis. tamara).
2. Edit config/database.php sesuai kredensial.
3. Jadikan DocumentRoot ke /var/www/tamara/public (atau akses via http://localhost/tamara/public/).
4. Pastikan mod_rewrite aktif, lalu restart Apache.

**Selesai**
1. Login/halaman lain diakses via …/public/ (atau URL cantik jika VirtualHost sudah diarahkan ke public/).
2. Jika ada error autoload: jalankan composer dump-autoload -o.
