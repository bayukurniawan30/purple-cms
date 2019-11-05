# Purple CLI - Database

You can view the database info or migrate the database from CLI.

To view database info, use command below

```bash
bin/cake purple database decrypt -i
```

If your Purple CMS uses environment variables, use command below

```bash
bin/cake purple database env -i
```

To migrate the database, use command below

```bash
bin/cake purple database migrate -m <database_name,database_user,database_password>
```

Replace <code>&#x3C;database_name,database_user,database_password&#x3E;</code> with your database information. Migrate database command is doing a same function like [Going Live in Production](production.md).