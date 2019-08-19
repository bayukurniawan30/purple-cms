<?php

namespace App\Purple;

use Cake\Http\Client;
use Cake\Log\Log;
use App\Purple\PurpleProjectGlobal;

class PurpleProjectApi 
{
	private function apiPath() 
	{
		$purpleGlobal = new PurpleProjectGlobal();
		$apiPath      = $purpleGlobal->apiDomain;
		return $apiPath;
	} 
	public function verifyEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$purpleGlobal = new PurpleProjectGlobal();
			$checkConnection = $purpleGlobal->isConnected();

			if ($checkConnection == true) {
				$http         = new Client();

		        // Check email is valid or not
		        $response     = $http->get($this->apiPath() . '/verify/email/'.$email.'.json');
		        $verifyResult = $response->body();
		        $decodeResult = json_decode($verifyResult, true);

		        if ($decodeResult['message'] == 'success' && $decodeResult['isValid'] == true) {
		        	return true;
		        }
		        else {
		        	return false;
		        }
		    }
		    else {
		    	return true;
		    }
		}
		else {
			return false;
		}
	}
	public function sendEmailAdministrativeSetup($key, $dashboardLink, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/administrative-setup', 
								[
									'key'			=> $key,
									'dashboardLink' => $dashboardLink,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
	public function sendEmailForgotPassword($key, $resetLink, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/forgot-password', 
								[
									'key'        => $key,
									'resetLink'  => $resetLink,
									'userData'   => $userData,
									'senderData' => $senderData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
	public function sendEmailNewPassword($key, $dashboardLink, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/new-password', 
								[
									'key'           => $key,
									'dashboardLink' => $dashboardLink,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
	public function sendEmailNewUser($key, $dashboardLink, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/new-user', 
								[
									'key'			=> $key,
									'dashboardLink' => $dashboardLink,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
	public function sendEmailDeleteUser($key, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/delete-user', 
								[
									'key'			=> $key,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
	public function sendEmailPostComment($key, $dashboardLink, $userData, $post, $commentData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/post-comment', 
								[
									'key'			=> $key,
									'dashboardLink' => $dashboardLink,
									'userData'      => $userData,
									'post'          => $post,
									'commentData'   => $commentData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
	public function sendEmailContactMessage($key, $dashboardLink, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/contact', 
								[
									'key'			=> $key,
									'dashboardLink' => $dashboardLink,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
	public function sendEmailOnlineWebsite($key, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/online-website', 
								[
									'key'			=> $key,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
	public function sendEmailUserVerification($key, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/user-verification', 
								[
									'key'			=> $key,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->body();
	        $decodeResult = json_decode($verifyResult, true);

	        // Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return true;
	    }
	}
}