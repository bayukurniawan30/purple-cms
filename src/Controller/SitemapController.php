<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\Routing\Router;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

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

        $this->loadModel('Admins');
        $this->loadModel('Blogs');
        $this->loadModel('BlogCategories');
        $this->loadModel('Tags');
        $this->loadModel('Pages');
        $this->loadModel('Medias');
        $this->loadModel('MediaDocs');
        $this->loadModel('MediaVideos');
        $this->loadModel('Settings');
    }
    /**
     * @param $mediaName = media file name
     * @param $mediaType = ['image', 'document', 'video']
     * @param $return = default 'original', ['original', 'thumbnail::300', 'thumbnail::480']
     */
    private function mediaPath($mediaName, $mediaType, $return = 'original')
    {
        $mediaStorage = $this->Settings->fetch('mediastorage');

        if ($mediaStorage->value == 'awss3') {
            $awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
            $awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
            $awsS3Region    = $this->Settings->fetch('awss3region');
            $awsS3Bucket    = $this->Settings->fetch('awss3bucket');
        }
        else {
            $baseUrl = Router::url([
                '_name' => 'home'
            ], true);
        }

        if ($mediaType == 'image') {
            // If media storage is Amazon AWS S3
            if ($mediaStorage->value == 'awss3') {
                $s3Client = new S3Client([
                    'region'  => $awsS3Region->value,
                    'version' => 'latest',
                    'credentials' => [
                        'key'      => $awsS3AccessKey->value,
                        'secret'   => $awsS3SecretKey->value,
                    ]
                ]);

                // Key
                $originalKey           = 'images/original/' . $mediaName;
                $thumbnailSquareKey    = 'images/thumbnails/300x300/' . $mediaName;
                $thumbnailLandscapeKey = 'images/thumbnails/480x270/' . $mediaName;

                $original         = $s3Client->getObjectUrl($awsS3Bucket->value, $originalKey);
                $thumbnail300x300 = $s3Client->getObjectUrl($awsS3Bucket->value, $thumbnailSquareKey);
                $thumbnail480x270 = $s3Client->getObjectUrl($awsS3Bucket->value, $thumbnailLandscapeKey);
            }
            else {
                $original         = $baseUrl . 'uploads/images/original/' . $mediaName;
                $thumbnail300x300 = $baseUrl . 'uploads/images/thumbnails/300x300/' . $mediaName;
                $thumbnail480x270 = $baseUrl . 'uploads/images/thumbnails/480x270/' . $mediaName;
            }

            $result = [
                'path'  => $original,
                'thumbnail' => [
                    '300x300' => $thumbnail300x300,
                    '480x270' => $thumbnail480x270,
                ]
            ];
        }
        elseif ($mediaType == 'document') 
        {
            if ($mediaStorage->value == 'awss3') {
                $s3Client = new S3Client([
                    'region'  => $awsS3Region->value,
                    'version' => 'latest',
                    'credentials' => [
                        'key'      => $awsS3AccessKey->value,
                        'secret'   => $awsS3SecretKey->value,
                    ]
                ]);

                // Key
                $key = 'documents/' . $mediaName;

                $path = $s3Client->getObjectUrl($awsS3Bucket->value, $key);
            }
            else {
                $path = $baseUrl . 'uploads/documents/' . $mediaName;
            }

            $result = [
                'path' => $path
            ];
        }
        elseif ($mediaType == 'video') 
        {
            if ($mediaStorage->value == 'awss3') {
                $s3Client = new S3Client([
                    'region'  => $awsS3Region->value,
                    'version' => 'latest',
                    'credentials' => [
                        'key'      => $awsS3AccessKey->value,
                        'secret'   => $awsS3SecretKey->value,
                    ]
                ]);

                // Key
                $key = 'videos/' . $mediaName;

                $path = $s3Client->getObjectUrl($awsS3Bucket->value, $key);
            }
            else {
                $path = $baseUrl . 'uploads/videos/' . $mediaName;
            }

            $result = [
                'path' => $path
            ];
        }

        if ($return == 'original') {
            return $result['path'];
        }
        elseif ($return == 'thumbnail::300') {
            return $result['thumbnail']['300x300'];
        }
        elseif ($return == 'thumbnail::480') {
            return $result['thumbnail']['480x270'];
        }
    }
    public function index()
    {
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
            $logoUrl = $this->mediaPath($logo, 'image', 'original');
        }

        $urls[] = [
            'loc'         => Router::url(['_name' => 'home', '_full' => true]),
            'lastmod'     => $admin->created->format('Y-m-d') . 'T' . $admin->created->format('H:i:s') . $timezone,
            'changefreq'  => 'weekly',
            'priority'    => '0.8',
            'image:image' => ['image:loc' => $logoUrl]
        ];

        // Pages
        $pages = $this->Pages->find('all')->contain('PageTemplates')->where(['status' => '1']);
        if ($pages->count() > 0) {
            foreach ($pages as $page) {
                if ($page->page_template->type == 'general') {
                    $changeFreq = 'yearly';
                }
                elseif ($page->page_template->type == 'blog') {
                    $changeFreq = 'daily';
                }
                elseif ($page->page_template->type == 'custom') {
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
                    'lastmod'    => $modified,
                    'changefreq' => $changeFreq,
                    'priority'   => '0.5'
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

                if ($blog->featured == NULL || $blog->featured == '') {
                    $urlImage = NULL;
                }
                else {
                    if (strpos($blog->featured, ',') !== false) {
                        $explodeFeatured = explode(',', $blog->featured);
                        $urlImage = ['image:loc' => $this->mediaPath($explodeFeatured[0], 'image', 'original')];
                    }
                    else {
                        $urlImage = ['image:loc' => $this->mediaPath($blog->featured, 'image', 'original')];
                    }
                }

                $urls[] = [
                    'loc' => Router::url(['_name' => 'specificPost', 'year'  => date('Y', strtotime($blog->created)), 'month' => date('m', strtotime($blog->created)), 'date'  => date('d', strtotime($blog->created)), 'post' => $blog->slug, '_full' => true]),
                    'lastmod'     => $modified,
                    'changefreq'  => 'daily',
                    'priority'    => '0.5',
                    'image:image' => $urlImage
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
                    'lastmod'    => $modified,
                    'changefreq' => 'daily',
                    'priority'   => '0.5'
                ];
            }
        }

        // Archives
        $archives = $this->Blogs->archivesList();

        if (count($archives) > 0) {
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
                    'lastmod'    => $modified,
                    'changefreq' => 'daily',
                    'priority'   => '0.5'
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
                    'lastmod'    => $modified,
                    'changefreq' => 'daily',
                    'priority'   => '0.5'
                ];
            }
        }

        $purplePlugins = new PurpleProjectPlugins();
        $sitemap       = $purplePlugins->pluginSitemap();

        if ($sitemap != false) {
            foreach ($sitemap as $pluginSitemap) {
                array_push($urls, $pluginSitemap);
            }
        }
            
        $urlsKey = 0;
        foreach ($urls as $url) {
            if (array_key_exists('image:image', $urls[$urlsKey]) && $urls[$urlsKey]['image:image'] == NULL) {
                unset($urls[$urlsKey]['image:image']);
            }
            $urlsKey++;
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