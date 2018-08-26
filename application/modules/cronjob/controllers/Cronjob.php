<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cronjob extends CI_Controller {

	private $client_id;
	private $client_secret;

	public function __construct(){
		parent::__construct();
		$googleAppData = $this->SocialappModel->getSocialAppData( 'google' );
		$this->client_id = com_arrIndex($googleAppData, 'client_id');
		$this->client_secret = com_arrIndex($googleAppData, 'client_secret');		
	}	

	public function updateGoogleToken(){
		$curDate = date('Y-m-d h:i:s', time());
		$gtokens = $this->db->select( 'id, google_access_token, google_token_expiration_time' )
						->from( 'users' )
						->where( 'google_token_expiration_time <= ', $curDate )
						->get()->result_array();
		if( $gtokens ){
				$redirect_uri = base_url('social/verify_google');
			foreach( $gtokens as $gtoken ){
				$accountId = $gtoken[ 'id' ];
				$access_token = $gtoken[ 'google_access_token' ];
		        $client = new Google_Client();
		        $client->setApplicationName("Analytics Report");
		        $client->setAccessType("offline");
		        $client->setClientId( $this->client_id );
		        $client->setClientSecret( $this->client_secret );
		        $client->setRedirectUri($redirect_uri);
		        $client->setAccessToken($access_token);
		        $client->addScope("email");
		        $client->addScope("profile");
		        $client->addScope("https://www.googleapis.com/auth/analytics");
		        $client->addScope("https://www.googleapis.com/auth/analytics.edit");
				$client->addScope("https://www.googleapis.com/auth/analytics.manage.users");
				$client->addScope("https://www.googleapis.com/auth/analytics.provision");
			    $client->fetchAccessTokenWithRefreshToken();
			    $access_token = $client->getAccessToken();
	        	$this->SocialappModel->updateGoogleAccessToken( $access_token, $accountId);
			}
		}
	}

	public function forceRefreshToken( $accId ){
		$gtoken = $this->db->select( 'id, google_access_token, google_token_expiration_time' )
						->from( 'users' )
						->where( 'id', $accId )
						->get()->row_array();
		$redirect_uri = base_url('social/verify_google');
		$accountId = $gtoken[ 'id' ];
		$access_token = $gtoken[ 'google_access_token' ];
		com_e( json_decode($access_token), 0 );
        $client = new Google_Client();
        $client->setApplicationName("Analytics Report");
        $client->setAccessType("offline");
        $client->setClientId( $this->client_id );
        $client->setClientSecret( $this->client_secret );
        $client->setRedirectUri($redirect_uri);
        $client->setAccessToken($access_token);
        $client->addScope("email");
        $client->addScope("profile");
        $client->addScope("https://www.googleapis.com/auth/analytics");
        $client->addScope("https://www.googleapis.com/auth/analytics.edit");
		$client->addScope("https://www.googleapis.com/auth/analytics.manage.users");
		$client->addScope("https://www.googleapis.com/auth/analytics.provision");
	    $client->fetchAccessTokenWithRefreshToken();
	    $access_token = $client->getAccessToken();
	    $refresh_token = $client->getRefreshToken();
    	$this->SocialappModel->updateGoogleAccessToken( $access_token, $accountId);
	    com_e($access_token, 0);
	    com_e( "Refresh Token ==========", 0 );
	    com_e($refresh_token, 0);
		$gtoken = $this->db->select( 'id, google_access_token, google_token_expiration_time' )
						->from( 'users' )
						->where( 'id', $accId )
						->get()->row_array();
		$access_token = $gtoken[ 'google_access_token' ];
		com_e($access_token, 0);
	}
}