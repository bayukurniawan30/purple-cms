# Production {docsify-ignore-all}

Before you can upload Purple CMS, it's recommended to do a clear cache. Run the following command in your terminal

```bash
bin/cake cache clear_all
```

Without clearing cache, Purple CMS will read the local URL routing, so your website will not running properly.

The recommended way to upload your project to production is follow the [Cakephp Documentation](https://book.cakephp.org/3.0/en/installation.html#production). But if your server is shared hosting or don't have root access, you can create archieve of all Purple CMS files and upload it to your server. 

For example, if your host is **example.com**, upload and extract the archieve file to domain folder of **example.com**

#### Uploading Database

Database information (database name, user, and password) in Purple CMS is encrypted by [Dcrypt](https://github.com/mmeyer2k/dcrypt/tree/4.0.2). It's located in <code>config/database.php</code>. To upload your database, Follow these steps below : 
 - Export your database from local server.
 - Upload your project to hosting.
 - Replace content in <code>config/database.php</code> to "default" (without double quote).
 - Create database in your hosting.
 - Run [Purple CMS Setup](/).
 - Delete all table inside your new database.
 - Import exported database from local server to new database.

Now your website is running like in the local server.  