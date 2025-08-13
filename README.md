# TAMARA - Install (Windows & Linux)

Aplikasi **PHP Native** dengan **Composer**. Panduan ini hanya untuk **instalasi & menjalankan**.

## Prasyarat
- PHP **8.1+**
- Composer **2.x**
- MySQL/MariaDB
- Apache (aktif **mod_rewrite**)
- Web root diarahkan ke folder **`public/`** *(atau akses langsung via `…/public/`)*

---

## Windows (XAMPP)

### Clone
```bat
cd C:\xampp\htdocs
git clone https://github.com/<org-atau-username>/tamara.git
cd tamara
```

### Composer
```bat
composer install
composer dump-autoload -o
```

### Konfigurasi & Jalankan
1. Buat database (mis. **tamara**) di phpMyAdmin.  
2. Edit `config/database.php`:
   ```php
   <?php
   return [
     'host'     => '127.0.0.1',
     'database' => 'tamara',
     'username' => 'root',
     'password' => '',
   ];
   ```
3. Start **Apache** & **MySQL** di XAMPP.  
4. Akses: `http://localhost/tamara/public/`

---

## Linux (Apache)

### Clone
```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/<org-atau-username>/tamara.git
cd tamara
```

### Composer
```bash
sudo composer install
sudo composer dump-autoload -o
```

### Konfigurasi & Jalankan
1. Buat database (mis. **tamara**).  
2. Edit `config/database.php` sesuai kredensial.  
3. Set **DocumentRoot** ke `/var/www/tamara/public` *(atau akses `http://localhost/tamara/public/`)*  
4. Pastikan **mod_rewrite** aktif, lalu **restart Apache**.

---

## Selesai
- Halaman login/dll diakses via `…/public/` *(atau URL cantik jika VirtualHost diarahkan ke `public/`).*  
- Jika error autoload: jalankan `composer dump-autoload -o`.
