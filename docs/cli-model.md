# Purple CLI - Model

Some models in Purple CMS can be viewed from CLI. Available models are **Admins, Blogs, BlogCategories, and Histories**

To view model data, use command below

```bash
bin/cake purple model <model> -d <query|table>
```

Replace <code>&#x3C;model&#x3E;</code> with available models.

<code>-d</code> flag means display, the value is query or table. Query will return query object, table will return data in table format.