<?php
// src/View/Helper/PurpleHelper.php
namespace App\View\Helper;

use Cake\View\Helper;

class PurpleHelper extends Helper
{
    public function readableFileSize($bytes, $precision = 2)
    {
    	$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
    public function shortenNumber($n, $precision = 2) 
    {
        if ($n < 1000) { 
            $n_format = number_format($n);
        }
        elseif ($n < 1000000) {
            // Anything less than a million
            $n_format = number_format($n / 1000, $precision) . 'K';
        }
        elseif ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, $precision) . 'M';
        }
        else {
            // At least a billion
            $n_format = number_format($n / 1000000000, $precision) . 'B';
        }
        return $n_format;
    }
    public function notificationCounter($number) 
    {
        if ($number > 10) {
            return '10+';
        }
        else {
            return $number;
        }
    }
    public function plural($number, $verb, $addition = 's', $shorten = false) 
    {
        if ($number == 0) {
            return 'No ' . $verb;
        }
        elseif($number == 1) {
            return '1 ' . $verb;
        }
        else {
            if ($shorten == false) {
                return $number . $verb . $addition;
            }
            else {
                return $this->shortenNumber($number) . ' ' . $verb . $addition;
            }
        }
    }
    public function getAllFuncInHtml($html)
    {
        if (strpos($html, '{{function|') !== false && strpos($html, '}}') !== false) {
            preg_match_all('/{{(.*?)}}/', $html, $matches);
            return $matches[1];
        }
        else {
            return false;
        }
    }
}