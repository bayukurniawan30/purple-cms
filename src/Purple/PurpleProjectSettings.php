<?php

namespace App\Purple;

use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\Http\Session;
use DateTimeZone;
use DateTime;

class PurpleProjectSettings 
{
	public function timezone($return = 'area') 
	{
		$query = TableRegistry::get('Settings')->find()->where(['name' => 'timezone'])->first();
		
		if ($return == 'area') {
			$area = preg_replace("/\([^)]+\)/","", $query->value);
			return trim($area);
		}
        elseif ($return == 'time') {
            $area = preg_replace("/\([^)]+\)/","", $query->value);
            $area = trim($area);
            $replace = str_replace($area, '', $query->value);
            $replace = str_replace('(UTC', '', $replace);
            $replace = str_replace(')', '', $replace);
            return trim($replace);
        }
		else {
			return $query->value;
		}
	}
    public function generateTimezoneList() {
        static $regions = array(
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        );
        $timezones = array();
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }
        $timezone_offsets = array();
        foreach ($timezones as $timezone){
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
        }
        //Sort timezone by offset
        asort($timezone_offsets);
        $timezone_list = array();
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate( 'H:i', abs($offset) );
            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }
        return $timezone_list;
    }
    public function checkUserLoggedIn()
    {
        $session = new Session();
        $sessionHost     = $session->read('Admin.host');
        $sessionID       = $session->read('Admin.id');
        $sessionPassword = $session->read('Admin.password');

        if (!$session->check('Admin.id')) {
            return false;
        }
        else {
            return true;
        }
    }
    public function maintenanceMode()
    {
        $query = TableRegistry::get('Settings')->find()->where(['name' => 'comingsoon'])->first();
        return $query->value;
    }
}