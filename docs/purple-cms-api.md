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

Encrypted password. *string*

**<code>_getDisplayName()</code>** <code>protected</code>
```php
_getDisplayName($displayName)
```

**Parameters**

<code>$displayName</code> *string*

User display name that will converted to upper case.

**Returns**

Upper cased user display name. *string*

#### Blog

**<code>_getTitle()</code>** <code>protected</code>
```php
_getTitle($title)
```

**Parameters**

<code>$title</code> *string*

Post title

**Returns**

Decoded HTML entity of post title. *string*

**<code>_getContent()</code>** <code>protected</code>
```php
_getContent($content)
```

**Parameters**

<code>$content</code> *string*

Post content

**Returns**

Decoded HTML entity of post content. *string*

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the post in readable format (Draft or Publish). *string*

#### Comment

**<code>_getContent()</code>** <code>protected</code>
```php
_getContent($content)
```

**Parameters**

<code>$content</code> *string*

Comment message

**Returns**

Decoded HTML entity of comment message. *string*

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the comment in readable format (Unpublish or Publish). *string*

**<code>_getRead()</code>** <code>protected</code>
```php
_getRead()
```

**Returns**

Read Status of the comment in readable format (Unread or Read). *string*

#### Menu

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the navigation menu in readable format (Draft or Publish). *string*

#### Message

**<code>_getRead()</code>** <code>protected</code>
```php
_getRead()
```

**Returns**

Read Status of the message in readable format (Unread or Read). *string*

#### Notification

**<code>_getRead()</code>** <code>protected</code>
```php
_getRead()
```

**Returns**

Read Status of the notification in readable format (Unread or Read). *string*

#### Page

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the page in readable format (Draft or Publish). *string*

#### Submenu

**<code>_getTextStatus()</code>** <code>protected</code>
```php
_getTextStatus()
```

**Returns**

Status of the navigation submenu in readable format (Draft or Publish). *string*

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

Object data of last created user. *object*

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

Query object. *object*

**<code>taggedPosts()</code>** <code>public</code>
```php
taggedPosts($tagId)
```

**Parameters**

<code>$tagId</code> *integer*

Id of the post tag.

**Returns**

Query object. *object*

**<code>archivesList()</code>** <code>public</code>
```php
archivesList($page = NULL)
```

**Parameters**

<code>$page</code> *integer* *null*

Id of the page.

**Returns**

Query object. *object*

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
 - ***fetch*** will return query object. *object*
 - ***count*** will return total of the query object. *integer*

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
Total posts created in last month. *integer*

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

True or false. *boolean*

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

**Parameters**

<code>$ip</code> *string*

Visitor IP address.

<code>$created</code> *datetime*

Date when checking visitor IP address <code>(Y-m-d H:i:s)</code>.

<code>$blog_id</code> *integer*

Id of the blog or post.

**Returns**

Total visitors with related IP, created time, and blog id. *integer*

**<code>totalVisitors()</code>** <code>public</code>
```php
totalVisitors($blog_id) 
```

**Parameters**

<code>$blog_id</code> *string*

Id of the blog or post.

**Returns**

Total visitors with blog id. *integer*

**<code>lastTwoWeeksVisitors()</code>** <code>public</code>
```php
lastTwoWeeksVisitors() 
```

**Returns**

Day and month of two weeks total visitors visited blog or post <code>(j M)</code> *array*

**<code>lastTwoWeeksTotalVisitors()</code>** <code>public</code>
```php
lastTwoWeeksTotalVisitors($blog_id)
```

**Parameters**

<code>$blog_id</code> *string*

Id of the blog or post.

**Returns**

Total visitors visited blog or post in two weeks day by day. *array*

**<code>totalVisitorsDate()</code>** <code>public</code>
```php
totalVisitorsDate($blog_id, $date) 
```

**Parameters**

<code>$blog_id</code> *string*

Id of the blog or post.

<code>$date</code> *string*

Specific date of visitors.

**Returns**

Total visitors visited blog or post in specific date. *integer*

#### Comments

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

**<code>dashboardStatistic()</code>** <code>public</code>
```php
dashboardStatistic($read = 'all')
```

**Parameters**

<code>$read</code> *string*

Comment status in the database. Default value is <code>all</code>. Other value is <code>read</code> or <code>unread</code>.

**Returns**

Total comments to show at dashboard statistic. *integer*

**<code>postComments()</code>** <code>public</code>
```php
postComments($blogId, $read = 'all', $type = 'fetch')
```

**Parameters**

<code>$blogId</code> *integer*

Id of the blog or post.

<code>$read</code> *string*

Comment status in the database. Default value is <code>all</code>. Other value is <code>read</code> or <code>unread</code>.

<code>$type</code> *string*

Return type of the function. Default value is <code>fetch</code>. Other value is <code>count</code>.

**Returns**

Query object for fetch, and total comments in post for count. *mixed*

**<code>publishedComments()</code>** <code>public</code>
```php
publishedComments($blogId, $type = 'fetch')
```

**Parameters**

<code>$blogId</code> *integer*

Id of the blog or post.

<code>$type</code> *string*

Return type of the function. Default value is <code>fetch</code>. Other value is <code>count</code> or <code>countall</code>.

**Returns**

Query object for fetch, total comments in post for count, and total comments and comments reply for countall. *mixed*

**<code>replyComments()</code>** <code>public</code>
```php
replyComments($blogId, $commentId, $type = 'fetch')
```

**Parameters**

<code>$blogId</code> *integer*

Id of the blog or post.

<code>$commentId</code> *integer*

Id of the comment.

<code>$type</code> *string*

Return type of the function. Default value is <code>fetch</code>. Other value is <code>count</code>.

**Returns**

Query object for fetch, and total comments reply for count. *moxed*

**<code>totalReplies()</code>** <code>public</code>
```php
totalReplies($blogId, $replyId, $type = 'number')
```

**Parameters**

<code>$blogId</code> *integer*

Id of the blog or post.

<code>$replyId</code> *integer*

Replied comment id.

<code>$type</code> *integer*

Return type of the function. Default value is <code>number</code>. Other value is <code>text</code>

**Returns**

Total reply comments in number format for number, and total reply comments in text format for text. For example, for number, it returns 5, for text, it returns 5 replies.

#### CustomPages

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

#### Generals

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

**<code>findById()</code>** <code>public</code>
```php
findById(Query $query, array $options)
```

**Parameters**

<code>Query $query</code> *object*

Query object.

<code>array $options</code> *array*

Additional options for query object.

**Returns**

Query object. *object*

#### Histories

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

**<code>saveActivity()</code>** <code>public</code>
```php
saveActivity($options)
```

**Parameters**

<code>$options</code> *integer*

Column in Histories Table.

**Returns**

True or false. *boolean*

#### MediaDocs

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

#### Medias

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

#### MediaVideos

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

#### Menus

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

**<code>fetchPublishedMenus()</code>** <code>public</code>
```php
fetchPublishedMenus() 
```

**Returns**

Fetch published menus and submenus. *array*

#### Messages

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

#### Notifications

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

#### Pages

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

#### PageTemplates

**<code>initialize()</code>** <code>public</code>
```php
initialize(array $config)
```

**Parameters**

<code>$config</code> *array*

Configuration options passed to the constructor.

#### Socials

**<code>initialize()</code>** <code>public</code>
```php
initialize(array $config)
```

**Parameters**

<code>$config</code> *array*

Configuration options passed to the constructor.

#### Submenus

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

#### Subscribers

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

#### Tags

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

**<code>checkExists()</code>** <code>public</code>
```php
checkExists($title) 
```

**Parameters**

<code>$title</code> *string*

Tag title to be checked.

**Returns**

True if exist, otherwise false. *boolean*

**<code>postTags()</code>** <code>public</code>
```php
postTags($blogId)
```

**Parameters**

<code>$blogId</code> *integer*

Id of the blog or post.

**Returns**

All tags in a blog or post. *Query object*

**<code>tagsSidebar()</code>** <code>public</code>
```php
tagsSidebar($limit = NULL)
```

**Parameters**

<code>$limit</code> *integer*

Total tags to be shown in sidebar. Default value is <code>NULL</code>

**Returns**

Tags to be shown in sidebar. *Query object*

#### Visitors

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

**<code>totalAllVisitors()</code>** <code>public</code>
```php
totalAllVisitors() 
```

**Returns**

Total all visitors (Mobile and desktop). *integer*

**<code>totalMobileVisitors()</code>** <code>public</code>
```php
totalMobileVisitors() 
```

**Returns**

Total mobile visitors. *integer*

**<code>lastTwoWeeksTotalVisitors()</code>** <code>public</code>
```php
lastTwoWeeksTotalVisitors() 
```

**Returns**

Total visitors in last two weeks. *array*

**<code>lastTwoWeeksBeforeTotalVisitors()</code>** <code>public</code>
```php
lastTwoWeeksBeforeTotalVisitors() 
```

**Returns**

Total visitors two weeks before last two weeks. *integer*

**<code>lastSixMonthVisitors()</code>** <code>public</code>
```php
lastSixMonthVisitors()
```

**Returns**

Month visited by visitors in last six months. *array*

**<code>lastSixMonthTotalVisitors()</code>** <code>public</code>
```php
lastSixMonthTotalVisitors()
```

**Returns**

Total visitors in last six months. *array*

**<code>lastSixMonthTotalMobileVisitors()</code>** <code>public</code>
```php
lastSixMonthTotalMobileVisitors()  
```

**Returns**

Total mobile visitors in last six months. *array*

**<code>countVisitorsInMonth()</code>** <code>public</code>
```php
countVisitorsInMonth($year = NULL, $month = NULL) 
```

**Parameters**

<code>$year</code> *integer*

Year to be checked (Y).

<code>$month</code> *date*

Month to be checked (m).

**Returns**

Total visitors in specific month and year. *integer*

**<code>totalVisitorsDate()</code>** <code>public</code>
```php
totalVisitorsDate($date) 
```

**Parameters**

<code>$date</code> *date*

Date to be checked.

**Returns**

Total visitors in specific date. *integer*

**<code>totalMobileVisitorsDate()</code>** <code>public</code>
```php
totalMobileVisitorsDate($date) 
```

**Parameters**

<code>$date</code> *integer*

Date to be checked.

**Returns**

Total mobile visitors in specific date. *integer*

**<code>visitorsPlatform()</code>** <code>public</code>
```php
visitorsPlatform($browser, $month = NULL, $year = NULL)
```

**Parameters**

<code>$browser</code> *string*

Browser to be checked.

<code>$month</code> *date*

Month to be checked (m). Default value is NULL.

<code>$year</code> *date*

Year to be checked (Y). Default value is NULL.

**Returns**

Total visitors in specific month, year, and browser. *integer*

**<code>checkVisitor()</code>** <code>public</code>
```php
checkVisitor($ip, $created, $browser, $platform, $device)
```

**Parameters**

<code>$ip</code> *string*

IP address to be checked.

<code>$created</code> *date*

Date created to be checked.

<code>$browser</code> *string*

Browser to be checked.

<code>$platform</code> *string*

Platform to be checked.

<code>$device</code> *string*

Device to be checked.

**Returns**

Total visitors with parameters above. *integer*

## View

### Helper

#### Purple Helper

**<code>readableFileSize()</code>** <code>public</code>
```php
readableFileSize($bytes, $precision = 2)
```

**Parameters**

<code>$bytes</code> *integer*

Size in bytes of the file.

<code>$precision</code> *integer*

The number of decimal digits to round to. Default value is 2.

**Returns**

File size in B, KB, MB, GB, or TB. *string*

**<code>shortenNumber()</code>** <code>public</code>
```php
shortenNumber($n, $precision = 2)
```

**Parameters**

<code>$n</code> *integer*

Number to be converted.

<code>$precision</code> *integer*

The number of decimal digits to round to. Default value is 2.

**Returns**

Formatted number. *string*

**<code>notificationCounter()</code>** <code>public</code>
```php
notificationCounter($number)
```

**Parameters**

<code>$number</code> *integer*

Total notification in Purple CMS.

**Returns**

Formatted total notification in Purple CMS. *integer*

**<code>plural()</code>** <code>public</code>
```php
plural($number, $verb, $addition = 's', $shorten = false) 
```

**Parameters**

<code>$number</code> *integer*

Number to be formatted to plural.

<code>$verb</code> *string*

Verb word to be added to plural sentence.

<code>$addition</code> *string*

Addition to verb, s, es or whatever. Default value is <code>s</code>

<code>$shorten</code> *string*

Option to shorten <code>$number</code> with <code>shortenNumber()</code> function.

**Returns**

Plural sentence with number and verb. *string*

**<code>getAllFuncInHtml()</code>** <code>public</code>
```php
getAllFuncInHtml($html)
```

**Parameters**

<code>$html</code> *string*

HTML code created in Block Editor.

**Returns**

All function name in HTML code. *integer*
