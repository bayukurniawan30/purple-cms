# Folder Permission {docsify-ignore-all}

Purple CMS is based on CakePHP and uses the <code>tmp</code> directory for a number of different operations. Model descriptions, cached views, and session information are a few examples. The <code>logs</code> directory is used to write log files by the default FileLog engine.

Make sure the directories logs, tmp and all its subdirectories are writable by the web server user.

Run the following command in your terminal if you're running on Linux


```bash
sudo chmod -R 777 tmp; sudo chmod -R 777 logs
```

Do the same thing for folder <code>plugins/EngageTheme</code> and <code>webroot/uploads</code>.

<p class="tip">If you're running on Windows, you don't need to change the folder permission</p>