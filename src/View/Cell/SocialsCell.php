<?php
namespace App\View\Cell;
use Cake\View\Cell;

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
            $this->set('instagramPosts', json_encode($igMedias));
        }
    }
}