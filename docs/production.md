# Production {docsify-ignore-all}

Before you can upload Purple CMS, it's recommended to do a clear cache. Run the following command in your terminal

```sh
bin/cake cache clear_all
```

Without clearing cache, Purple CMS will read the local URL routing, so your website will not running properly.

Then, to make your website running on production server, create archieve of all Purple CMS files, upload and extract to your production server.

For example, if your host is **example.com**, upload and extract the archieve file to root folder of **example.com**