# Quick Start

It's recommended to install Purple CMS by cloning the Purple CMS repository on Github

```bash
git clone https://github.com/bayukurniawan30/purple-cms.git
```

Or you can download the zip file. Extract all the files to your server. If you want to install it locally, for example XAMPP, LAMPP, MAMP, or Laragon, create a folder inside htdocs folder (www for Laragon) and extract the zip file. Next, install dependencies by composer, you have to use composer, because composer will install all dependencies automatically. If you don't have a composer yet, download [here](https://getcomposer.org/)

```bash
composer install
```
If the installation process is successful, open the link below in your web browser if you are using Apache stack app like XAMPP, LAMPP, MAMP, or Laragon.

```http
http://<hostname>/setup
```

Or if you install Purple CMS in local server

```http
http://localhost/<folder-name>/setup
```

If you prefer using CLI, you can use the following command provided by CakePHP

```bash
bin/cake server
```

It will run the built-in server running in <code>http://localhost:8765</code>. Just open the link in your web browser.

<p class="tip">Substituting <code>&lt;hostname&gt;</code> with your web server’s host name, and <code>&lt;folder-name&gt;</code> is where you installed Purple CMS in local server.</p>