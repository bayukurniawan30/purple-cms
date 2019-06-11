# Production {docsify-ignore-all}

Before you can upload Purple CMS, it's recommended to do a clear cache. Run the following command in your terminal

```bash
bin/cake cache clear_all
```

Without clearing cache, Purple CMS will read the local URL routing, so your website will not running properly.

The recommended way to upload your project to production is follow the [Cakephp Documentation](https://book.cakephp.org/3.0/en/installation.html#production). But if your server is shared hosting or don't have root access, you can create archieve of all Purple CMS files and upload it to your server. 

For example, if your host is **example.com**, upload and extract the archieve file to domain folder of **example.com**

#### Going Live

If you installed Purple CMS in local server or localhost, you have to migrate to live server with 3 steps. After all of your website files uploaded to live server, follow the steps below to make your website live.

 - Open <code>http://&#x3C;hostname&#x3E;/production-site/user-verification</code>. Insert your email and production key. Production key is a unique key to migrate your database. You can find the key in the Purple CMS Settings Page. If the key is correct, Purple CMS will send an email with verification code.
 - Insert the verification code to verify you provide a real email.
 - Insert the new database information in live server, and you are done!

Now your website is running live.  