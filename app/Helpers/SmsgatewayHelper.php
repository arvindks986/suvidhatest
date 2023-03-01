<?php 
// Code within app\Helpers\Helper.php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class SmsgatewayHelper
{

	public static $username="ECISMS-ICT"; //username of the department
	public static $password="ict@1234567"; //password of the department
	public static $senderid="ecisms"; //senderid of the deparment
	public static $deptSecureKey= "93e36092-b1a0-4f0a-9084-4d0eb84f6744"; //departsecure key for encryption of message...
	
	//Function to send single sms	
	public static function sendSingleSMS($message, $mobileno){
		$encryp_password=sha1(trim(self::$password));
		$key=hash('sha512',trim(self::$username).trim(self::$senderid).trim($message).trim(self::$deptSecureKey));
		  
		 $data = array(
		 "username" => trim(self::$username),
		 "password" => trim($encryp_password),
		 "senderid" => trim(self::$senderid),
		 "content" => trim($message),
		 "smsservicetype" =>"singlemsg",
		 "mobileno" =>trim($mobileno),
		 "key" => trim($key)
		 );
		 //echo "<pre/>"; print_r($data);
		 $response = SmsgatewayHelper::post_to_url("https://msdgweb.mgov.gov.in/esms/sendsmsrequest",$data);
		 // $response = post_to_url("https://msdgweb.mgov.gov.in/esms/sendsmsrequest",$data); //calling post_to_url to send sms
		  return $response;
	 }
	
	//Function to send otpsms
	public static function sendOtpSMS($message, $mobileno){
		//echo 'UNAME->'. self::$username;
		$encryp_password=sha1(trim(self::$password));
		$key=hash('sha512',trim(self::$username).trim(self::$senderid).trim($message).trim(self::$deptSecureKey));
		 
		$data = array(
		"username" => trim(self::$username),
		"password" => trim($encryp_password),
		"senderid" => trim(self::$senderid),
		"content" => trim($message),
		"smsservicetype" =>"otpmsg",
		"mobileno" =>trim($mobileno),
		"key" => trim($key)
		);
		//echo "<pre/>"; print_r($data);die;
		$response = SmsgatewayHelper::post_to_url("https://msdgweb.mgov.gov.in/esms/sendsmsrequest",$data);
		//$response =post_to_url("https://msdgweb.mgov.gov.in/esms/sendsmsrequest",$data); //calling post_to_url to send otpsms
		return $response;
	}

	public static function post_to_url($url, $data) {
		$fields = '';
		foreach($data as $key => $value) {
		   $fields .= $key . '=' . $value . '&';
		}
	   rtrim($fields, '&');
	   $post = curl_init();
		curl_setopt($post,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($post, CURLOPT_URL, $url);
		curl_setopt($post, CURLOPT_POST, count($data));
		curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($post); //result from mobile seva server
		return $result; //output from server displayed
		curl_close($post);
	}
	
	
	public static function gupshup($mobile_number,$message){
		 $url= 'http://enterprise.smsgupshup.com/GatewayAPI/rest?';

		$data = array('method' => 'SendMessage',
					 'send_to' => trim($mobile_number),
					 'msg' => trim($message),
					 'msg_type' => 'TEXT',
					 'userid' => '2000184878',
					 'auth_scheme' => 'plain',
					 'password' => 'pVkyKGef',
					 'v' => '1.1',
					 'format' => 'text',);

		$fields = '';
        foreach($data as $key => $value) {
           $fields .= $key . '=' . $value . '&';
        }
        rtrim($fields, '&');
        $post = curl_init();
        curl_setopt($post,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($post); //result from mobile seva server
        //dd($result); //output from server displayed
        curl_close($post);


	}
}