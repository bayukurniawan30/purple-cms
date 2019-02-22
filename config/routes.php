<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;
use Cake\Datasource\ConnectionManager;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 * Cache: Routes are cached to improve performance, check the RoutingMiddleware
 * constructor in your `src/Application.php` file to change this behavior.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

$purpleGlobal = new PurpleProjectGlobal();
$databaseInfo   = $purpleGlobal->databaseInfo();
if ($databaseInfo == 'default') {
    $maintenance    = 'disable';
    $userLoggedIn   = false;
}
else {
    $connection = ConnectionManager::get('default');
    $results = $connection->execute("SHOW TABLES LIKE 'settings'");
    $count   = $results->rowCount();

    if ($count > 0) {
        $purpleSettings = new PurpleProjectSettings();
        $maintenance    = $purpleSettings->maintenanceMode();
        $userLoggedIn   = $purpleSettings->checkUserLoggedIn();
    }
    else {
        $maintenance    = 'disable';
        $userLoggedIn   = false;
    }
}

if ($maintenance == 'enable' && $userLoggedIn == false) {
    Router::scope('/', function ($routes) {
        /**
         * Maintenance Routing
         */
        $routes->connect('/', 
                ['controller' => 'Maintenance', 'action' => 'index'], 
                ['_name' => 'home']
        );

        $routes->connect('/maintenance/ajax-get-email', 
                ['controller' => 'Maintenance', 'action' => 'ajaxGetEmail'], 
                ['_name' => 'ajaxGetNotifyEmail']
        );

        $routes->redirect(
            '/*',
            ['controller' => 'Maintenance', 'action' => 'index']
        );
    });
}
else {
    Router::scope('/sitemap', function ($routes) {
        $routes->setExtensions(['xml']);
        $routes->connect('/',
                ['controller' => 'Sitemap', 'action' => 'index'],
                ['_name' => 'websiteSitemap']
        );
    });

    Router::scope('/', function (RouteBuilder $routes) {
        /**
         * Here, we are connecting '/' (base path) to a controller called 'Pages',
         * its action called 'display', and we pass a param to select the view file
         * to use (in this case, src/Template/Pages/home.ctp)...
         */
        
        /**
         * Home Routing
         */
        $routes->connect('/', 
                ['controller' => 'Pages', 'action' => 'home'], 
                ['_name' => 'home']
        );
        

        /**
         * Specific Page Routing
         * E.g. General, Blog, or Custom Page 
         */
        $routes->connect('/:page', 
                ['controller' => 'Pages'], 
                ['_name' => 'specificPage'])
            ->setPass(['page']
        );

        /**
         * Blog Routing with paging
         */
        $routes->connect('/:page/page/:paging', 
                ['controller' => 'Pages', 'action' => 'blog'], 
                ['_name' => 'blogPagination'])
            ->setPatterns(['paging' => '\d+'])
            ->setPass(['paging']
        );

        /**
         * Blog Posts in Category Routing
         */
        $routes->connect('/posts/:category', 
                ['controller' => 'Blogs', 'action' => 'postsInCategory'], 
                ['_name' => 'postsInCategory'])
            ->setPass(['category']
        );

        /**
         * Blog Posts in Category Routing with paging
         */
        $routes->connect('/posts/:category/page/:paging', 
                ['controller' => 'Blogs', 'action' => 'postsInCategory'], 
                ['_name' => 'postsInCategoryPagination'])
            ->setPatterns(['paging' => '\d+'])
            ->setPass(['category', 'paging']
        );

        /**
         * Blog Post Detail Routing
         * Use post DATE created and slug as URL
         */
        $routes->connect('/:year/:month/:date/:post', 
                ['controller' => 'Blogs', 'action' => 'detail'], 
                ['_name' => 'specificPost'])
            ->setPatterns([
                'year'  => '[12][0-9]{3}', 
                'month' => '0[1-9]|1[012]', 
                'date'  => '0[1-9]|[12][0-9]|3[01]'])
            ->setPass(['post']
        );

        /* Use post YEAR and MONTH created and slug as URL */
        $routes->connect('/:year/:month/:post', 
                ['controller' => 'Blogs', 'action' => 'detail'], 
                ['_name' => 'specificPostMonth'])
            ->setPatterns([
                'year'  => '[12][0-9]{3}', 
                'month' => '0[1-9]|1[012]'])
            ->setPass(['post']
        );

        /* Use post slug as URL */
        $routes->connect('/:post', 
                ['controller' => 'Blogs', 'action' => 'detail'], 
                ['_name' => 'specificPostName'])
            ->setPass(['post']
        );

        /**
         * Blog Post in Tag Routing
         */
        $routes->connect('/tag/:tag', 
                ['controller' => 'Blogs', 'action' => 'tag'], 
                ['_name' => 'taggedPosts'])
            ->setPass(['tag']
        );

        /**
         * Blog Post in Tag Routing with paging
         */
        $routes->connect('/tag/:tag/page/:paging', 
                ['controller' => 'Blogs', 'action' => 'tag'], 
                ['_name' => 'taggedPostsPagination'])
            ->setPatterns(['paging' => '\d+'])
            ->setPass(['tag', 'paging']
        );

        /**
         * Blog Post Archives Routing
         */
        $routes->connect('/archives/:year/:month', 
                ['controller' => 'Blogs', 'action' => 'archives'], 
                ['_name' => 'archivesPost'])
            ->setPatterns([
                'year'  => '[12][0-9]{3}', 
                'month' => '0[1-9]|1[012]'])
            ->setPass(['year', 'month']
        );

        /**
         * Blog Post Archives Routing with paging
         */
        $routes->connect('/archives/:year/:month/page/:paging', 
                ['controller' => 'Blogs', 'action' => 'archives'], 
                ['_name' => 'archivesPostPagination'])
            ->setPatterns([
                'year'  => '[12][0-9]{3}', 
                'month' => '0[1-9]|1[012]'])
            ->setPass(['year', 'month', 'paging']
        );

        /**
         * Search Routing
         * Only accept POST
         */
        $routes->connect('/search', 
                ['controller' => 'Search', 'action' => 'index'], 
                ['_name' => 'searchPost'])
            ->setPass(['search'])
            ->setMethods(['POST']
        );

        /**
         * Ajax Routing
         */
        $routes->connect('/ajax/verify-form/:action/:token', 
                ['controller' => 'Pages', 'action' => 'ajaxVerifyForm'], 
                ['_name' => 'ajaxVerifyForm'])
            ->setPass(['action', 'token']);
        $routes->connect('/ajax/send-comment', 
                ['controller' => 'Blogs', 'action' => 'ajaxSendComment'], 
                ['_name' => 'ajaxSendComment']);
        $routes->connect('/ajax/send-contact', 
                ['controller' => 'Pages', 'action' => 'ajaxSendContact'], 
                ['_name' => 'ajaxSendContact']);

        /**
         * Connect catchall routes for all controllers.
         *
         * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
         *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
         *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
         *
         * Any route class can be used with this method, such as:
         * - DashedRoute
         * - InflectedRoute
         * - Route
         * - Or your own route class
         *
         * You can remove these routes once you've connected the
         * routes you want in your application.
         */
        $routes->fallbacks(DashedRoute::class);
    });
}

/**
 *  Setup route
 */

Router::scope('/setup', function ($routes) {
    $routes->connect('/', 
            ['controller' => 'Setup', 'action' => 'index'], 
            ['_name' => 'setupIndex']);
    $routes->connect('/administrative', 
            ['controller' => 'Setup', 'action' => 'administrative'], 
            ['_name' => 'setupAdministrative']);
    $routes->connect('/finish', 
            ['controller' => 'Setup', 'action' => 'finish'], 
            ['_name' => 'setupFinish']);
});

/**
 *  Admin route
 */

Router::prefix('purple', function ($routes) {
    // Because you are in the purple scope,
    // you do not need to include the /purple prefix
    // or the admin route element.

    /**
     * Login, Logout, Forgot Password, and Dashboard Route
     * Controller : Authenticate, Dashboard
     */
    $routes->connect('/', 
            ['controller' => 'Authenticate', 'action' => 'login'], 
            ['_name' => 'adminLogin']);
    $routes->connect('/reset-password/token/:token', 
            ['controller' => 'Authenticate', 'action' => 'reset-password'], 
            ['_name' => 'adminResetPassword'])
        ->setPass(['token']);
    $routes->connect('/logout', 
            ['controller' => 'Authenticate', 'action' => 'logout'], 
            ['_name' => 'adminLogout']);
    $routes->connect('/dashboard', 
            ['controller' => 'Dashboard', 'action' => 'index'], 
            ['_name' => 'adminDashboard']);
    $routes->connect('/dashboard/:action', 
            ['controller' => 'Dashboard'], 
            ['_name' => 'adminDashboardAction']);
    $routes->connect('/ajax-login', 
            ['controller' => 'Authenticate', 'action' => 'ajax-login'], 
            ['_name' => 'adminAjaxLogin']);
    $routes->connect('/ajax-forgot-password', 
            ['controller' => 'Authenticate', 'action' => 'ajax-forgot-password'], 
            ['_name' => 'adminAjaxForgotPassword']);
    $routes->connect('/ajax-reset-password', 
            ['controller' => 'Authenticate', 'action' => 'ajax-reset-password'], 
            ['_name' => 'adminAjaxResetPassword']);

    /**
     * Settings Route
     * Controller : Settings
     */
    $routes->connect('/settings/:action', 
            ['controller' => 'Settings'], 
            ['_name' => 'adminSettingsAction']);

    /**
     * Medias Route
     * Controller : Medias
     */
    $routes->connect('/medias/:action', 
            ['controller' => 'Medias'], 
            ['_name' => 'adminMediasAction']);
    $routes->connect('/medias/images/page/:id', 
            ['controller' => 'Medias', 'action' => 'images'], 
            ['_name' => 'adminMediasImagesPagination'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);

    /**
     * Appearance Route
     * Controller : Appearance
     */
    $routes->connect('/appearance/:action', 
            ['controller' => 'Appearance'], 
            ['_name' => 'adminAppearanceAction']);

    /**
     * Navigation Route
     * Controller : Navigation
     */
    $routes->connect('/navigation', 
            ['controller' => 'Navigation'], 
            ['_name' => 'adminNavigation']);
    $routes->connect('/navigation/:action', 
            ['controller' => 'Navigation'], 
            ['_name' => 'adminNavigationAction']);
    $routes->connect('/navigation/child/:parent',
            ['controller' => 'Navigation', 'action' => 'index'], 
            ['_name' => 'adminNavigationChild'])
        ->setPass(['parent']);

    /**
     * Blogs Route
     * Controller : Admins
     */
    $routes->connect('/blogs', 
            ['controller' => 'Blogs'], 
            ['_name' => 'adminBlogs']);
    $routes->connect('/blogs/:action', 
            ['controller' => 'Blogs'], 
            ['_name' => 'adminBlogsAction']);
    $routes->connect('/blogs/edit/:blogid', 
            ['controller' => 'Blogs', 'action' => 'edit'], 
            ['_name' => 'adminBlogsEdit'])
        ->setPatterns(['blogid' => '\d+'])
        ->setPass(['blogid']);

    /**
     * Comments Route
     * Controller : Admins
     */
    $routes->connect('/comments/:blogid', 
            ['controller' => 'Comments'], 
            ['_name' => 'adminComments'])
        ->setPatterns(['blogid' => '\d+'])
        ->setPass(['blogid']);
    $routes->connect('/comments/:blogid/page/:id', 
            ['controller' => 'Comments', 'action' => 'index'], 
            ['_name' => 'adminCommentsPagination'])
        ->setPatterns(['blogid' => '\d+', 'id' => '\d+'])
        ->setPass(['blogid', 'id']);
    $routes->connect('/comments/:blogid/:action', 
            ['controller' => 'Comments'], 
            ['_name' => 'adminCommentsAction'])
        ->setPatterns(['blogid' => '\d+'])
        ->setPass(['blogid']);
    $routes->connect('/comments/:blogid/view/:id', 
            ['controller' => 'Comments', 'action' => 'detail'], 
            ['_name' => 'adminCommentsView'])
        ->setPatterns(['id' => '\d+', 'blogid' => '\d+'])
        ->setPass(['blogid', 'id']);

    /**
     * Blog Categories Route
     * Controller : Admins
     */
    $routes->connect('/blog-categories', 
            ['controller' => 'BlogCategories'], 
            ['_name' => 'adminBlogCategories']);
    $routes->connect('/blog-categories/:action', 
            ['controller' => 'BlogCategories'], 
            ['_name' => 'adminBlogCategoriesAction']);

    /**
     * Socials Route
     * Controller : Socials
     */
    $routes->connect('/socials', 
            ['controller' => 'Socials'], 
            ['_name' => 'adminSocials']);
    $routes->connect('/socials/:action', 
            ['controller' => 'Socials'], 
            ['_name' => 'adminSocialsAction']);

    /**
     * Subscribers Route
     * Controller : Socials
     */
    $routes->connect('/subscribers', 
            ['controller' => 'Subscribers'], 
            ['_name' => 'adminSubscribers']);
    $routes->connect('/subscribers/:action', 
            ['controller' => 'Subscribers'], 
            ['_name' => 'adminSubscribersAction']);

    /**
     * Notifications Route
     * Controller : Notifications
     */
    $routes->connect('/notifications', 
            ['controller' => 'Notifications'], 
            ['_name' => 'adminNotifications']);
    $routes->connect('/notifications/page/:id', 
            ['controller' => 'Notifications', 'action' => 'index'], 
            ['_name' => 'adminNotificationsPagination'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $routes->connect('/notifications/:action', 
            ['controller' => 'Notifications'], 
            ['_name' => 'adminNotificationsAction']);
    $routes->connect('/notifications/filter/:filter',
            ['controller' => 'Notifications', 'action' => 'filter'],
            ['_name' => 'adminNotificationsFilter'])
        ->setPass(['filter']);
    $routes->connect('/notifications/filter/:filter/page/:id',
            ['controller' => 'Notifications', 'action' => 'filter'],
            ['_name' => 'adminNotificationsFilterPagination'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['filter', 'id']);

    /**
     * Messages Route
     * Controller : Messages
     */
    $routes->connect('/messages', 
            ['controller' => 'Messages'], 
            ['_name' => 'adminMessages']);
    $routes->connect('/messages/:action', 
            ['controller' => 'Messages'], 
            ['_name' => 'adminMessagesAction']);
    $routes->connect('/messages/page/:id', 
            ['controller' => 'Messages', 'action' => 'index'], 
            ['_name' => 'adminMessagesPagination'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $routes->connect('/messages/view/:id', 
            ['controller' => 'Messages', 'action' => 'detail'], 
            ['_name' => 'adminMessagesView'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $routes->connect('/messages/folder/:folder',
            ['controller' => 'Messages', 'action' => 'folder'],
            ['_name' => 'adminMessagesFolder'])
        ->setPass(['folder']);
    $routes->connect('/messages/folder/:folder/page/:id',
            ['controller' => 'Messages', 'action' => 'folder'],
            ['_name' => 'adminMessagesFolderPagination'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['folder', 'id']);

    /**
     * Histories Route
     * Controller : Messages
     */
    $routes->connect('/histories', 
            ['controller' => 'Histories'], 
            ['_name' => 'adminHistories']);
    $routes->connect('/histories/:action', 
            ['controller' => 'Histories'], 
            ['_name' => 'adminHistoriesAction']);
    $routes->connect('/histories/page/:id', 
            ['controller' => 'Histories', 'action' => 'index'], 
            ['_name' => 'adminHistoriesPagination'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $routes->connect('/histories/filter/:year/:month', 
            ['controller' => 'Histories', 'action' => 'filter'], 
            ['_name' => 'adminHistoriesFilter'])
        ->setPatterns([
            'year'  => '[12][0-9]{3}', 
            'month' => '0[1-9]|1[012]'])
        ->setPass(['year', 'month']);
    $routes->connect('/histories/filter/:year/:month/page/:id', 
            ['controller' => 'Histories', 'action' => 'filter'], 
            ['_name' => 'adminHistoriesFilterPagination'])
        ->setPatterns([
            'year'  => '[12][0-9]{3}', 
            'month' => '0[1-9]|1[012]',
            'id' => '\d+'])
        ->setPass(['year', 'month', 'id']);

    /**
     * Users Route
     * Controller : Admins
     */
    $routes->connect('/users', 
            ['controller' => 'Admins'], 
            ['_name' => 'adminUsers']);
    $routes->connect('/users/:action', 
            ['controller' => 'Admins'], 
            ['_name' => 'adminUsersAction']);
    $routes->connect('/users/edit/:id', 
            ['controller' => 'Admins', 'action' => 'edit'], 
            ['_name' => 'adminUsersEdit'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $routes->connect('/users/change-password/:id', 
            ['controller' => 'Admins', 'action' => 'changePassword'], 
            ['_name' => 'adminUsersChangePassword'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);

    /**
     * Froala Route
     * Controller : Froala
     */
    $routes->connect('/froala/:action', 
            ['controller' => 'Froala'], 
            ['_name' => 'adminFroalaAction']);

    /**
     * Pages Route
     * Controller : Pages
     */
    $routes->connect('/pages', 
            ['controller' => 'Pages'], 
            ['_name' => 'adminPages']);
    $routes->connect('/pages/:action', 
            ['controller' => 'Pages'], 
            ['_name' => 'adminPagesAction']);
    $routes->connect('/pages/:type/:id/:slug',
            ['controller' => 'Pages', 'action' => 'detail'],
            ['_name' => 'adminPagesDetail'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['type', 'id', 'slug']);
    $routes->connect('/pages/html-blocks/:file',
            ['controller' => 'Pages', 'action' => 'generatedBlocks'],
            ['_name' => 'adminPagesHtmlBlocks'])
        ->setPass(['file']);
    $routes->connect('/pages/blog/:id/:slug/add',
            ['controller' => 'Blogs', 'action' => 'add'],
            ['_name' => 'adminPagesBlogsAdd'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id', 'slug']);
    $routes->connect('/pages/blog/:id/:slug/edit/:blogid',
            ['controller' => 'Blogs', 'action' => 'edit'],
            ['_name' => 'adminPagesBlogsEdit'])
        ->setPatterns(['id' => '\d+', 'blogid' => '\d+'])
        ->setPass(['id', 'slug', 'blogid']);
    $routes->connect('/pages/blog/:id/:slug/categories',
            ['controller' => 'BlogCategories', 'action' => 'index'],
            ['_name' => 'adminPagesBlogCategories'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id', 'slug']);
    $routes->connect('/pages/blog/:id/:slug/comments/:blogid',
            ['controller' => 'Comments', 'action' => 'index'],
            ['_name' => 'adminPagesComments'])
        ->setPatterns(['id' => '\d+', 'blogid' => '\d+'])
        ->setPass(['id', 'slug', 'blogid']);
    $routes->connect('/pages/blog/:id/:slug/comments/view/:commentid',
            ['controller' => 'Comments', 'action' => 'detail'],
            ['_name' => 'adminPagesCommentsView'])
        ->setPatterns(['id' => '\d+', 'blogid' => '\d+', 'commentid' => '\d+'])
        ->setPass(['id', 'slug', 'blogid', 'commentid']);

    /**
     * Themes Route
     * Controller : Socials
     */
    $routes->connect('/themes', 
            ['controller' => 'Themes'], 
            ['_name' => 'adminThemes']);
    $routes->connect('/themes/:action', 
            ['controller' => 'Themes'], 
            ['_name' => 'adminThemesAction']);

    /**
     * Search Routing
     * Only accept POST
     */
    $routes->connect('/search', 
            ['controller' => 'Search', 'action' => 'index'], 
            ['_name' => 'adminSearchPost'])
        ->setPass(['search'])
        ->setMethods(['POST']
    );
});