<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crontool extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('adwords');
		$this->load->library('loaddata');
		$this->load->model('ReportModel');
		$this->load->model('AccountModel');
		$this->load->model('SocialappModel');
	}

	public function index($to = 'World') {
		echo "Hello {$to}!" . PHP_EOL;
	}

	public function UpdateAccountSocialData() {
		$urlProfiles = $this->db->select('account_url_profiles_social_token.*, account_url_profiles.*,
    							account_url_profiles.profile_id as `analytic_prof_id`,
    							account_url_profiles_social_token.profile_id as `profile_id`')
			->from('account_url_profiles')
			->join('account_url_profiles_social_token',
				'account_url_profiles_social_token.profile_id=account_url_profiles.id', 'left')
			->get()->result_array();
		foreach ($urlProfiles as $urlProfile) {
			$urlProfile = $this->checkTokenValid($urlProfile);
			$socialAcc = array('mbusiness' => 'gmb',
				'analytic' => 'analytic',
				'adwords' => 'adword',
				'webmaster' => 'gsc',
			);
			foreach ($socialAcc as $socRef => $fldRef) {
				if (!$urlProfile[$fldRef . "_reset_token"]) {
					if ($socRef == 'mbusiness') {
						$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($urlProfile['id']);
						$this->SocialappModel->updateProfileGBuissData($fetchedProfile, 2);
					}
					if ($socRef == 'analytic') {
						$access_token = $urlProfile[$fldRef . "_access_token"];
						$refresh_token = $urlProfile[$fldRef . "_refresh_token"];
						$view_id = $urlProfile["view_id"];
						$prop_id = $urlProfile["property_id"];
						if ($access_token && $refresh_token && $view_id && $prop_id) {
							$opt = array();
							$opt['prod'] = $socRef;
							$opt['profId'] = $urlProfile['id'];
							$opt['log_user_id'] = $urlProfile['account_id'];
							$opt['access_token'] = $access_token;
							$opt['refresh_token'] = $refresh_token;
							$client_token = $this->loaddata->updateGoogleTokens(true, $opt);
							$client = $client_token['client'];
							$this->getViewData($urlProfile, $client);
						}
					}
					if ($socRef == 'adwords') {
						$access_token = $urlProfile[$fldRef . "_access_token"];
						$refresh_token = $urlProfile[$fldRef . "_refresh_token"];
						if ($access_token && $refresh_token) {
							$this->AccountModel->updateGoogleAdwordsData($urlProfile, $urlProfile['account_id'], 2);
						}
					}
					if ($socRef == 'webmaster') {
						$profDet = $this->AccountModel->getFetchedAccountDetail($urlProfile['id']);
						$this->ReportModel->updateWebMasterData($profDet, 2);
					}
				}
			}

			if ($urlProfile['rankinity_access_token'] &&
				$urlProfile['linked_rankinity_id']) {
				$rankProj = array();
				$rankProj['rankinity_project_id'] = $urlProfile['linked_rankinity_id'];
				$this->AccountModel->linkRankinityAccount($rankinityProj, $urlProfile['id']);
			}
		}
	}

	public function checkTokenValid($profDet) {
		$socialAcc = array('mbusiness' => 'gmb',
			'analytic' => 'analytic',
			'adwords' => 'adword',
			'webmaster' => 'gsc',
		);
		$urlProf = $profDet;
		foreach ($socialAcc as $socRef => $fldRef) {
			$access_token = $profDet["$fldRef_access_token"];
			$refresh_token = $profDet["$fldRef_refresh_token"];
			if ($access_token && $refresh_token) {
				$opt = array();
				$opt['prod'] = $socRef;
				$opt['profId'] = $profDet['id'];
				$opt['log_user_id'] = $profDet['account_id'];
				$opt['access_token'] = $access_token;
				$opt['refresh_token'] = $refresh_token;
				$ctoken = $this->loaddata->updateGoogleTokens(true, $opt);
				$client = $ctoken['client'];
				$access_token = $client->getAccessToken();
				$refresh_token = $client->getRefreshToken();
				$profDet = $ctoken['profile_detail'];
			}
		}
		$urlProf = array_merge($urlProf, $profDet);
		return $urlProf;
	}

	public function getViewData($profileDet, $gClient) {
		$viewId = $profileDet['view_id'];
		$profileId = $profileDet['analytic_prof_id'];
		$propertyId = $profileDet['property_id'];
		$relProfId = $this->input->post('prof_id');
		$prodDet = $profileDet;
		$client = $gClient;
		$analytics = new Google_Service_Analytics($client);
		$this->ReportModel->fetchPropViewAnalyticData($analytics, $viewId, $prodDet['id'], $prodDet['account_id'], 2);
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
}