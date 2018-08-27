<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loaddata {

	/**
	 * CI Instance
	 * @var CI_Controller
	 */
	private $_ci;
	private $enc_key;
	/**
	 * Constructor.
	 * @param array $config
	 */
	public function __construct($config = array()) {
		$this->_ci = &get_instance();
		$this->_ci->load->model('SocialappModel');
		$this->loadConst();
		$this->enc_key = GEKEY;
	}

	public function loadConst() {
		$googleAppData = $this->_ci->SocialappModel->getSocialAppData('google');
		$client_id = com_arrIndex($googleAppData, 'client_id');
		$client_secret = com_arrIndex($googleAppData, 'client_secret');
		if (!defined('GOOGLE_CLIENT_ID')) {
			define('GOOGLE_CLIENT_ID', $client_id);
		}
		if (!defined('GOOGLE_CLIENT_SECRET')) {
			define('GOOGLE_CLIENT_SECRET', $client_secret);
		}

		$googleAdWordAppData = $this->_ci->SocialappModel->getSocialAppData('google-adword');
		$client_id = com_arrIndex($googleAdWordAppData, 'client_id');
		$client_secret = com_arrIndex($googleAdWordAppData, 'client_secret');
		if (!defined('GOOGLE_ADWORD_MANAGERACC')) {
			define('GOOGLE_ADWORD_MANAGERACC', $client_id);
		}
		if (!defined('GOOGLE_ADWORD_DEV_TOKEN')) {
			define('GOOGLE_ADWORD_DEV_TOKEN', $client_secret);
		}

		$trelloAppData = $this->_ci->SocialappModel->getSocialAppData('trello');
		$trello_id = com_arrIndex($trelloAppData, 'client_id');
		$trello_secret = com_arrIndex($trelloAppData, 'client_secret');
		if (!defined('TRELLO_DEV_KEY')) {
			define('TRELLO_DEV_KEY', $trello_id);
		}
		if (!defined('TRELLO_SECRET_KEY')) {
			define('TRELLO_SECRET_KEY', $trello_secret);
		}

		$rankAppData = $this->_ci->SocialappModel->getSocialAppData('rankinity');
		$rank_key = com_arrIndex($rankAppData, 'client_id');
		if (!defined('RANKINITY_KEY')) {
			define('RANKINITY_KEY', $rank_key);
		}
	}

	public function getGoogleClient($prod, $profId, $extra_state = "") {
		$scopes = array();
		$rName = "";
		if ($prod == 'analytic') {
			$rName = "Analytic";
			$scopes[] = "https://www.googleapis.com/auth/analytics";
			// $scopes[] = "https://www.googleapis.com/auth/gmail.readonly";
			$scopes[] = "https://www.googleapis.com/auth/analytics.edit";
			$scopes[] = "https://www.googleapis.com/auth/analytics.provision";
			$scopes[] = "https://www.googleapis.com/auth/analytics.manage.users";
		} else if ($prod == 'adwords') {
			$rName = "Adwords";
			// $scopes[] = "https://www.googleapis.com/auth/gmail.readonly";
			$scopes[] = "https://www.googleapis.com/auth/adwords";
		} else if ($prod == 'mbusiness') {
			$rName = "Google Business";
			// $scopes[] = "https://www.googleapis.com/auth/gmail.readonly";
			$scopes[] = "https://www.googleapis.com/auth/plus.business.manage";
		}
		$redirect_uri = base_url('social/verify_google');
		$state = 'PROD:' . $prod . '|PID:' . $profId;
		if ($extra_state) {
			$state .= $extra_state;
		}
		$state = $this->_ci->encrypt->encode($state, $this->enc_key);
		$client = new Google_Client();
		$client->setApplicationName("$rName Report");
		$client->setAccessType("offline");
		$client->setApprovalPrompt("force");
		$client->setState($state);
		$client->setClientId(GOOGLE_CLIENT_ID);
		$client->setClientSecret(GOOGLE_CLIENT_SECRET);
		$client->setRedirectUri($redirect_uri);
		$client->addScope($scopes);
		return $client;
	}

	public function updateGoogleTokens($getClient = false, $opt = array()) {
		$profId = $opt['profId'];
		$prodType = $opt['prod'];
		$access_token = $opt['access_token'];
		$refresh_token = $opt['refresh_token'];
		$redirect_uri = base_url('social/verify_google');
		$client = $this->getGoogleClient($prodType, $profId);
		$client->setAccessToken($access_token);
		$tokenExpired = $client->isAccessTokenExpired();
		$profToken = array();
		if ($tokenExpired) {
			$client->fetchAccessTokenWithRefreshToken($refresh_token);
			$access_token = $client->getAccessToken();			
			$profToken = $this->_ci->SocialappModel
							->updateGoogleTokens($prodType, $access_token, $refresh_token, $profId);
		}
		if ($getClient) {
			$out = array();
			$out['client'] = $client;
			$out['access_token'] = $access_token;
			$out['refresh_token'] = $refresh_token;
			$out['profile_detail'] = $profToken;
			return $out;
		}
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
			$this->_ci->SocialappModel->updateGoogleAdwordAccessToken($access_token);
			com_update_session();
		}
	}
}