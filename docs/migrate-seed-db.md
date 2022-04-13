# Migrate and Seed Database

Purple has migration and seed files. If you prefer using CLI and want to skip the Setup section, you can migrate and seed the database manually by using the following commands.

```bash
bin/cake migrations migrate
```

Then, seed the database.

```bash
bin/cake migrations seed
```

<p class="tip">Make sure to change the Admin password or delete the default account and make a new one before moving to production</p>