# Creating Theme

Creating theme for Purple CMS is easy. A theme require [Bootstrap 4](https://getbootstrap.com/), [UIkit 3](https://getuikit.com/) and [Froala Design Blocks](https://www.froala.com/design-blocks). Why need these 3 libraries? Purple CMS Block Editor uses these libraries to build Block Editor in HTML format. Combination from these libraries makes your website awesome!

Don't need to manually download the libraries. Just include the libraries from <code>webroot/master-assets</code> folder.

## Theme Naming Convention

You can name your theme whatever, maybe Creative, Spectacular, or Light Theme. But your theme folder must followed by **Theme**. Folder name is **CamelCase** For example, your theme name is **Creative**, your folder name is **CreativeTheme**.  

## Theme Structure

Purple CMS uses CakePHP template format (.ctp).

```tree
YourTheme
│───src
│    │───Plugin.php
│    │───Controller
│    │    └───AppController.php
│    │───Function
│    │    └───ThemeFunction.php
│    └───Template
│         │───Blogs
│         │    │───ajax_send_comment.ctp
│         │    │───archives.ctp
│         │    │───detail.ctp
│         │    │───posts_in_category.ctp
│         │    └───tag.ctp
│         │───Cell
│         │    │───Categories
│         │    │    └───total_post.ctp
│         │    └───Comments
│         │         └───fetch_replies.ctp
│         │───Element
│         │    │───Meta
│         │    │    │───open_graph.ctp
│         │    │    └───twitter.ctp
│         │    │───Post
│         │    │    │───Comment
│         │    │    │    │───comment_form.ctp
│         │    │    │    └───comments.ctp
│         │    │    │───Sidebar
│         │    │    │    │───about.ctp
│         │    │    │    │───archives.ctp
│         │    │    │    │───category.ctp
│         │    │    │    │───search.ctp
│         │    │    │    └───tags.ctp
│         │    │    └───tags.ctp
│         │    │───breadcrumb.ctp
│         │    │───footer.ctp
│         │    │───head_title.ctp
│         │    │───head.ctp
│         │    │───navigation.ctp
│         │    └───script.ctp
│         │───Home
│         │    └───index.ctp
│         │───Layout
│         │    └───default.ctp
│         │───Pages
│         │    │───ajax_send_contact.ctp
│         │    │───ajax_verify_form.ctp
│         │    │───blog.ctp
│         │    │───code.ctp
│         │    │───general.ctp
│         │    │───home.ctp
│         │    └───index.ctp
│         └───Search
│              └───index.ctp
│───webroot
│    │───blocks
│    │───css
│    │───fonts
│    │───img
│    └───js
└───detail.json
```

To get started, you can copy the default theme, and modify it for your need. After you create your theme, place it in <code><YourWebsite>/webroot/uploads/themes/</code>. Your theme will appear in Theme Page in Purple Admin.

Theme information is placed in <code>YourTheme/detail.json</code>.

```json
{
	"name" : "Theme Name",
	"author" : "Theme Author",
	"image" : "",
	"preview" : "",
	"homepage" : 
		{
			"use" : "",
			"function" : ""
		}
	,
	"blocks" : "",
	"description" : "Theme description",
	"version" : "1.0"
}
``` 
 - **name** : Theme name.
 - **author** : Theme author.
 - **image** : Theme image that shown in Themes page. Image file is located in <code>YourTheme/webroot/img</code>. Default is empty.
 - **preview** : Theme preview (images) that shown when clicking theme image. Use <code>,</code> for multiple images. For example, <code>preview-1.jpg,preview-2.jpg,preview-3.jpg</code>. Images file are located in <code>YourTheme/webroot/img/preview</code>. Default is empty.
 - **homepage - use** : Set theme homepage. Default value is <code>default</code>. Other option is <code>theme</code>. Default option use default Purple CMS homepage function that made in Block Editor. Theme option use custom function in your theme to generate custom homepage. To create custom homepage function, create a public function in <code>YourTheme/src/Functions/ThemeFunction.php</code>, and you can call your function by make it a variable from function name. For example, the function name is <code>customHome</code>, so you call the function <code>$customHome</code>. 
 - **homepage - function** : Set with function name for theme homepage. Fill blank if *homepage - use* is default. See [Custom Function](/creating-theme?id=custom-function) for more details. 
 - **blocks** : Fill with custom blocks name if you have custom blocks for your theme. You can fill this blank, Purple CMS is automatically fetch your custom blocks in <code>YourTheme/webroot/blocks</code>. See [Creating Blocks](/creating-theme?id=creating-blocks) for more details.
 - **description** : Theme description.
 - **version** : Theme version.

## Including Libraries

To include [Bootstrap 4](https://getbootstrap.com/), [UIkit 3](https://getuikit.com/) and [Froala Design Blocks](https://www.froala.com/design-blocks), you can add the script below in the <code>head.ctp</code> and <code>script.ctp</code> file.

**YourTheme/src/Template/Element/head.ctp**
```php
<!-- Bootstrap -->
<!-- Use your own Bootstrap file. Require version 4.x.x -->
<?= $this->Html->css('bootstrap.min.css') ?>
<!-- Froala Blocks -->
<?= $this->Html->css('/master-assets/plugins/froala-blocks/css/froala_blocks.css') ?>
<!-- UI Kit -->
<?= $this->Html->css('/master-assets/plugins/uikit/css/uikit.css') ?>
```

**YourTheme/src/Template/Element/script.ctp**
```php
<!-- Bootstrap -->
<!-- Use your own Bootstrap file. Require version 4.x.x -->
<?= $this->Html->script('bootstrap.min.js'); ?>
<!-- UI Kit -->
<?= $this->Html->script('/master-assets/plugins/uikit/js/uikit.js'); ?>
<?= $this->Html->script('/master-assets/plugins/uikit/js/uikit-icons.js'); ?>
```

You have to include Purple CMS javascript in the last included script.

```php
<?= $this->Html->script('/master-assets/js/ajax-front-end.js'); ?>
<?= $this->Html->script('/master-assets/js/purple-front-end.js'); ?>
```

## Creating Blocks

Block is a part in Block Editor. You can create your custom blocks for your theme. Custom blocks are located in <code>YourTheme/webroot/blocks</code>, and the extension of the file is json.

To create a block, create a json file with lowercase name. For example <code>myblock.json</code>. Below is the format of the json file.

```json
{
	"options" : [
		{
			"theme" : "Theme name",
			"name" : "Block name",
			"category" : "", 
			"editable" : "",
			"title"	 : "",
			"content" : "",
			"block" : "<div class='purple-theme-block-preview uk-card uk-card-default uk-card-body uk-text-center bg-primary text-white'>Theme Block - Creative<br><small>Contact Form<br>Visit your website to view the content.</small></div>",
			"html" : ""
		}
	]
}
```
 - **theme** : Theme name.
 - **name** : Block name.
 - **category** : Just fill blank, reserved for future update.
 - **editable** : Just fill blank, reserved for future update.
 - **title** : Just fill blank, reserved for future update.
 - **content** : Just fill blank, reserved for future update.
 - **block** : HTML that will shown in block editor if your block contain custom functions.
 - **html** : HTML that will be generated in block editor. Must be wrapped in <code>&#x3C;section id=&#x27;fdb-{bind.id}&#x27; class=&#x27;fdb-block purple-theme-block-section remove-padding-in-real&#x27; data-fdb-id=&#x27;{bind.id}&#x27;&#x3E;&#x3C;div class=&#x27;purple-theme-block&#x27;&#x3E;Put your HTML here&#x3C;/div&#x3E;&#x3C;/section&#x3E;</code>.

 Add <code>{theme.webroot}</code> to element that you want to include theme webroot url. For example, <code>&#x3C;img src="{theme.webroot}img/my-image.jpg"&#x3E;</code>. Purple will replace <code>{theme.webroot}</code> in your image tag with theme webroot url.

 If you want to add custom functions in block, add <code>{{function|functionName}}</code> in your HTML. Replace <code>functionName</code> with function name that you created in **ThemeFunction.php**. For now, it doesn't support parameters.

Blocks have some class that can be added to the HTML tag. 
 - **fdb-editor** : class to make content editable in Block Editor. Can be added in text, image, link, button, or add it at parent tag to make whole childs editable.
<<<<<<< HEAD
<<<<<<< HEAD
 - **fdb-block-copy** : class to make element copy-able. This class add Copy Button to element. Remember to add <code>data-fdb-id</code> attribute with <code>{bind.id}</code> value to targeted element.
=======
>>>>>>> parent of 9370574... Update Creating Theme Doc
=======
 - **fdb-block-copy** : class to make element copy-able. This class add Copy Button to element. Remember to add *data-fdb-id* attribute with *{bind.id}* value to targeted element.
>>>>>>> master
 - **non-uikit** : if your element classes are conflict with UIkit stylesheet, add this class to the element. 
 - **remove-padding-in-real** : class to make element has no padding in front-end website.
 - **bttn-to-customize** : special class to edit button or a tag.
 - **fdb-font-awesome** : class that only can be added in font awesome element. Please add <code>data-purple-fa-icon</code> also with icon name as the value. For example, <code>&lt;i class=&quot;fa fa-twitter fdb-font-awesome&quot; data-purple-fa-icon=&quot;twitter&quot;&gt;&lt;/i&gt;</code>

## Custom Function

Custom function is located in <code>YourTheme/src/Functions/ThemeFunction.php</code>. To create a function, create a new public function (without parameters). For example :

```php
<?php

// ThemeFunction.php

...

class ThemeFunction
{
	public function myFunction()
	{
		...
	}
}

```

To call your function in theme block, use <code>{{function|myFunction}}</code> in the HTML code.
To call your function as homepage, put the function name in <code>YourTheme/detail.json</code>.
To call your function in a page, call <code>$themeFunction->myFunction()</code> in your page.

<p class="tip">
	All function in <strong>ThemeFunction.php</strong> support parameters except functions that will be used in theme blocks and as homepage.  
</p>