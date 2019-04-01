# Production {docsify-ignore-all}

Before you can upload Purple CMS, it's recommended to do a clear cache. Run the following command in your terminal

```bash
bin/cake cache clear_all
```

Without clearing cache, Purple CMS will read the local URL routing, so your website will not running properly.

The recommended way to upload your project to production is follow the [Cakephp Documentation](https://book.cakephp.org/3.0/en/installation.html#production). But if your server is shared hosting or don't have root access, you can create archieve of all Purple CMS files and upload it to your server. 

For example, if your host is **example.com**, upload and extract the archieve file to domain folder of **example.com**