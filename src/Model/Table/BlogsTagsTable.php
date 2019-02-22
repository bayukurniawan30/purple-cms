<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class BlogsTagsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('blogs_tags');
	}
	public function checkExists($blogId, $tagId)
	{
        $query = $this->find()->where(['blog_id' => $blogId, 'tag_id' => $tagId]);
        if ($query->count() > 0) {
        	return true;
        }
        else {
        	return false;
        }

	}
}