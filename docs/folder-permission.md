# Folder Permission {docsify-ignore-all}

Purple CMS is based on CakePHP and uses the **tmp** directory for a number of different operations. Model descriptions, cached views, and session information are a few examples. The **logs** directory is used to write log files by the default FileLog engine.

Make sure the directories logs, tmp and all its subdirectories are writable by the web server user.

Run the following command in your terminal if you're running on Linux


```sh
sudo chmod -R 777 tmp; sudo chmod -R 777 logs
```

Do the same thing for folder **plugins/EngageTheme** and **webroot/uploads**.

<p class="tip">If you're running on Windows, you don't need to change the folder permission</p>