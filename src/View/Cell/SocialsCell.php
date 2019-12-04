<?php
namespace App\View\Cell;
use Cake\View\Cell;
use Cake\Log\Log;

class SocialsCell extends Cell
{
	public function instagramPosts($username = 'default', $limit = 12)
	{
        $this->loadModel('Socials');

        $socials = $this->Socials->find('all')->order(['ordering' => 'ASC']);
        
        $instagramUrl = false;
        $igMedias     = false;
        if ($socials->count() > 0) {
            foreach ($socials as $social) {
                if ($social->name == 'instagram' && $social->link != '') {
                    $instagramAccount = true;
                    $instagramUrl     = $social->link;
                }
            }

            if ($instagramAccount == true) {
                $instagramApi     = new \InstagramScraper\Instagram();
                $instagramLink    = parse_url($instagramUrl);
                $instagramAccount = str_replace('/', '', $instagramLink['path']);

                if ($username == 'default') {
                    $igMedias = $instagramApi->getMedias($instagramAccount, $limit);
                }
                else {
                    $igMedias = $instagramApi->getMedias($username, $limit);
                }
            }
        }

        if ($igMedias == false) {
            $this->set('instagramPosts', '0');
        }
        else {
            $json = [];
            $i    = 0; 
            foreach ($igMedias as $post) {
                $json[$i]['Id']       = $post->getId();
                $json[$i]['image']    = $post->getImageHighResolutionUrl();
                $json[$i]['created']  = $post->getCreatedTime();
                $json[$i]['link']     = $post->getLink();
                $json[$i]['type']     = $post->getType();
                $json[$i]['caption']  = $post->getCaption();
                $json[$i]['comments'] = $post->getCommentsCount();
                $json[$i]['likes']    = $post->getLikesCount();

                ++$i;
            }

            $this->set('instagramPosts', json_encode($json));
        }
    }
}