# Purple CMS

Content Management System Base on CakePHP 3

### Tentang Purple CMS
Purple CMS adalah sebuah Content Management System yang dibuat dengan framework CakePHP 3. Tujuannya adalah untuk memudahkan developer dalam membuat suatu website, baik yang sederhana ataupun kompleks.

### Fitur
 - Easy Setup, setup Purple CMS hanya dalam 3 langkah.
 - Block Editor, sebuah live html editor yang bisa diedit secara live langsung dari CMS.
 - Visitors Statistics, menampilkan data pengunjung website dengan tampilan yang user friendly.
 - Themes, mudah diintegrasikan dengan template Bootstrap 4, dan bisa membuat custom block yang bisa digunakan di Block Editor!
 - Notification, mengirim notifikasi ke email jika ada pemberitahuan, walaupun diinstal di localhost (harus terkoneksi ke internet).
 - Customizable, bisa menambahkan fitur sesuai kebutuhan developer.

### Instalasi
Download zip dari repo ini atau clone
```sh
$ git clone https://github.com/bayukurniawan30/purple-cms.git
```
Setelah itu, instal dependency dengan composer, wajib menggunakan composer, karena composer akan menginstal semua dependency dengan otomatis. Jika belum memiliki composer, download di [sini](https://getcomposer.org/)
```sh
$ composer install
```
Jika proses instal berjalan dengan lancar, silahkan masuk ke halaman setup Purple CMS
```sh
http://localhost/folder-name/setup
```
Perhatikan, folder-name adalah folder tempat anda menginstal Purple CMS, sesuaikan dengan nama folder anda.

### Setup
Setup Purple CMS dalam 3 langkah :
 - Database, isikan nama database, user, dan password untuk koneksi ke database. Database harus dibuat terlebih dahulu.
 - Administrative, isikan Site Name, dan data anda untuk membuat user administrator.
 - Finishing Setup, selesaikan setup dengan menekan tombol Start Purple. Jika anda terhubung ke internet, anda akan menerima email data Sign In ke halaman Purple.


