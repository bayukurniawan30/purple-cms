<?php
// src/View/Helper/PurpleHelper.php
namespace App\View\Helper;

use Cake\View\Helper;

class PurpleHelper extends Helper
{
    public function readableFileSize(int $bytes, int $precision = 2): string
    {
    	$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
    public function shortenNumber(int $number, int $precision = 2): string
    {
        if ($number < 1000) { 
            $numberFormat = number_format($number);
        }
        elseif ($number < 1000000) {
            // Anything less than a million
            $numberFormat = number_format($number / 1000, $precision) . 'K';
        }
        elseif ($number < 1000000000) {
            // Anything less than a billion
            $numberFormat = number_format($number / 1000000, $precision) . 'M';
        }
        else {
            // At least a billion
            $numberFormat = number_format($number / 1000000000, $precision) . 'B';
        }
        return $numberFormat;
    }
    public function notificationCounter(int $number): string 
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
                return $number . ' ' . $verb . $addition;
            }
            else {
                return $this->shortenNumber($number) . ' ' . $verb . $addition;
            }
        }
    }
    public function getAllFuncInHtml(string $html) 
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