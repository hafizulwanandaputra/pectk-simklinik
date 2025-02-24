# Sistem Informasi Manajemen Klinik Utama Mata Padang Eye Center Teluk Kuantan

**Aplikasi Sistem Informasi Manajemen Klinik Utama Mata Padang Eye Center Teluk Kuantan** adalah sebuah sistem yang digunakan untuk memenuhi proses bisnis pada Klinik Utama Mata Padang Eye Center Teluk Kuantan. Sistem ini memanajemen pasien, rawat jalan, rekam medis elektronik (sedang dalam pengembangan), stok obat, resep obat, dan transaksi. Aplikasi ini didasarkan pada [HWPweb Admin Template](https://github.com/hafizulwanandaputra/hwpweb-admin-template) yang juga didasarkan pada [CodeIgniter 4](https://github.com/codeigniter4/appstarter).

## Instalasi

1. Klon repositori ini.
2. Arahkan ke folder repositori yang diklon.
3. Buka aplikasi terminal dan jalankan `composer install --no-dev` dan `npm install`.

## Pengaturan

1. Salin `.env.example` ke `.env` dan sesuaikan untuk aplikasi ini, khususnya `requestURL` dan pengaturan basis data.
2. Buat basis data MySQL yang sesuai dengan nama basis data yang ditentukan dalam file `.env`.
3. Gunakan file `pectk-simklinik-no-data.sql.gz` di repositori ini untuk impor tabel-tabel ke dalam basis data.
4. Jalankan `php spark db:seed User` dan `php spark db:seed PwdTransaksi` untuk menyemai (_seed_) item basis data.
5. Untuk digunakan dengan server pengembangan PHP, jalankan `php spark serve` untuk memulai server. Biasanya [http://localhost:8080](http://localhost:8080). Anda dapat menggunakan port yang berbeda dengan menggunakan `php spark serve --port 8081`. Ganti `8081` dengan nomor port yang diinginkan. Anda perlu mengubah `requestURL` di `.env` agar sesuai dengan nomor port yang diinginkan.
6. Untuk penggunaan tanpa server pengembangan PHP seperti Apache atau Nginx, cukup buka dari URL seperti [http://localhost/pectk-farmasi](http://localhost/pectk-farmasi) atau yang lain berdasarkan konfigurasi server web Anda. Anda perlu mengubah `requestURL` di `.env` agar sesuai dengan alamat URL yang diinginkan.
   > URL dasar (_base URL_) didasarkan pada nilai `$_SERVER['SERVER_NAME']` PHP. Anda hanya perlu mengubah `requestURL` yang terdiri dari port dan subfolder (jika aplikasi disimpan dalam subfolder).
7. Untuk menjalankan websocket, ikuti instruksi pada bagian websocket.
8. Masuk menggunakan nama pengguna `admin` dan kata sandi `12345`. Anda perlu mengubah kata sandi dari `{base_url_anda}/settings/changepassword` dan kami sarankan untuk menggunakan kata sandi yang kuat demi keamanan yang lebih baik.

### Websocket

Konfigurasi URL websocket terletak pada `.env` dengan nilai `WS-URL-JS` untuk _frontend_ (contoh: `ws://localhost:8090`) dan `WS-URL-PHP` untuk _backend_ (contoh: `http://localhost:3000/notify`).

Jika Anda menjalankannya di peladen pribadi virtual (VPS) dengan SSL, gunakan `wss://alamatdomain.com/ws/` pada `WS-URL-JS` dan `https://alamatdomain.com/notify/` pada `WS-URL-PHP`. Pastikan Anda membuat hos virtual atau peladen proksi sesuai dengan peladen web yang Anda gunakan.

Contoh untuk nginx:

```
   location /ws/ {
      proxy_pass http://127.0.0.1:8090;
      proxy_http_version 1.1;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "upgrade";
      proxy_set_header Host $host;
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
      proxy_cache_bypass $http_upgrade;
      proxy_redirect off;
      proxy_read_timeout 3600;
   }

   location /notify/ {
      proxy_pass http://127.0.0.1:3000/notify;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_set_header Host $host;
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   }
```

Agar websocket berjalan dengan baik:

1. Izinkan port agar bisa menggunakan websocket dengan perintah sebagai berikut (Linux):
   ```
   sudo ufw allow 8090/tcp
   sudo ufw allow 3000/tcp
   ```
2. Instal `pm2` dengan perintah sebagai berikut:
   ```
   sudo npm insall -g pm2
   ```
3. Lalu, jalankan websocket dengan perintah sebagai berikut:
   ```
   sudo pm2 start websocket.js --name websocket
   sudo pm2 save
   sudo pm2 startup
   ```

### Puppeteer untuk ekspor PDF

Ekspor dokumen-dokumen PDF pada sistem ini menggunakan [Puppeteer](https://github.com/puppeteer/puppeteer) yang merupakan pustaka JavaScript untuk Node.js. Pastikan peladen Anda sudah memiliki Node.js. Jika ada masalah terkait dengan Puppeteer, [silakan kunjungi halaman ini](https://pptr.dev/troubleshooting).

## Pengaturan Aplikasi Web Progresif (PWA)

File `manifest.json` berisi konfigurasi aplikasi untuk PWA yang terletak di folder `public`.

Isi PWA:

- Di `app/Views/auth/templates/login.php` dan `app/Views/dashboard/templates/dashboard.php`, ada tag `<link rel="manifest" href="<?= base_url(); ?>/manifest.json">` untuk menginisialisasi `manifest.json`.

- Jika PWA terletak di subfolder, tambahkan subfolder di nilai `start_url` dan `src` di `manifest.json`.

Untuk menyiapkan aplikasi PWA:

1. Periksa atau ubah konfigurasi PWA di atas berdasarkan kebutuhan Anda.
2. Jalankan `php spark serve` atau `php spark serve --port 8081`. Ganti `8081` dengan nomor port yang diinginkan.
3. Buka alat pengembangan browser untuk memeriksa informasi manifes.
4. Jika konfigurasi memenuhi persyaratan PWA, Anda dapat menginstal PWA. Anda dapat meluncurkannya dari menu atau daftar aplikasi. Jangan lupa untuk menjalankan `php spark serve` (atau `php spark serve --port 8081` jika Anda menggunakan port yang berbeda) sebelum meluncurkan aplikasi.
5. Jika tidak menggunakan `localhost` yang dilayani menggunakan atau tidak menggunakan `php spark serve` seperti domain atau alamat IP server Anda, Anda harus menggunakan HTTPS.

> [!WARNING]
>
> Anda perlu menginstal ulang PWA jika port atau URL diubah. Pastikan port atau URL yang digunakan untuk PWA tidak bentrok dengan proyek lain.

## Peran Pengguna

Ada 4 peran pengguna yang digunakan pada aplikasi ini:

1. **Admin**: digunakan untuk mengelola semua hal dalam aplikasi ini dengan akses eksklusif untuk mengelola pengguna dan sesinya serta membuat kata sandi transaksi untuk pembatalan transaksi.
2. **Admisi**: digunakan untuk mengelola pasien dan mendaftarkan pasien untuk rawat jalan.
3. **Apoteker**: digunakan untuk mengelola stok obat, resep eksternal, dan mencetak e-tiket serta dokumen resep dari resep dokter yang telah dikonfirmasi.
4. **Dokter**: digunakan untuk memberikan diagnosis, tindakan, resep obat, dan resep kacamata yang diberikan kepada pasien, serta membuat surat perintah kamar operasi, laporan rawat jalan, dan laporan-laporan operasi.
5. **Kasir**: digunakan untuk mengelola tindakan dan melakukan transaksi atas tindakan dan obat-obatan.
6. **Perawat**: digunakan untuk memberikan asesmen, skrining, edukasi, dan pemeriksaan penunjang kepada pasien.

## Perubahan Penting dengan index.php

`index.php` tidak lagi berada di root proyek! File tersebut telah dipindahkan ke dalam folder `public`, demi keamanan dan pemisahan komponen yang lebih baik.

Ini berarti Anda harus mengonfigurasi server web Anda untuk "mengarah" ke folder `public` proyek Anda, dan bukan ke root proyek. Praktik yang lebih baik adalah mengonfigurasi host virtual untuk mengarah ke sana. Praktik yang buruk adalah mengarahkan server web Anda ke root proyek dan berharap untuk memasukkan `public/...`, karena logika dan kerangka kerja Anda yang lain akan terekspos.

**Harap** baca panduan pengguna untuk penjelasan yang lebih baik tentang cara kerja CI4!

## Manajemen Repositori

Gunakan _GitHub Issues_, di repositori utama ini, untuk melaporkan bug, kesalahan, dan kegagalan pada aplikasi ini.

## Persyaratan Server

Diperlukan PHP versi 8.1 atau yang lebih tinggi, dengan ekstensi berikut terpasang:

- json
- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [fileinfo](https://php.net/manual/en/fileinfo.installation.php) untuk mengekspor file excel dengan gambar yang dihasilkan dari [PHPSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) untuk menghubungkan basis data MySQL.

> [!WARNING]
>
> - Tanggal akhir masa pakai PHP 7.4 adalah 28 November 2022.
> - Tanggal akhir masa pakai PHP 8.0 adalah 26 November 2023.
> - Jika Anda masih menggunakan PHP 7.4 atau 8.0, Anda harus segera memperbaruinya.
> - Tanggal akhir masa pakai PHP 8.1 adalah 31 Desember 2025.

## Informasi Hukum

Aplikasi ini digunakan untuk:

> [**Klinik Mata Utama Padang Eye Center Teluk Kuantan**](https://maps.app.goo.gl/3HLW83B9RjRsH6GY6)
>
> Jl. Rusdi S. Abrus No. 35, LK III Sinambek, Sungai Jering, Kuantan Tengah, Kuantan Singingi, Riau, Indonesia

© 2025 Klinik Mata Utama Padang Eye Center Teluk Kuantan

Kode sumber aplikasi ini dilisensikan di bawah Lisensi MIT

# Management Information System of Main Eye Clinic of Padang Eye Center Teluk Kuantan

**Management Information System of Main Eye Clinic of Padang Eye Center Teluk Kuantan Application** is a system used to fulfill business processes at the Main Eye Clinic Padang Eye Center Teluk Kuantan. This system manages patients, outpatients, electronic medical records (under development), medicine stock, medicine prescriptions, and transactions. This application is based on [HWPweb Admin Template](https://github.com/hafizulwanandaputra/hwpweb-admin-template) which is also based on [CodeIgniter 4](https://github.com/codeigniter4/appstarter).

## Installation

1. Clone this repostiory.
2. Navigate into cloned repository folder.
3. Open terminal app and run `composer install --no-dev` and `npm install`.

## Setup

1. Copy `.env.example` to `.env` and tailor for this app, specifically the `requestURL` and the database settings.
2. Create MySQL database that matches with database name specified in `.env` file.
3. Use the `pectk-simklinik-no-data.sql.gz` file in this repository to import the tables into the database.
4. Run `php spark db:seed User` and `php spark db:seed PwdTransaksi` to seed the database items.
5. For use with PHP development server, run `php spark serve` to start the server. Usually [http://localhost:8080](http://localhost:8080). You can use different port by using `php spark serve --port 8081`. Replace `8081` with the desired port number. You need to modify `requestURL` in `.env` to match with the desired port number.
6. For use without PHP development server such as Apache or Nginx, just open it from URL like [http://localhost/pectk-farmasi](http://localhost/pectk-farmasi) or others based on your web server's configuration. You need to modify `requestURL` in `.env` to match with the desired URL address.
   > The base URL is based on PHP's `$_SERVER['SERVER_NAME']` value. You just need to change the `requestURL` which consists of the port and the subfolder (if the app is stored in a subfolder).
7. To run a websocket, follow the instructions in the websocket section.
8. Sign in using username `admin` and password `12345`. You need to change the password from `{your_base_url}/settings/changepassword` and we recommend using a strong password for better security.

### Websocket

The websocket URL configuration is located in `.env` with the values ​​`WS-URL-JS` for the frontend (example: `ws://localhost:8090`) and `WS-URL-PHP` for the backend (example: `http://localhost:3000/notify`).

If you are running on a virtual private server (VPS) with SSL, use `wss://domainaddress.com/ws/` for `WS-URL-JS` and `https://domainaddress.com/notify/` for `WS-URL-PHP`. Make sure you create a virtual host or proxy server that matches the web server you are using.

Example for nginx:

```
   location /ws/ {
      proxy_pass http://127.0.0.1:8090;
      proxy_http_version 1.1;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "upgrade";
      proxy_set_header Host $host;
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
      proxy_cache_bypass $http_upgrade;
      proxy_redirect off;
      proxy_read_timeout 3600;
   }

   location /notify/ {
      proxy_pass http://127.0.0.1:3000/notify;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_set_header Host $host;
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   }
```

To make websocket work properly:

1. Allow the port to use websocket with the following command (Linux):
   ```
   sudo ufw allow 8090/tcp
   sudo ufw allow 3000/tcp
   ```
2. Install `pm2` with the following command:
   ```
   sudo npm insall -g pm2
   ```
3. Then, run the websocket with the following command:
   ```
   sudo pm2 start websocket.js --name websocket
   sudo pm2 save
   sudo pm2 startup
   ```

### Puppeteer for PDF export

Export PDF documents on this system using [Puppeteer](https://github.com/puppeteer/puppeteer) which is a JavaScript library for Node.js. Make sure your server has Node.js. If you have any issues with Puppeteer, [please visit this page](https://pptr.dev/troubleshooting).

## Progressive Web App (PWA) Setup

The `manifest.json` file contains the application configuration for the PWA located in the `public` folder.

PWA contents:

- In `app/Views/auth/templates/login.php` and `app/Views/dashboard/templates/dashboard.php`, there's is a `<link rel="manifest" href="<?= base_url(); ?>/manifest.json">` tag to initiate `manifest.json`.

- If the PWA located in subfolder, add the subfolder in the `start_url` and `src` values in `manifest.json`.

To set up PWA application:

1. Check or modify PWA configuration above based on your needs.
2. Run `php spark serve` or `php spark serve --port 8081`. Replace `8081` with the desired port number.
3. Open the browser's development tools to check manifest information.
4. If the configuration meets the PWA requirement, you can install the PWA. You can launch it from applications menu or list. Don't forget to run `php spark serve` (or `php spark serve --port 8081` if you use different port) before launching an application.
5. If not using `localhost` served using or not using `php spark serve` such as your server's domain or IP address, you must use HTTPS.

> [!WARNING]
>
> You will need to reinstall the PWA if the port or URL is changed. Make sure the port or URL used for the PWA does not conflict with another project.

## User Roles

There are 4 user roles used on this application:

1. **Admin**: is used to manage everything of this application with exclusively access to manage users and its sessions and generating transaction password for transaction cancellation.
2. **Medical Records**: used to manage patients and register patients for outpatient care.
3. **Pharmacist**: is used to manage medicine stocks, external prescriptions, and printing e-ticket and prescription document from confirmed doctor prescriptions.
4. **Doctor**: is used to provide diagnoses, procedures, medicine prescriptions, and eyeglass prescriptions given to patients, as well as to create surgery room orders, outpatient reports, and surgical reports.
5. **Cashier**: is used to manage actions and making transaction of the actions and medicines.
6. **Nurse**: is used to provide assessment, screening, education, and supporting checks to patients.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the `public` folder, for better security and separation of components.

This means that you should configure your web server to "point" to your project's `public` folder, and not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter `public/...`, as the rest of your logic and the framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

Use GitHub Issues, in this main repository, to report any bugs, errors, and failures for this application.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- json
- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [fileinfo](https://php.net/manual/en/fileinfo.installation.php) for exportng excel files with images generated from [PHPSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) for connecting MySQL database.

> [!WARNING]
>
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

## Legal Information

This application is used for:

> [**Main Eye Clinic of Padang Eye Center Teluk Kuantan**](https://maps.app.goo.gl/3HLW83B9RjRsH6GY6)
>
> Jl. Rusdi S. Abrus No. 35, LK III Sinambek, Sungai Jering, Kuantan Tengah, Kuantan Singingi, Riau, Indonesia

© 2025 Main Eye Clinic of Padang Eye Center Teluk Kuantan

The source code of this application is licensed under MIT License
