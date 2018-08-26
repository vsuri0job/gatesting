<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * VCI_Controller extends Controller default controller of codeigniter
 * overriding the default controller. provides additional
 * functionality for layout and views and handles unauthorized user access
 * to admin panel and provides common functionality
 *
 * @author Vince Balrai
 */
class MY_Controller extends CI_Controller {

	public $isLogin = false;

	function __construct() {
		parent::__construct();
		$this->isLogin = $this->session->userdata('id');
		$this->output->enable_profiler(FALSE);
	}

	function isLogin() {
		if (!$this->isLogin) {
			redirect('/');
			exit;
		}
		$this->loaddata->loadConst();
		$this->load->library('Rankinity', RANKINITY_KEY);
	}

	public function curlRequest($opts) {
		$url = $opts['url'];
		// create curl resource
		$ch = curl_init();

		// set url
		curl_setopt($ch, CURLOPT_URL, $url);

		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// $output contains the output string
		$output = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);
		return $output;
		/*
			//API URL
			$url = 'http://www.example.com/api';

			//create a new cURL resource
			$ch = curl_init($url);

			//setup request to send json via POST
			$data = array(
			    'username' => 'codexworld',
			    'password' => '123456'
			);
			$payload = json_encode(array("user" => $data));

			//attach encoded JSON string to the POST fields
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

			//set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

			//return response instead of outputting
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			//execute the POST request
			$result = curl_exec($ch);

			//close cURL resource
			curl_close($ch);
		*/
	}

	public function getGoogleAdwordClient() {
		$scopes = array();
		$scopes[] = "https://www.googleapis.com/auth/adwords";
		$redirect_uri = base_url('social/verify_tmpgoogle');
		$client = new Google_Client();
		$client->setApplicationName("Adwords Report");
		$client->setAccessType("offline");
		$client->setApprovalPrompt("force");
		$client->setClientId(GOOGLE_ADWORD_CLIENT_ID);
		$client->setClientSecret(GOOGLE_ADWORD_CLIENT_SECRET);
		$client->setRedirectUri($redirect_uri);
		$client->addScope($scopes);
		return $client;
	}

	public function updateGoogleAdwordTokens() {
		$access_token = com_user_data('google_adword_access_token');
		$refresh_token = com_user_data('google_adword_refresh_token');
		$redirect_uri = base_url('social/verify_tmpgoogle');
		$client = $this->getGoogleAdwordClient();
		$client->setAccessToken($access_token);
		if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($refresh_token);
			$access_token = $client->getAccessToken();
			$this->SocialappModel->updateGoogleAdwordAccessToken($access_token);
			com_update_session();
		}
	}
}