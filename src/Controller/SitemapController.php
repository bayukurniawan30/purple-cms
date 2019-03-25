<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class SitemapController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $purpleGlobal = new PurpleProjectGlobal();
        $databaseInfo   = $purpleGlobal->databaseInfo();
        if ($databaseInfo == 'default') {
            return $this->redirect(
                ['prefix' => false, 'controller' => 'Setup', 'action' => 'index']
            );
        }
    }
    public function initialize()
    {
        $this->loadComponent('RequestHandler');
    }
    public function index()
    {
        $this->loadModel('Admins');
        $this->loadModel('Blogs');
        $this->loadModel('BlogCategories');
        $this->loadModel('Tags');
        $this->loadModel('Pages');
        $this->loadModel('Medias');
        $this->loadModel('MediaDocs');
        $this->loadModel('MediaVideos');
        $this->loadModel('Settings');

        $purpleSettings = new PurpleProjectSettings();
        $timezone       = $purpleSettings->timezone('time');

        $urls = [];

        // Home
        $admin = $this->Admins->find()->where(['id' => 1])->first();
        $logo = $this->Settings->settingsLogo();
        if ($logo == '') {
            $logoUrl = Router::url(['_name' => 'home', '_full' => true]).'/master-assets/img/logo.svg';
        }
        else {
            $logoUrl = Router::url(['_name' => 'home', '_full' => true]).'/uploads/images/original/'.$logo;
        }

        $urls[] = [
            'loc' => Router::url(['_name' => 'home', '_full' => true]),
            'lastmod' => $admin->created->format('Y-m-d') . 'T' . $admin->created->format('H:i:s') . $timezone,
            'changefreq' => 'weekly',
            'priority' => '0.8'
        ];

        // Pages
        $pages = $this->Pages->find('all')->contain('PageTemplates')->where(['status' => '1']);
        if ($pages->count() > 0) {
            foreach ($pages as $page) {
                if ($page->page_template_id == '1') {
                    $changeFreq = 'yearly';
                }
                elseif ($page->page_template_id == '2') {
                    $changeFreq = 'daily';
                }
                elseif ($page->page_template_id == '3') {
                    $changeFreq = 'yearly';
                }

                if ($page->modified == NULL) {
                    $modified = $page->created->format('Y-m-d') . 'T' . $page->created->format('H:i:s') . $timezone;
                }
                else {
                    $modified = $page->modified->format('Y-m-d') . 'T' . $page->modified->format('H:i:s') . $timezone;
                }

                $urls[] = [
                    'loc' => Router::url(['_name' => 'specificPage', 'page'  => $page->slug, '_full' => true]),
                    'lastmod' => $modified,
                    'changefreq' => $changeFreq,
                    'priority' => '0.5'
                ];
            }
        }

        // Blogs
        $blogs = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogTypes')->contain('BlogCategories')->where(['Blogs.status' => '1']);

        if ($blogs->count() > 0) {
            foreach ($blogs as $blog) {
                if ($blog->modified == NULL) {
                    $modified = $blog->created->format('Y-m-d') . 'T' . $blog->created->format('H:i:s') . $timezone;
                }
                else {
                    $modified = $blog->modified->format('Y-m-d') . 'T' . $blog->modified->format('H:i:s') . $timezone;
                }

                $urls[] = [
                    'loc' => Router::url(['_name' => 'specificPost', 'year'  => date('Y', strtotime($blog->created)), 'month' => date('m', strtotime($blog->created)), 'date'  => date('d', strtotime($blog->created)), 'post' => $blog->slug, '_full' => true]),
                    'lastmod' => $modified,
                    'changefreq' => 'daily',
                    'priority' => '0.5'
                ];
            }
        }

        // Blog Categories
        $categories = $this->BlogCategories->find('all', [
                'order' => ['BlogCategories.ordering' => 'ASC']]);

        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                if ($category->modified == NULL) {
                    $modified = $category->created->format('Y-m-d') . 'T' . $category->created->format('H:i:s') . $timezone;
                }
                else {
                    $modified = $category->modified->format('Y-m-d') . 'T' . $category->modified->format('H:i:s') . $timezone;
                }

                $urls[] = [
                    'loc' => Router::url(['_name' => 'postsInCategory', 'category'  => $category->slug, '_full' => true]),
                    'lastmod' => $modified,
                    'changefreq' => 'daily',
                    'priority' => '0.5'
                ];
            }
        }

        // Archives
        $archives    = $this->Blogs->archivesList();

        if ($archives->count() > 0) {
            $blog = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->where(['Blogs.status' => '1'])->first();

            if ($blog->modified == NULL) {
                $modified = $blog->created->format('Y-m-d') . 'T' . $blog->created->format('H:i:s') . $timezone;
            }
            else {
                $modified = $blog->modified->format('Y-m-d') . 'T' . $blog->modified->format('H:i:s') . $timezone;
            }

            foreach ($archives as $archive) {
                $urls[] = [
                    'loc' => Router::url(['_name' => 'archivesPost', 'year'  => date('Y', strtotime($archive->created)), 'month' => date('m', strtotime($archive->created)), '_full' => true]),
                    'lastmod' => $modified,
                    'changefreq' => 'daily',
                    'priority' => '0.5'
                ];
            }
        }

        // Tags
        $tags = $this->Tags->tagsSidebar();

        if ($tags->count() > 0) {
            $blog = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->where(['Blogs.status' => '1'])->first();

            if ($blog->modified == NULL) {
                $modified = $blog->created->format('Y-m-d') . 'T' . $blog->created->format('H:i:s') . $timezone;
            }
            else {
                $modified = $blog->modified->format('Y-m-d') . 'T' . $blog->modified->format('H:i:s') . $timezone;
            }

            foreach($tags as $tag) {
                $urls[] = [
                    'loc' => Router::url(['_name' => 'taggedPosts', 'tag'  => $tag->slug, '_full' => true]),
                    'lastmod' => $modified,
                    'changefreq' => 'daily',
                    'priority' => '0.5'
                ];
            }
        }

        // Define a custom root node in the generated document.
        $this->set('_rootNode', 'urlset');
        $this->set([
            // Define an attribute on the root node.
            '@xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            'xmlns:image' => 'http://www.google.com/schemas/sitemap-image/1.1',
            'url' => $urls
        ]);
        $this->set('_serialize', ['@xmlns', 'xmlns:image', 'url']);
    }
    public function robots()
    {
        $this->viewBuilder()->setLayout('robots');
    }
}