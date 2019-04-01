# API

## Model

### Entity

Entities represent individual rows or domain objects in Purple CMS. Entities contain methods to manipulate and access the data they contain. Fields can also be accessed as properties on the object.

<p class="tip">You don’t need to create entity classes to get started with the ORM.</p>

#### Admin

**<code>_setPassword()</code>** <code>protected</code>
```php
_setPassword($password)
```
**Parameters**

<code>$password</code> *string*

Password that will be encrypted.

**Returns**

Encrypted password *string*

**<code>_getDisplayName()</code>** <code>protected</code>
```php
_getDisplayName($displayName)
```

**Parameters**

<code>$displayName</code> *string*

User display name that will converted to upper case.

**Returns**

Upper cased user display name *string*

#### Blog

**<code>_getTitle()</code>** <code>protected</code>
```php
_getTitle($title)
```

**Parameters**

<code>$title</code> *string*

Post title

**Returns**

Decoded HTML entity of post title *string*

**<code>_getContent()</code>** <code>protected</code>
```php
_getContent($content)
```

**Parameters**

<code>$content</code> *string*

Post content

**Returns**

Decoded HTML entity of post content *string*

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the post in readable format (Draft or Publish) *string*

#### Comment

**<code>_getContent()</code>** <code>protected</code>
```php
_getContent($content)
```

**Parameters**

<code>$content</code> *string*

Comment message

**Returns**

Decoded HTML entity of comment message *string*

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the comment in readable format (Unpublish or Publish) *string*

**<code>_getRead()</code>** <code>protected</code>
```php
_getRead()
```

**Returns**

Read Status of the comment in readable format (Unread or Read) *string*

#### Menu

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the navigation menu in readable format (Draft or Publish) *string*

#### Message

**<code>_getRead()</code>** <code>protected</code>
```php
_getRead()
```

**Returns**

Read Status of the message in readable format (Unread or Read) *string*

#### Notification

**<code>_getRead()</code>** <code>protected</code>
```php
_getRead()
```

**Returns**

Read Status of the notification in readable format (Unread or Read) *string*

#### Page

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the page in readable format (Draft or Publish) *string*

#### Submenu

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the navigation submenu in readable format (Draft or Publish) *string*

### Table

Table objects provide access to the collection of entities stored in a specific table

#### Admins

**<code>initialize()</code>** <code>public</code>
```php
initialize(array $config)
```

**Parameters**

<code>$config</code> *array*

Configuration options passed to the constructor.

**<code>beforeSave()</code>** <code>public</code> <code>callbacks/events</code>
```php
beforeSave($event, $entity, $options)
```

**<code>lastUser()</code>** <code>public</code>
```php
lastUser()
```

**Returns**

Object data of last created user *object*

#### BlogCategories

**<code>initialize()</code>** <code>public</code>
```php
initialize(array $config)
```

**Parameters**

<code>$config</code> *array*

Configuration options passed to the constructor.

**<code>beforeSave()</code>** <code>public</code> <code>callbacks/events</code>
```php
beforeSave($event, $entity, $options)
```

#### Blogs

**<code>initialize()</code>** <code>public</code>
```php
initialize(array $config)
```

**Parameters**

<code>$config</code> *array*

Configuration options passed to the constructor.

**<code>beforeSave()</code>** <code>public</code> <code>callbacks/events</code>
```php
beforeSave($event, $entity, $options)
```

**<code>findTagged()</code>** <code>public</code>
```php
findTagged(Query $query, array $options)
```

**Parameters**

<code>$query</code> *array*

The query object to apply the finder options to.

<code>$options</code> *array*

List of options to pass to the finder.

**Returns**

Query object *object*

**<code>taggedPosts()</code>** <code>public</code>
```php
taggedPosts($tagId)
```

**Parameters**

<code>$tagId</code> *integer*

Id of the post tag.

**Returns**

Query object *object*

**<code>archivesList()</code>** <code>public</code>
```php
archivesList($page = NULL)
```

**Parameters**

<code>$page</code> *integer* *null*

Id of the page.

**Returns**

Query object *object*

**<code>fetchPosts()</code>** <code>public</code>
```php
fetchPosts($limit, $return = 'fetch')
```

**Parameters**

<code>$limit</code> *integer* *null*

Total post to be shown.

<code>$return</code> *string*

There are two values for this parameter.
 - ***fetch*** Default value. Fetch the result of the query object.
 - ***count*** Count the result of the query object.

**Returns**
 - ***fetch*** will return query object *object*
 - ***count*** will return total of the query object *integer*

**<code>dashboardStatistic()</code>** <code>public</code>
```php
dashboardStatistic($status = 'all')
```

**Parameters**

<code>$status</code> *string*

There are three values for this parameter.
 - ***all*** Default value. Get all publish and draft posts.
 - ***publish*** Get publish posts.
 - ***draft*** Get draft posts.

**Returns**
Total posts *integer*

**<code>lastMonthTotalPosts()</code>** <code>public</code>
```php
lastMonthTotalPosts()
```

**Returns**
Total posts created in last month *integer*

#### BlogsTags

**<code>initialize()</code>** <code>public</code>
```php
initialize(array $config)
```

**Parameters**

<code>$config</code> *array*

Configuration options passed to the constructor.

**<code>checkExists()</code>** <code>public</code>
```php
checkExists($blogId, $tagId)
```

**Parameters**

<code>$blogId</code> *string*

Id post.

<code>$tagId</code> *string*

Id post tag.

**Returns**

True or false *boolean*

#### BlogTypes

**<code>initialize()</code>** <code>public</code>
```php
initialize(array $config)
```

**Parameters**

<code>$config</code> *array*

Configuration options passed to the constructor.

#### BlogVisitors

**<code>initialize()</code>** <code>public</code>
```php
initialize(array $config)
```

**Parameters**

<code>$config</code> *array*

Configuration options passed to the constructor.

**<code>beforeSave()</code>** <code>public</code> <code>callbacks/events</code>
```php
beforeSave($event, $entity, $options)
```

**<code>checkVisitor()</code>** <code>public</code>
```php
checkVisitor($ip, $created, $blog_id)
```


## View

### Helper

## Controller