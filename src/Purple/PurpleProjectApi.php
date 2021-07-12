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
		        $verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
	public function sendEmailMasterAdminAccount($key, $dashboardLink, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/master-admin-account', 
								[
									'key'			=> $key,
									'dashboardLink' => $dashboardLink,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
			$verifyResult = $response->getStringBody();
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
	public function sendEmailSignInVerification($key, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/sign-in-verification', 
								[
									'key'			=> $key,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->getStringBody();
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
	public function sendEmailCertainVisitors($key, $userData, $senderData)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/action/certain-visitors', 
								[
									'key'			=> $key,
									'userData'      => $userData,
									'senderData'    => $senderData
								]
							);
			$verifyResult = $response->getStringBody();
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
	public function sendVerifyNumberTwoFactorAuth($key, $country, $number)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/verify/2fa/verify-number', 
								[
									'key'	  => $key,
									'country' => $country,
									'number'  => $number
								]
							);
			$verifyResult = $response->getStringBody();
	        $decodeResult = json_decode($verifyResult, true);

	        Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return false;
	    }
	}
	public function sendApproveNumberTwoFactorAuth($key, $code, $number)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/verify/2fa/approve-number', 
								[
									'key'    => $key,
									'code'   => $code,
									'number' => $number
								]
							);
			$verifyResult = $response->getStringBody();
	        $decodeResult = json_decode($verifyResult, true);

	        Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return false;
	    }
	}
	public function sendAuthyRegisterUser($key, $email, $number, $countryCode)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/authy/register-user', 
								[
									'key'	  => $key,
									'email'   => $email,
									'number'  => $number,
									'country' => $countryCode
								]
							);
			$verifyResult = $response->getStringBody();
	        $decodeResult = json_decode($verifyResult, true);

	        Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return $decodeResult['id'];
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return false;
	    }
	}
	public function sendAuthySendSms($key, $id)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/authy/send-sms', 
								[
									'key' => $key,
									'id'  => $id
								]
							);
			$verifyResult = $response->getStringBody();
	        $decodeResult = json_decode($verifyResult, true);

	        Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return false;
	    }
	}
	public function sendAuthyVerifyToken($key, $id, $token)
	{	
		$purpleGlobal = new PurpleProjectGlobal();
		$checkConnection = $purpleGlobal->isConnected();

		if ($checkConnection == true) {
			$http         = new Client();
			$response     = $http->post($this->apiPath() . '/authy/verify-token', 
								[
									'key'   => $key,
									'id'    => $id,
									'token' => $token
								]
							);
			$verifyResult = $response->getStringBody();
	        $decodeResult = json_decode($verifyResult, true);

	        Log::write('debug', $decodeResult);

	        if ($decodeResult['message'] == 'success') {
	        	return true;
	        }
	        else {
	        	return false;
	        }
		}
	    else {
	    	return false;
	    }
	}
}