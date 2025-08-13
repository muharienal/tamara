# TAMARA — Install (Windows & Linux)

Aplikasi **PHP Native** dengan **Composer**. Panduan ini hanya untuk **instalasi & menjalankan**.

## Prasyarat
- PHP **8.1+**
- Composer **2.x**
- MySQL/MariaDB
- Apache (aktif **mod_rewrite**)
- Web root diarahkan ke folder **`public/`** *(atau akses langsung via `…/public/`)*

> **Penting:** Letakkan project di **htdocs** saat memakai XAMPP/LAMPP.  
> - Windows (XAMPP): `C:\xampp\htdocs\tamara`  
> - Linux (LAMPP): `/opt/lampp/htdocs/tamara`  
> - Linux (Apache native): `/var/www/tamara` (DocumentRoot → `public/`)

---

## Windows (XAMPP)

### 1) Clone ke `htdocs`
```bat
cd C:\xampp\htdocs
git clone https://github.com/<org-atau-username>/tamara.git
cd tamara
```

### 2) Composer
```bat
composer install
composer dump-autoload -o
```

### 3) Database (phpMyAdmin)
- Buka: `http://localhost/phpmyadmin`
- **Database** → **New** → buat: `tamara`
- **Import** → pilih file **.sql** (mis. `database/tamara.sql`) → **Go**

### 4) Konfigurasi & Jalankan
Edit `config/database.php`:
```php
<?php
return [
  'host'     => '127.0.0.1',
  'database' => 'tamara',
  'username' => 'root',
  'password' => '',
];
```
Start **Apache** & **MySQL** di XAMPP.  
Akses: `http://localhost/tamara/public/`

---

## Linux (Apache native)

### 1) Clone ke `/var/www`
```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/<org-atau-username>/tamara.git
cd tamara
```

### 2) Composer
```bash
sudo composer install
sudo composer dump-autoload -o
```

### 3) Database (phpMyAdmin atau MySQL CLI)
- phpMyAdmin (jika terpasang) → buat DB `tamara` → **Import** file **.sql** (mis. `database/tamara.sql`)  
- atau MySQL CLI:
  ```bash
  sudo mysql -e "CREATE DATABASE tamara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  # lalu import:
  # mysql -u root -p tamara < path/ke/file.sql
  ```

### 4) Konfigurasi & Jalankan
- Edit `config/database.php` sesuai kredensial.  
- Set **DocumentRoot** ke `/var/www/tamara/public` *(atau akses `http://localhost/tamara/public/`)*  
- Pastikan **mod_rewrite** aktif, lalu **restart Apache**.

---

## Linux (LAMPP/XAMPP for Linux) — ringkas
```bash
# Start LAMPP
sudo /opt/lampp/lampp start

# Clone ke htdocs LAMPP
cd /opt/lampp/htdocs
sudo git clone https://github.com/<org-atau-username>/tamara.git
cd tamara

# Composer
sudo /opt/lampp/bin/php /usr/local/bin/composer install || composer install
sudo composer dump-autoload -o
```
- Buat DB `tamara` via `http://localhost/phpmyadmin` → **Import** file **.sql**.  
- Edit `config/database.php`.  
- Akses: `http://localhost/tamara/public/`

---

## Selesai
- Akses halaman via `…/public/` *(atau URL cantik jika VirtualHost diarahkan ke `public/`).*  
- Jika error autoload: jalankan `composer dump-autoload -o`.
