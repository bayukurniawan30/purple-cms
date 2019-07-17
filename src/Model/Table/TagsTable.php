<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class TagsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('tags');
		$this->setPrimaryKey('id');
		$this->belongsToMany('Blogs', [
			'joinTable'        => 'blogs_tags',
			'foreignKey'       => 'tag_id',
			'targetForeignKey' => 'blog_id'
        ]);
	}
	public function beforeSave($event, $entity, $options)
    {
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// if (!$entity->slug) {
	        $sluggedTitle = Text::slug(strtolower($entity->title));
	        // trim slug to maximum length defined in schema
	        $entity->slug = substr($sluggedTitle, 0, 191);
		// }

		// Sanitize title
		$entity->title   = trim(strip_tags($entity->title));

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
		else {
			$entity->modified = $date;
		}
	}
	protected function _getCreated($created)
    {
        $serverRequest   = new ServerRequest();
        $session         = $serverRequest->getSession();
        $timezone        = $session->read('Purple.timezone');
        $settingTimezone = $session->read('Purple.settingTimezone');

        $date = new \DateTime($created, new \DateTimeZone($settingTimezone));
        $date->setTimezone(new \DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }
    protected function _getModified($modified)
    {
        if ($modified == NULL) {
            return $modified;
        }
        else {
            $serverRequest   = new ServerRequest();
            $session         = $serverRequest->getSession();
            $timezone        = $session->read('Purple.timezone');
            $settingTimezone = $session->read('Purple.settingTimezone');

            $date = new \DateTime($modified, new \DateTimeZone($settingTimezone));
            $date->setTimezone(new \DateTimeZone($timezone));
            return $date->format('Y-m-d H:i:s');
        }
	}
	public function checkExists($title)
	{
        $sluggedTitle = Text::slug(strtolower(trim($title)));
        $query = $this->find()->where(['slug' => $sluggedTitle]);
        if ($query->count() > 0) {
        	return true;
        }
        else {
        	return false;
        }
	}
	public function postTags($blogId)
	{
		$tags  = $this->find();
		$tags->matching('Blogs', function ($q) use ($blogId) {
		    return $q->where(['Blogs.id' => $blogId]);
		});

		return $tags;
	}
	public function tagsSidebar($limit = NULL)
	{
		$tags = $this->find();

		if ($limit == NULL) {
			$tags->order('rand()');
		}
		else {
			$tags->order('rand()')->limit($limit);
		}
		
		return $tags;
	}
}