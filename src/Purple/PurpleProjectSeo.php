<?php

namespace App\Purple;

use Cake\ORM\TableRegistry;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectGlobal;
use Melbahja\Seo\Factory;

class PurpleProjectSeo 
{
    public function schemaLdJson($type, $option = NULL)
    {
		$serverRequest = new ServerRequest();
        $purpleGlobal  = new PurpleProjectGlobal();
        $protocol      = $purpleGlobal->protocol();
		$socialsList   = TableRegistry::get('Socials')->find('all')->select(['link'])->order(['ordering' => 'ASC'])->toArray();
		$ldJson        = TableRegistry::get('Settings')->find()->where(['name' => 'ldjson'])->first();
		$siteName      = TableRegistry::get('Settings')->find()->where(['name' => 'sitename'])->first();
		$metaDesc      = TableRegistry::get('Settings')->find()->where(['name' => 'metadescription'])->first();
		$logo          = TableRegistry::get('Settings')->find()->where(['name' => 'websitelogo'])->first();

        if ($type == 'website') {
            $schema = Factory::schema('WebSite')
                    ->name($siteName->value)
                    ->url($protocol.$serverRequest->host().$serverRequest->getAttribute("webroot"));
        }
        else if ($type == 'webpage') {
            $schema = Factory::schema('WebPage')
                    ->name($option['title'])
                    ->url($option['url']);

            if ($option['description'] != NULL) {
                $schema->description($option['description']);
            }
        }
        elseif ($type == 'organization') {
            $schema = Factory::schema('Organization')
                    ->url($protocol.$serverRequest->host().$serverRequest->getAttribute("webroot"))          
                    ->name($siteName->value)
                    ->description($metaDesc->value);

            // Add logo property
            if ($logo->value != '') {
                $schema->logo($protocol.$serverRequest->host().$serverRequest->getAttribute("webroot").'uploads/images/original/'.$logo->value);
            } 
        }
        elseif ($type == 'breadcrumblist') {
            if (is_array($option)) {
                $breadcrumbList = array();
                $breadcrumbList = [
                    0 => [
                        "@type"    => "ListItem",
                        "position" => 1,
                        "item"     => [
                            "@id"  => $protocol.$serverRequest->host().$serverRequest->getAttribute("webroot"),
                            "name" => $siteName->value
                        ]
                    ]
                ];

                $start = 1;
                $position = 2;
                foreach ($option as $list) {
                    $breadcrumbList[$start] = [
                        "@type"    => "ListItem",
                        "position" => $position,
                        "item"     => [
                            "@id"  => $list["@id"],
                            "name" => $list["name"]
                        ]
                    ];

                    $start++;
                    $position++;
                }

                $schema = Factory::schema('BreadcrumbList')
                            ->name($siteName->value)
                            ->itemListElement($breadcrumbList);
            }
            else {
                return false;
            }
        }
        elseif ($type == 'article') {
            if (is_array($option)) {
                $author        = $option['author'];
                $datePublished = $option['datePublished'];
                $datemodified  = $option['datemodified'];
                $headline      = $option['headline'];
                $image         = $option['image'];
                $articleBody   = $option['articleBody'];

                $schema = Factory::schema('Article')
                            ->author->set('@type', 'Person')->name($author)->getRoot();

                $schema->datePublished($datePublished)
                       ->datemodified($datemodified)
                       ->headline($headline)
                       ->image($image)
                       ->articleBody($articleBody)
                       ->mainEntityOfPage(true);
                            
                // Add logo property
                if ($logo->value != '') {
                    $schema->publisher
                        ->set('@type', 'Organization')
                        ->name($siteName->value)
                        ->logo
                            ->set('@type', 'imageObject')
                            ->url($protocol.$serverRequest->host().$serverRequest->getAttribute("webroot").'uploads/images/original/'.$logo->value);
                } 
                else {
                    $schema->publisher
                        ->set('@type', 'Organization')
                        ->name($siteName->value);
                }
            }
            else {
                return false;
            }
        }

        // Add sameAs property if social media is available
        if ($type == 'website' || $type == 'organization') {
            if (count($socialsList) > 0) {
                $socialList = array();
                foreach ($socialsList as $social) {
                    $socialList[] = $social['link'];
                }
                $schema->sameAs($socialList);
            }
        }

        if ($ldJson->value == 'enable') {
            return $purpleGlobal->reformatLdJson($schema);
        }
        else {
            return false;
        }       
    }
}