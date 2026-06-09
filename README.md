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

### Websocket dan Puppeteer Cluster

Konfigurasi URL websocket dan Puppeteer Cluster terletak pada `.env` dengan nilai `WS-URL-JS` untuk _frontend_ (contoh: `ws://localhost:8090`), `WS-URL-PHP` untuk _backend_ (contoh: `http://localhost:3000/notify`), dan `PDF-URL` untuk Puppeteer Cluster (contoh: `http://localhost:3001/generate-pdf`).

Jika Anda menjalankannya di peladen pribadi virtual (VPS) dengan SSL, gunakan `wss://alamatdomain.com/ws/` pada `WS-URL-JS`, `https://alamatdomain.com/notify/` pada `WS-URL-PHP`, dan `https://alamatdomain.com/generate-pdf/` pada `PDF-URL`. Pastikan Anda membuat hos virtual atau peladen proksi sesuai dengan peladen web yang Anda gunakan.

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

location /generate-pdf/ {
   proxy_pass http://127.0.0.1:3001/generate-pdf;
   proxy_http_version 1.1;
   proxy_set_header Connection "";
   proxy_set_header Host $host;
   proxy_set_header X-Real-IP $remote_addr;
   proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
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

Agar websocket dan Puppeteer Cluster berjalan dengan baik (Linux sebagai root):

1. Izinkan port agar bisa menggunakan websocket dan Puppeteer Cluster dengan perintah sebagai berikut:
   ```
   ufw allow 8090/tcp
   ufw allow 3000/tcp
   ufw allow 3001/tcp
   ```
2. Instal `pm2` dengan perintah sebagai berikut:
   ```
   npm insall -g pm2
   ```
3. Lalu, jalankan websocket dan Puppeteer Cluster dan aktifkan pemulaian otomatis saat memulai ulang peladan dengan perintah sebagai berikut:
   ```
   pm2 start /path/to/websocket.js --name ws-pectk-simklinik
   pm2 start /path/to/puppeteer-pdf.js --name pdf-pectk-simklinik
   pm2 save
   pm2 startup
   ```
   Setelah menjalankan `pm2 startup`, ikuti instruksi yang ada pada keluaran.

### Puppeteer Cluster untuk ekspor PDF

Ekspor dokumen-dokumen PDF pada sistem ini menggunakan [Puppeteer Cluster](https://github.com/thomasdondorf/puppeteer-cluster) yang merupakan pustaka JavaScript untuk Node.js yang dapat dijalan secara konkruen. Pastikan peladen Anda sudah memiliki Node.js dan peramban web Chromium atau Google Chrome.

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

1. **Admin**: digunakan untuk mengelola semua hal dalam aplikasi ini dengan akses eksklusif untuk mengelola pengguna dan sesinya serta membuat kata sandi transaksi untuk pembatalan transaksi. Admin yang bukan pemilik tidak dapat mengubah, mengaktifkan, menonaktifkan, dan menghapus akun Admin yang berstatus pemilik.
2. **Admisi**: digunakan untuk mengelola pasien dan mendaftarkan pasien untuk rawat jalan.
3. **Apoteker**: digunakan untuk mengelola stok obat, resep eksternal, dan mencetak e-tiket serta dokumen resep dari resep dokter yang telah dikonfirmasi.
4. **Dokter**: digunakan untuk memberikan diagnosis, tindakan, resep obat, dan resep kacamata yang diberikan kepada pasien, serta membuat surat perintah kamar operasi, laporan rawat jalan, dan laporan-laporan operasi.
5. **Kasir**: digunakan untuk mengelola tindakan dan melakukan transaksi atas tindakan dan obat-obatan.
6. **Manajer**: fungsinya sama seperti Admin, tetapi tidak dapat memonitor sesi yang sedang aktif.
7. **Monitor Antrean**: digunakan untuk memonitor antrean pendaftaran.
8. **Monitor Antrean Poliklinik**: digunakan untuk memonitor antrean poliklinik.
9. **Perawat**: digunakan untuk memberikan asesmen, skrining, edukasi, dan pemeriksaan penunjang kepada pasien.
10. **Satpam**: digunakan untuk mengoperasikan antrean pendaftaran.

## Perubahan Penting dengan index.php

`index.php` tidak lagi berada di root proyek! File tersebut telah dipindahkan ke dalam folder `public`, demi keamanan dan pemisahan komponen yang lebih baik.

Ini berarti Anda harus mengonfigurasi server web Anda untuk "mengarah" ke folder `public` proyek Anda, dan bukan ke root proyek. Praktik yang lebih baik adalah mengonfigurasi host virtual untuk mengarah ke sana. Praktik yang buruk adalah mengarahkan server web Anda ke root proyek dan berharap untuk memasukkan `public/...`, karena logika dan kerangka kerja Anda yang lain akan terekspos.

**Harap** baca panduan pengguna untuk penjelasan yang lebih baik tentang cara kerja CI4!

## Manajemen Repositori

Gunakan _GitHub Issues_, di repositori utama ini, untuk melaporkan bug, kesalahan, dan kegagalan pada aplikasi ini.

## Persyaratan Server

### PHP

Diperlukan PHP versi 8.2 atau yang lebih tinggi, dengan ekstensi berikut terpasang:

- json
- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [fileinfo](https://php.net/manual/en/fileinfo.installation.php) dan [zip](https://php.net/manual/en/zip.installation.php) untuk mengekspor file excel dengan gambar yang dihasilkan dari [PHPSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) untuk menghubungkan basis data MySQL.

> [!WARNING]
>
> - Tanggal akhir masa pakai PHP 8.2 adalah 31 Desember 2026.
> - Tanggal akhir masa pakai PHP 8.3 adalah 31 Desember 2027.
> - Tanggal akhir masa pakai PHP 8.4 adalah 31 Desember 2028.

### Node.js

Node.js versi 22 LTS atau yang lebih tinggi diperlukan.

> [!WARNING]
>
> - Tanggal akhir masa pakai Node.js 22 LTS adalah 30 April 2027.
> - Tanggal akhir masa pakai Node.js 24 LTS adalah 30 April 2028.
> - Tanggal akhir masa pakai Node.js 26 LTS adalah April 2029.
> - Node.js versi LTS direkomendasikan.

## Informasi Hukum

Aplikasi ini digunakan untuk:

> [**Klinik Mata Utama Padang Eye Center Teluk Kuantan**](https://maps.app.goo.gl/3HLW83B9RjRsH6GY6)
>
> Jl. Rusdi S. Abrus No. 35, LK III Sinambek, Sungai Jering, Kuantan Tengah, Kuantan Singingi, Riau, Indonesia

© 2025 Klinik Mata Utama Padang Eye Center Teluk Kuantan

Kode sumber aplikasi ini dilisensikan di bawah Lisensi MIT
