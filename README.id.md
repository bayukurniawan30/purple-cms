![Purple CMS](webroot/master-assets/img/purple-logo-small.png?raw=true "Purple CMS")

# Purple CMS

Content Management System Base on CakePHP 3

[English version](README.md)

[Read Full Documentaion](https://bayukurniawan30.github.io/purple-cms/)

### Memperkenalkan Purple CMS API v1
API ini digunakan untuk aplikasi eksternal seperti aplikasi seluler. Tidak semua aksi di Purple CMS berbasis web tersedia di API ini, beberapa aksi hanya dapat dilakukan dari web.

[API Documentation](https://documenter.getpostman.com/view/13404470/Tzm8FFSv)

### Tentang Purple CMS
Purple CMS adalah sebuah Content Management System yang dibuat dengan framework CakePHP 3. Tujuannya adalah untuk memudahkan developer dalam membuat suatu website, baik yang sederhana ataupun kompleks.

### Fitur Baru
Purple CMS sekarang bisa menjadi Headless CMS untuk framework front-end seperti Nextjs. Buat dan atur konten di Purple CMS, tarik datanya ke framework front-end melalui Purple CMS API.

[Demo dengan Nextjs](https://github.com/bayukurniawan30/nextjs-purple-cms)

### Fitur
 - ***Easy Setup***, setup Purple CMS hanya dalam 3 langkah.
 - ***Block Editor***, sebuah live html editor yang bisa diedit secara live langsung dari CMS.
 - ***Visitors Statistics***, menampilkan data pengunjung website dengan tampilan yang user friendly.
 - ***Themes***, mudah diintegrasikan dengan template Bootstrap 4, dan bisa membuat custom block yang bisa digunakan di Block Editor!
 - ***Notification***, mengirim notifikasi ke email jika ada pemberitahuan, walaupun diinstal di localhost (harus terkoneksi ke internet).
 - ***Customizable***, bisa menambahkan fitur sesuai kebutuhan developer.

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
 - ***Database***, isikan nama database, user, dan password untuk koneksi ke database. Database harus dibuat terlebih dahulu, dengan collation utf8mb4_general_ci.
 - ***Administrative***, isikan Site Name, dan data anda untuk membuat user administrator.
 - ***Finishing Setup***, selesaikan setup dengan menekan tombol Start Purple. Jika anda terhubung ke internet, anda akan menerima email data Sign In ke halaman Purple.

### Masuk ke Purple
Untuk masuk ke halaman administrator Purple CMS, silahkan buka halaman :
```sh
http://localhost/folder-name/purple
```
Gunakan username dan password yang anda buat pada setup bagian administrative untuk sign in.

### Deploy ke Production
Untuk memindahkan Purple CMS ke production atau ke server, bisa dengan proses instalasi yang tertulis di atas, atau dengan membuat archive .zip dari Purple CMS yang terinstal di localhost, kemudian diupload ke server production.
Hal-hal yang harus dipastikan setelah memindahkan Purple CMS ke server production adalah :
 - Ubah debug mode menjadi ***false*** pada file ***config/app.php***
 - Clear cache dengan terminal/cmd, masuk ke folder instalasi Purple CMS, ketikkan ***bin/cake cache clear_all***


### Credits
 - [***CakePHP 3***](https://cakephp.org/) - PHP framework
 - [***Purple Admin Template***](https://github.com/BootstrapDash/PurpleAdmin-Free-Admin-Template) - Responsive admin template built with Bootstrap 4
 - [***Froala Design Blocks and 
WYSIWYG Editor***](https://www.froala.com/) - WYSIWYG HTML Editor and ready to use HTML blocks
 - [***Bootstrap 4***](https://getbootstrap.com/) - The most popular CSS Framework for developing responsive and mobile-first websites.
 - [***UI Kit 3***](https://getuikit.com/) - A lightweight and modular front-end framework for developing fast and powerful web interfaces.



