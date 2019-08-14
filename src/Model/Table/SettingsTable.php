<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Purple\PurpleProjectSettings;

class SettingsTable extends Table
{
	public function initialize(array $config)
	{
        $this->setTable('settings');
		$this->setPrimaryKey('id');
    }
    public function settingsSiteName() 
    {
        $sitename = $this->find()->where(['name' => 'sitename'])->first();
        return $sitename->value;
    }
    public function settingsTagLine() 
    {
        $tagline = $this->find()->where(['name' => 'tagline'])->first();
        return $tagline->value;
    }
    public function settingsEmail() 
    {
        $email = $this->find()->where(['name' => 'email'])->first();
        return $email->value;
    }
    public function settingsPhone() 
    {
        $phone = $this->find()->where(['name' => 'phone'])->first();
        return $phone->value;
    }
    public function settingsAddress() 
    {
        $address = $this->find()->where(['name' => 'address'])->first();
        return $address->value;
    }
    public function settingsMetaKeywords() 
    {
        $metaKeywords = $this->find()->where(['name' => 'metakeywords'])->first();
        return $metaKeywords->value;
    }
    public function settingsMetaDescription() 
    {
        $metaDescription = $this->find()->where(['name' => 'metadescription'])->first();
        return $metaDescription->value;
    }
    public function settingsAnalyticscode() 
    {
        $googleAnalyticsCode = $this->find()->where(['name' => 'googleanalyticscode'])->first();
        return $googleAnalyticsCode->value;
    }
    public function settingsFavicon() 
    {
    	$favicon = $this->find()->where(['name' => 'favicon'])->first();
    	return $favicon->value;
    }
    public function settingsLogo() 
    {
    	$logo = $this->find()->where(['name' => 'websitelogo'])->first();
    	return $logo->value;
    }
    public function settingsHomepage() 
    {
    	$homepage = $this->find()->where(['name' => 'homepagestyle'])->first();
    	return $homepage->value;
    }
    public function settingsLeftFooter() 
    {
        $footer = $this->find()->where(['name' => 'secondaryfooter'])->first();
        $explodeFooter = explode('::', $footer->value);
        if ($explodeFooter[0] == 'NULL') {
            return '';
        }
        else {
            return html_entity_decode($explodeFooter[0]);
        }
    }
    public function settingsRightFooter() 
    {
        $footer = $this->find()->where(['name' => 'secondaryfooter'])->first();
        $explodeFooter = explode('::', $footer->value);
        if ($explodeFooter[1] == 'NULL') {
            return '';
        }
        else {
            return html_entity_decode($explodeFooter[1]);
        }
    }
    public function settingsDateFormat() 
    {
        $format = $this->find()->where(['name' => 'dateformat'])->first();
        return $format->value;
    }
    public function settingsTimeFormat() 
    {
        $format = $this->find()->where(['name' => 'timeformat'])->first();
        return $format->value;
    }
    public function settingsTimeZone() 
    {
        $purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
        return $timezone;
    }
    public function settingsRecaptchaSitekey() 
    {
        $sitekey = $this->find()->where(['name' => 'recaptchasitekey'])->first();
        return $sitekey->value;
    }
    public function settingsRecaptchaSecret() 
    {
        $secret = $this->find()->where(['name' => 'recaptchasecret'])->first();
        return $secret->value;
    }
    public function settingsPostLimitPerPage() 
    {
        $limit = $this->find()->where(['name' => 'postlimitperpage'])->first();
        return $limit->value;
    }
    public function settingsSocialShare() 
    {
        $socialShare = $this->find()->where(['name' => 'socialshare'])->first();
        return $socialShare->value;
    }
    public function settingsSocialTheme() 
    {
        $socialTheme = $this->find()->where(['name' => 'socialtheme'])->first();
        return $socialTheme->value;
    }
    public function settingsSocialFontSize() 
    {
        $socialFontSize = $this->find()->where(['name' => 'socialfontsize'])->first();
        return $socialFontSize->value;
    }
    public function settingsSocialLabel() 
    {
        $socialLabel = $this->find()->where(['name' => 'sociallabel'])->first();
        return $socialLabel->value;
    }
    public function settingsSocialCount() 
    {
        $socialCount = $this->find()->where(['name' => 'socialcount'])->first();
        return $socialCount->value;
    }
    public function settingsPublicApiKey() 
    {
        $apikey = $this->find()->where(['name' => 'purpleapipublic'])->first();
        return $apikey->value;
    }
    public function settingsApiAccessKey() 
    {
        $apikey = $this->find()->where(['name' => 'apiaccesskey'])->first();
        return $apikey->value;
    }
    public function settingsTwilioSid() 
    {
        $twilio = $this->find()->where(['name' => 'twiliosid'])->first();
        return $twilio->value;
    }
    public function settingsTwilioToken() 
    {
        $twilio = $this->find()->where(['name' => 'twiliotoken'])->first();
        return $twilio->value;
    }
    public function settingsLdJson() 
    {
        $ldJson = $this->find()->where(['name' => 'ldjson'])->first();
        return $ldJson->value;
    }
    public function settingsMailchimpApi() 
    {
        $api = $this->find()->where(['name' => 'mailchimpapikey'])->first();
        return $api->value;
    }
    public function settingsMailchimpListId() 
    {
        $list = $this->find()->where(['name' => 'mailchimplistid'])->first();
        return $list->value;
    }
}