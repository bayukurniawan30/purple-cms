<?php

namespace App\Purple;

use Cake\Filesystem\File;
use Cake\Http\ServerRequest;
use \Detection\MobileDetect;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class PurpleProjectGlobal 
{
	public function databaseInfo() 
	{
		$key     = CIPHER;
		$file    = new File(__DIR__ . DS . '..' . DS . '..' . DS . 'config' . DS . 'database.php');
		$content = $file->read();
		
		if ($content == 'default') {
			return 'default';
		}
		else {
			$decrypted        = \Dcrypt\Aes::decrypt($content, CIPHER);
			$explodeLine      = explode(',', $decrypted);
			$databaseName     = $explodeLine[0];
			$databaseUser     = $explodeLine[1];
			$databasePassword = $explodeLine[2];

			$databaseInfo     = array(
				'name'     => $databaseName,
				'user'     => $databaseUser,
				'password' => $databasePassword
			);
			return $databaseInfo;
		}
	}
	public function productionKeyInfo() 
	{
		$file    = new File(__DIR__ . DS . '..' . DS . '..' . DS . 'config' . DS . 'production_key.php');
		$content = $file->read();
		
		if ($content == '') {
			return 'empty';
		}
		else {
			return 'filled';
		}
	}
	public function detectOS() 
	{
		$mobileDetect = new MobileDetect();
		$agent        = $_SERVER['HTTP_USER_AGENT'];
		$version      = '';
		$codeName     = '';
		$os           = 'Unknown OS';
		
		foreach ($mobileDetect->getOperatingSystems() as $name => $regex) {
			$check = $mobileDetect->version($name);
			if ($check !== false) { 
				$os = $name . ' ' . $check; 
			}
			break;
		}

		if ($mobileDetect->isAndroidOS()) {
			if ($mobileDetect->version('Android') !== false) {
				$version = ' ' . $mobileDetect->version('Android');
				switch (true) {
                    case $mobileDetect->version('Android') >= 8.0: $codeName = ' (Oreo)'; break;
					case $mobileDetect->version('Android') >= 7.0: $codeName = ' (Nougat)'; break;
                    case $mobileDetect->version('Android') >= 6.0: $codeName = ' (Marshmallow)'; break;
                    case $mobileDetect->version('Android') >= 5.0: $codeName = ' (Lollipop)'; break;
					case $mobileDetect->version('Android') >= 4.4: $codeName = ' (KitKat)'; break;
					case $mobileDetect->version('Android') >= 4.1: $codeName = ' (Jelly Bean)'; break;
					case $mobileDetect->version('Android') >= 4.0: $codeName = ' (Ice Cream Sandwich)'; break;
					case $mobileDetect->version('Android') >= 3.0: $codeName = ' (Honeycomb)'; break;
					case $mobileDetect->version('Android') >= 2.3: $codeName = ' (Gingerbread)'; break;
					case $mobileDetect->version('Android') >= 2.2: $codeName = ' (Froyo)'; break;
					case $mobileDetect->version('Android') >= 2.0: $codeName = ' (Eclair)'; break;
					case $mobileDetect->version('Android') >= 1.6: $codeName = ' (Donut)'; break;
					case $mobileDetect->version('Android') >= 1.5: $codeName = ' (Cupcake)'; break;
					default: $codeName = ''; break;
				}
			}

			$os = 'Android' . $version . $codeName;
		} 
		elseif (preg_match('/Linux/', $agent)) {
			$os = 'Linux';
		} 
		elseif (preg_match('/Mac OS X/', $agent)) {
			if (preg_match('/Mac OS X 10_13/', $agent) || preg_match('/Mac OS X 10.13/', $agent)) {
				$os = 'OS X (High Sierra)';
			} elseif (preg_match('/Mac OS X 10_12/', $agent) || preg_match('/Mac OS X 10.12/', $agent)) {
				$os = 'OS X (Sierra)';
			} elseif (preg_match('/Mac OS X 10_11/', $agent) || preg_match('/Mac OS X 10.11/', $agent)) {
				$os = 'OS X (El Capitan)';
			} elseif (preg_match('/Mac OS X 10_10/', $agent) || preg_match('/Mac OS X 10.10/', $agent)) {
				$os = 'OS X (Yosemite)';
			} elseif (preg_match('/Mac OS X 10_9/', $agent) || preg_match('/Mac OS X 10.9/', $agent)) {
				$os = 'OS X (Mavericks)';
			} elseif (preg_match('/Mac OS X 10_8/', $agent) || preg_match('/Mac OS X 10.8/', $agent)) {
				$os = 'OS X (Mountain Lion)';
			} elseif (preg_match('/Mac OS X 10_7/', $agent) || preg_match('/Mac OS X 10.7/', $agent)) {
				$os = 'Mac OS X (Lion)';
			} elseif (preg_match('/Mac OS X 10_6/', $agent) || preg_match('/Mac OS X 10.6/', $agent)) {
				$os = 'Mac OS X (Snow Leopard)';
			} elseif (preg_match('/Mac OS X 10_5/', $agent) || preg_match('/Mac OS X 10.5/', $agent)) {
				$os = 'Mac OS X (Leopard)';
			} elseif (preg_match('/Mac OS X 10_4/', $agent) || preg_match('/Mac OS X 10.4/', $agent)) {
				$os = 'Mac OS X (Tiger)';
			} elseif (preg_match('/Mac OS X 10_3/', $agent) || preg_match('/Mac OS X 10.3/', $agent)) {
				$os = 'Mac OS X (Panther)';
			} elseif (preg_match('/Mac OS X 10_2/', $agent) || preg_match('/Mac OS X 10.2/', $agent)) {
				$os = 'Mac OS X (Jaguar)';
			} elseif (preg_match('/Mac OS X 10_1/', $agent) || preg_match('/Mac OS X 10.1/', $agent)) {
				$os = 'Mac OS X (Puma)';
			} elseif (preg_match('/Mac OS X 10/', $agent)) {
				$os = 'Mac OS X (Cheetah)';
			}
		} 
		elseif ($mobileDetect->isWindowsPhoneOS()) {
			//$icon = 'windowsphone8';
			if ($mobileDetect->version('WindowsPhone') !== false) {
				$version = ' ' . $mobileDetect->version('WindowsPhoneOS');
				/*switch (true) {
					case $version >= 8: $icon = 'windowsphone8'; break;
					case $version >= 7: $icon = 'windowsphone7'; break;
					default: $icon = 'windowsphone8'; break;
				}*/
			}

			$os = 'Windows Phone' . $version;
		} 
		elseif ($mobileDetect->version('Windows NT') !== false) {
			switch ($mobileDetect->version('Windows NT')) {
				case 10.0: $codeName = ' 10'; break;
				case 6.3: $codeName = ' 8.1'; break;
				case 6.2: $codeName = ' 8'; break;
				case 6.1: $codeName = ' 7'; break;
				case 6.0: $codeName = ' Vista'; break;
				case 5.2: $codeName = ' Server 2003; Windows XP x64 Edition'; break;
				case 5.1: $codeName = ' XP'; break;
				case 5.01: $codeName = ' 2000, Service Pack 1 (SP1)'; break;
				case 5.0: $codeName = ' 2000'; break;
				case 4.0: $codeName = ' NT 4.0'; break;
				default: $codeName = ' NT v' . $mobileDetect->version('Windows NT'); break;
			}

			$os = 'Windows' . $codeName;
		} 
		elseif ($mobileDetect->isiOS()) {
            if ($mobileDetect->isTablet()) {
                $version = ' ' . $mobileDetect->version('iPad');
            } 
            else {
                $version = ' ' . $mobileDetect->version('iPhone');
            }

            $os = 'iOS' . $version;
        }
		return $os;
	}
	public function detectDevice() 
	{
		$mobileDetect = new MobileDetect();
		return ($mobileDetect->isMobile() ? ($mobileDetect->isTablet() ? 'Tablet' : 'Phone') : 'Computer');
	}
	public function detectBrowser() 
	{
		$mobileDetect = new MobileDetect();
		$agent        = $_SERVER['HTTP_USER_AGENT'];
		$browser      = 'Unknown Browser';
		if (preg_match('/Edge\/\d+/', $agent)) {
			#$browser = 'Microsoft Edge ' . (floatval($mobileDetect->version('Edge')) + 8);
			$browser = 'Microsoft Edge ' . str_replace('12', '20', $mobileDetect->version('Edge'));
		} 
		elseif ($mobileDetect->version('Trident') !== false && preg_match('/rv:11.0/', $agent)) {
			$browser = 'Internet Explorer 11';
		} 
		else {
			$found = false;
			
			foreach($mobileDetect->getBrowsers() as $name => $regex) {
				$check = $mobileDetect->version($name);
				if ($check !== false && !$found) {
					$browser = $name . ' ' . $check;
					$found = true;
				}
			}
		}
		return $browser;
	}
	public function isConnected()
	{
		// port (try 80 or 443)
	    $connected = @fsockopen("www.google.com", 80); 
	                                        
	    if ($connected) {
	        $isConn = true; 
	        fclose($connected);
	    }
	    else {
	        $isConn = false;
	    }

	    return $isConn;
	}
	public function protocol() {
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$protocol = "https://";
		}
		else {
			$protocol = "http://";
		}

		return $protocol;
	}
	public function reformatLdJson($schema) {
		$output = str_replace(':\/\/', '://', htmlentities($schema));
		$output = str_replace('\/', '/', $output);
		
		return $output;
	}
	public function shortenNumber($number, $precision = 2) 
	{
		if ($number < 1000) { 
	        $n_format = number_format($number);
		}
	    elseif ($number < 1000000) {
	        // Anything less than a million
	        $n_format = number_format($number / 1000, $precision) . 'K';
	    }
	    elseif ($number < 1000000000) {
	        // Anything less than a billion
	        $n_format = number_format($number / 1000000, $precision) . 'M';
	    }
	    else {
	        // At least a billion
	        $n_format = number_format($number / 1000000000, $precision) . 'B';
	    }

	    return $n_format;
	}
	public function truncateString($string, $limit = 1) {
		return preg_replace('/((\w+\W*){'.($limit-1).'}(\w+))(.*)/', '${1}', $string);   
	}
	public function greetings() 
	{
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		$hour = date('H', strtotime($date));
		
		$randomGreetings = array('Hello', 'Howdy', 'Hai', 'Welcome', 'Good to see u');

		if( $hour > 6 && $hour <= 11) {
			$randomGreetings[] = 'Good Morning';
			$newArray          = array_rand($randomGreetings);
			$data = [
				'icon'     => 'mdi-weather-sunset-up',
				'greeting' => $randomGreetings[array_rand($randomGreetings)]
			];
		}
		else if($hour > 11 && $hour <= 16) {
			$randomGreetings[] = 'Good Afternoon';
			$newArray          = array_rand($randomGreetings);
		  	$data = [
				'icon'     => 'mdi-weather-sunny',
				'greeting' => $randomGreetings[array_rand($randomGreetings)]
			];
		}
		else if($hour > 16 && $hour <= 23) {
			$randomGreetings[] = 'Good Evening';
			$newArray          = array_rand($randomGreetings);
		  	$data = [
				'icon'     => 'mdi-weather-night',
				'greeting' => $randomGreetings[array_rand($randomGreetings)]
			];
		}
		else {
			$randomGreetings[] = 'Good Evening';
			$newArray          = array_rand($randomGreetings);
			$data = [
				'icon'     => 'mdi-weather-night',
				// 'greeting' => "Why aren't you asleep?"
				'greeting' => $randomGreetings[array_rand($randomGreetings)]
			];
		}

		return $data;
	}
	public function showUserValidateErrors($errors)
	{
		$script = '<script>$(document).ready(function() {';
	    foreach ($errors as $key => $value) {
	        $script .= '$("form").find("input[name='.$key.'], select[name='.$key.'], textarea[name='.$key.']").removeClass("parsley-success").addClass("uk-form-danger").attr("uk-tooltip", "';
	        foreach($value as $validate => $validateValue) {
	        	if (end($value) === $validateValue) {
	        		$last = '.';
	        	}
	        	else {
	        		$last = ', ';
	        	}
	        	$script .= $validateValue.$last;
	        }
	    } 
		$script .= '");})</script>';
		return $script;
	}
	public function isRecaptchaPass($status, $score)
	{
		$serverRequest = new ServerRequest();
		$host = $serverRequest->getEnv('HTTP_HOST');
		if ($host == 'localhost') {
			return true;
		}
		else {
			if ($status == 'success' && $score > 0.5) {
				return true;
			}
			else {
				return false;
			}
		}
	}
	public function apiKeyGenerator($length = 32)
	{
		$key = '';
		list($usec, $sec) = explode(' ', microtime());
		mt_srand((float) $sec + ((float) $usec * 100000));
		
		$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

		for ($i = 0; $i < $length; $i++)
		{
			$key .= $inputs{mt_rand(0,61)};
		}
		return $key;
	}
}