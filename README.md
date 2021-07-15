![Purple CMS](webroot/master-assets/img/purple-logo-small.png?raw=true "Purple CMS")

# Purple CMS

Content Management System Base on CakePHP 3

[Indonesian version](README.id.md)
[Read Full Documentaion](https://bayukurniawan30.github.io/purple-cms/)

### Introducing Purple CMS API v1
This API is used for external application like mobile app. Not all action in Purple CMS web based available in this API, some action can only be done from web based.

[API Documentation](https://documenter.getpostman.com/view/13404470/Tzm8FFSv)

### Tentang Purple CMS
Purple CMS is a Content Management System built with the CakePHP 3 framework. The goal is to make it easier for developers to create a website, either simple or complex.

### Fitur
 - ***Easy Setup***, setup Purple CMS only in 3 steps.
 - ***Block Editor***, a live html editor that can be edited live directly from the CMS.
 - ***Visitors Statistics***, displaying website visitor data in a user-friendly display.
 - ***Themes***, easy to integrate with Bootstrap 4 templates, and can create custom blocks that can be used in the Block Editor!
 - ***Notification***, send notification to email if there is notification, even if installed on localhost (must be connected to internet).
 - ***Customizable***, can add features according to developer needs.

### Instalasi
Download zip from this repo or clone
```sh
$ git clone https://github.com/bayukurniawan30/purple-cms.git
```
After that, install dependencies with composer, it is mandatory to use composer, because composer will install all dependencies automatically. If you don't have composer, download it [here](https://getcomposer.org/)
```sh
$ composer install
```
If the installation process went smoothly, please enter the Purple CMS setup page
```sh
http://localhost/folder-name/setup
```
Note, folder-name is the folder where you installed Purple CMS, adjust it to your folder name.

### Setup
Setup Purple CMS in 3 steps :
 - ***Database***, fill in the database name, user, and password to connect to the database. Database must be created first, with collation utf8mb4_general_ci.
 - ***Administrative***, fill in the Site Name, and your data to create an administrator user.
 - ***Finishing Setup***, complete the setup by pressing the Start Purple button. If you are connected to the internet, you will receive an email with Sign In data to the Purple page.

### Sign In to Purple
To enter the Purple CMS administrator page, please open the page :
```sh
http://localhost/folder-name/purple
```
Use the username and password you created in the administrative setup section to sign in.

### Deploy to Production
To move Purple CMS to production or to the server, you can use the installation process written above, or by creating a .zip archive of Purple CMS installed on localhost, then uploading it to the production server.
Things to make sure after moving Purple CMS to production server are :
 - Change the debug mode to ***false*** on file ***config/app.php***
 - Clear cache with terminal/cmd, go to Purple CMS installation folder, type ***bin/cake cache clear_all***


### Credits
 - [***CakePHP 3***](https://cakephp.org/) - PHP framework
 - [***Purple Admin Template***](https://github.com/BootstrapDash/PurpleAdmin-Free-Admin-Template) - Responsive admin template built with Bootstrap 4
 - [***Froala Design Blocks and 
WYSIWYG Editor***](https://www.froala.com/) - WYSIWYG HTML Editor and ready to use HTML blocks
 - [***Bootstrap 4***](https://getbootstrap.com/) - The most popular CSS Framework for developing responsive and mobile-first websites.
 - [***UI Kit 3***](https://getuikit.com/) - A lightweight and modular front-end framework for developing fast and powerful web interfaces.



