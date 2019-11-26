# Purple CLI - Theme

To make creating theme simple, Purple CMS provide a command to create a new theme and it's files.

```bash
bin/cake purple theme create -t <name>
```

Replace <code>&#x3C;name&#x3E;</code> with your theme name. The theme name must include "**Theme**" at the end of theme name, for example WowTheme, NiceTheme.

Theme files will be created in <code>webroot/uploads/themes/&#x3C;name&#x3E;</code>.

You can create theme block also, use the command below to generate a theme block.

```bash
bin/cake purple theme create-block -b <name>
```

Replace <code>&#x3C;name&#x3E;</code> with your block name. The name is alphanumeric. Theme block will be generated in active theme blocks folder.