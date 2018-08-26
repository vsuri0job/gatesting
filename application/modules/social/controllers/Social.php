<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Social extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->library('Loaddata');
		$this->load->library('Adwords');
		$this->load->model('SocialModel');
	}

	public function google($prod, $profId) {
		$ex_state = "";
		$customer_id = $this->input->post('customer_id');
		if ($customer_id) {
			$customer_id = str_replace("-", "", $customer_id);
			$ex_state = '|CUS_ID:' . trim($customer_id);
		}
		$client = $this->loaddata->getGoogleClient($prod, $profId, $ex_state);
		//Send Client Request
		$objOAuthService = new Google_Service_Oauth2($client);
		$authUrl = $client->createAuthUrl();
		header('Location: ' . $authUrl);
	}

	public function googleBusiness() {
		$client = $this->loaddata->getGoogleClient(true);
		//Send Client Request
		$objOAuthService = new Google_Service_Oauth2($client);
		$authUrl = $client->createAuthUrl();
		header('Location: ' . $authUrl);
	}

	public function link_google_adword() {
		$acc_id = $this->input->post('acc_id');
		$out = array();
		$out['success'] = false;
		if ($acc_id) {
			$out['success'] = true;
			$data = array();
			$data['google_adwords_accid'] = $acc_id;
			$this->db->where('id', com_user_data('id'))
				->update('users', $data);
			com_update_session();
		}
		echo json_encode($out);
		exit();
	}

	public function verify_google() {
		$ret_code = $this->input->get('code');
		$state = $this->input->get('state');
		$state = $this->encrypt->decode($state, GEKEY);
		if ($ret_code && $state) {
			$state = explode('|', $state);
			$sD = array('PROD' => '', 'PID' => '');
			$ex = array('CUS_ID' => '');
			foreach ($state as $state_data) {
				$state_data = explode(':', $state_data);
				if (isset($sD[$state_data[0]])) {
					$sD[$state_data[0]] = $state_data[1];
				}
				if (isset($ex[$state_data[0]])) {
					$ex[$state_data[0]] = $state_data[1];
				}
			}
			$client = $this->loaddata->getGoogleClient($sD['PROD'], $sD['PID']);
			$authentication = $client->authenticate($ret_code);
			$access_token = $client->getAccessToken();
			$refresh_token = $client->getRefreshToken();
			if ($access_token) {
				$this->SocialappModel->updateGoogleTokens($sD['PROD'], $access_token, $refresh_token, $sD['PID']);
				if ($sD['PROD'] == 'analytic') {
					$this->fetchAnalyticData($client, $sD['PID']);
				} else if ($sD['PROD'] == 'adwords') {
					$opt = array();
					$opt['prod'] = $sD['PROD'];
					$opt['profId'] = $sD['PID'];
					$opt['access_token'] = $access_token;
					$opt['refresh_token'] = $refresh_token;
					$opt['clientCustomerId'] = $ex["CUS_ID"];
					$opt['log_user_id'] = com_user_data('id');
					$adWordsAccounts = $this->adwords->getList($opt);
					if (is_array($adWordsAccounts) && $adWordsAccounts) {
						$rsWhere = array();
						$rsWhere['prof_id'] = $sD['PID'];
						$rsWhere['log_acc_id'] = com_user_data('id');
						$this->SocialModel->resetAdwordsAccounts($rsWhere, $adWordsAccounts);
						$this->SocialappModel->linkProfileAdword($sD['PID'], $ex["CUS_ID"]);
					} else if (is_string($adWordsAccounts) && $adWordsAccounts) {
						$this->session->set_flashdata('emsg', $adWordsAccounts);
						$this->SocialappModel->updateGoogleTokens($sD['PROD'], $access_token, $refresh_token, $sD['PID'], true);
					}
				} else if ($sD['PROD'] == 'mbusiness') {
					$opt = array();
					$opt['prod'] = $sD['PROD'];
					$opt['profId'] = $sD['PID'];
					$opt['access_token'] = $access_token;
					$opt['refresh_token'] = $refresh_token;
					$opt['log_user_id'] = com_user_data('id');
					$this->update_google_business_list($opt);
				}
				redirect('accounts/list');
				exit;
			}
		}
		redirect('/');
		exit;
	}

	public function resetAdwordList() {
		$adWordsAccounts = $this->adwords->getList();
		$this->SocialModel->resetAdwordsAccounts($adWordsAccounts);
		$this->load->library('user_agent');
		$ref = "/";
		if ($this->agent->is_referral()) {
			$ref = $this->agent->referrer();
		}
		redirect($ref);
		exit;
	}

	private function fetchAnalyticData($client, $profId) {
		$logUserId = com_user_data('id');
		$service = new Google_Service_Oauth2($client);
		$analytics = new Google_Service_Analytics($client);
		$accounts = $analytics->management_accountSummaries->listManagementAccountSummaries();
		$profiles = array();
		$profile_properties = array();
		$profile_property_adass = array();
		$profile_property_views = array();
		$profile_property_view_adata = array();
		foreach ($accounts->getItems() as $index => $item) {
			$GAAccountId = $item->id;
			$profiles[$index]['account_id'] = $logUserId;
			$profiles[$index]['profile_id'] = $GAAccountId;
			$profiles[$index]['profile_name'] = $item->name;
			$profiles[$index]['url_profile_id'] = $profId;
			foreach ($item->webProperties as $propIndex => $property) {
				$propertyId = $property->id;
				$profile_properties[$propertyId]['account_id'] = $logUserId;
				$profile_properties[$propertyId]['profile_id'] = $GAAccountId;
				$profile_properties[$propertyId]['property_id'] = $propertyId;
				$profile_properties[$propertyId]['property_name'] = $property->name;
				$profile_properties[$propertyId]['url_profile_id'] = $profId;
				$profile_properties[$propertyId]['property_website_url'] = $property->websiteUrl;
				$accountsAdwords = $analytics->management_webPropertyAdWordsLinks->listManagementWebPropertyAdWordsLinks($GAAccountId, $propertyId);
				foreach ($property->profiles as $viewItem) {
					$viewId = $viewItem->id;
					$profile_property_views[$viewId]['view_id'] = $viewId;
					$profile_property_views[$viewId]['account_id'] = $logUserId;
					$profile_property_views[$viewId]['property_id'] = $propertyId;
					$profile_property_views[$viewId]['view_name'] = $viewItem->name;
					$profile_property_views[$viewId]['url_profile_id'] = $profId;
					// $ga_data = $this->getPropViewAnalyticData($analytics, $viewId);
					// $profile_property_view_adata = array_merge($profile_property_view_adata, $ga_data);
				}
				if ($accountsAdwords->totalResults) {
					foreach ($accountsAdwords->items as $adwordAss) {
						$profile_property_adass[$adwordAss->id]['account_id'] = $logUserId;
						$profile_property_adass[$adwordAss->id]['property_id'] = $propertyId;
						$profile_property_adass[$adwordAss->id]['url_profile_id'] = $profId;
						$profile_property_adass[$adwordAss->id]['adword_link_id'] = $adwordAss->id;
						$profile_property_adass[$adwordAss->id]['adword_link_name'] = $adwordAss->name;
						$pIds = array();
						if (isset($adwordAss->profileIds)) {
							$pIds = $adwordAss->profileIds;
						}
						$profile_property_adass[$adwordAss->id]['profile_ids'] = implode(',', $pIds);
						$adword_cus_ref = array();
						foreach ($adwordAss->adWordsAccounts as $adWordAcc) {
							$adword_cus_ref[] = $adWordAcc['customerId'];
						}
						$profile_property_adass[$adwordAss->id]['adword_refs'] = implode(',', $adword_cus_ref);
					}
				}
			}
		}
		$this->SocialappModel->emptyAnalyticData($logUserId, $profId);
		$this->SocialModel->addUserGoogleAnalyticsProfiles($profiles);
		$this->SocialModel->addUserGoogleAnalyticsProfileProperties($profile_properties);
		$this->SocialModel->addUserGoogleAnalyticsProfilePropertyView($profile_property_views);
		$this->SocialModel->addUserGoogleAnalyticsProfilePropertyAdwordAssoc($profile_property_adass);
		$this->SocialModel->addUserGoogleAnalyticsProfilePropertyViewGData($profile_property_view_adata);
	}

	private function getPropViewAnalyticData($analytics, $viewId) {
		$months_tstamps = array(
			strtotime("-13 months"),
			strtotime("-12 months"),
			strtotime("-11 months"),
			strtotime("-10 months"),
			strtotime("-9 months"),
			strtotime("-8 months"),
			strtotime("-7 months"),
			strtotime("-6 months"),
			strtotime("-5 months"),
			strtotime("-4 months"),
			strtotime("-3 months"),
			strtotime("-2 months"),
			strtotime("-1 months"),
			time(),
		);
		/*
			,
		*/
		$ga_data = array();
		foreach ($months_tstamps as $mtstamp) {
			$month_ref = date('Y-m', $mtstamp);
			$sday_month = date('Y-m-01', $mtstamp);
			$lday_month = date('Y-m-t', $mtstamp);
			$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
				$sday_month, $lday_month,
				'ga:sessions, ga:users, ga:pageviewsPerSession, ga:avgSessionDuration, ga:bounceRate, ga:avgPageDownloadTime, ga:goalConversionRateAll, ga:goalCompletionsAll');
			$ga_data[$viewId . '-' . $month_ref]['view_id'] = $viewId;
			$ga_data[$viewId . '-' . $month_ref]['month_ref'] = $month_ref;
			$ga_data[$viewId . '-' . $month_ref]['sessions'] = $ga_rstdata->totalsForAllResults['ga:sessions'];
			$ga_data[$viewId . '-' . $month_ref]['users'] = $ga_rstdata->totalsForAllResults['ga:users'];
			$ga_data[$viewId . '-' . $month_ref]['page_view_per_sessions'] = $ga_rstdata->totalsForAllResults['ga:pageviewsPerSession'];
			$ga_data[$viewId . '-' . $month_ref]['avg_session_duration'] = $ga_rstdata->totalsForAllResults['ga:avgSessionDuration'];
			$ga_data[$viewId . '-' . $month_ref]['bounce_rate'] = $ga_rstdata->totalsForAllResults['ga:bounceRate'];
			$ga_data[$viewId . '-' . $month_ref]['avg_page_download_time'] = $ga_rstdata->totalsForAllResults['ga:avgPageDownloadTime'];
			$ga_data[$viewId . '-' . $month_ref]['goal_conversion_rate'] = $ga_rstdata->totalsForAllResults['ga:goalConversionRateAll'];
			$ga_data[$viewId . '-' . $month_ref]['goal_completion_all'] = $ga_rstdata->totalsForAllResults['ga:goalCompletionsAll'];
		}
		return $ga_data;
	}

	public function verify_trello() {
		$profId = $this->input->get('pid');
		$ret_code = $this->input->get('token');
		if ($ret_code && $profId) {
			$this->SocialappModel->updateTrelloAccessToken($ret_code, $profId);
			$this->updateTrelloBoards($ret_code, $profId);
			// $this->updateTrelloBoardCards();
		}
		redirect('accounts/list');
		exit;
	}

	public function updateTrelloBoards($token, $profId) {
		$opt = array();
		$opt['url'] = 'https://api.trello.com/1/members/me/boards?key=' . TRELLO_DEV_KEY . '&token=' . $token;
		$boards = $this->curlRequest($opt);
		$boards = json_decode($boards);
		$boardStack = array();
		foreach ($boards as $bIndex => $board) {
			$boardStack[$bIndex]['board_id'] = $board->id;
			$boardStack[$bIndex]['board_url'] = $board->url;
			$boardStack[$bIndex]['url_profile_id'] = $profId;
			$boardStack[$bIndex]['board_name'] = $board->name;
			$boardStack[$bIndex]['board_closed'] = $board->closed;
			$boardStack[$bIndex]['account_id'] = com_user_data('id');
		}
		if ($boardStack) {
			$this->SocialappModel->updateTrelloBoards($boardStack);
		}
	}

	public function updateTrelloBoardCards() {
		$boards = $this->SocialappModel->getTrelloBoards();
		$boardCards = array();
		foreach ($boards as $bIndex => $board) {
			$opt = array();
			$opt['url'] = 'https://api.trello.com/1/boards/' . $board['board_id'] . '/cards?key=' . TRELLO_DEV_KEY . '&token=' .
			com_user_data('trello_access_token');
			$cards = $this->curlRequest($opt);
			com_e(json_decode($cards));
		}
	}

	public function updateGoogleProfiles() {
		$client = $this->getGoogleClient();
		$access_token = com_user_data('google_access_token');
		$refresh_token = com_user_data('google_refresh_token');
		$client->setAccessToken($access_token);
		if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($refresh_token);
			$access_token = $client->getAccessToken();
			$this->SocialappModel->updateGoogleAccessToken($access_token);
			com_update_session();
		}
		$this->fetchUserGoogleData($client);
		$adWordsAccounts = $this->adwords->getList();
		$this->SocialModel->resetAdwordsAccounts($adWordsAccounts);
		redirect('report/ganalyticreport');
		exit;
	}

	public function index() {
		$this->resetAdwordConfig();
	}

	public function update_google_business_list($opt = array()) {
		$profId = $opt['profId'];
		$log_user_id = $opt['log_user_id'];
		// $prodType = $opt[ 'prod' ];
		// $access_token = $opt[ 'access_token' ];
		// $refresh_token = $opt[ 'refresh_token' ];
		$ctoken = $this->loaddata->updateGoogleTokens(true, $opt);
		$client = $ctoken['client'];
		$objOAuthService = new Google_Service_Oauth2($client);
		$authUrl = $client->createAuthUrl();
		$this->load->library('Google_Service_MyBusiness', $client, 'GMBS');
		$gList = $this->GMBS->accounts->listAccounts();
		$gAList = $gList->accounts;
		$gADataBPages = array();
		$gADataBPagesLocations = array();
		foreach ($gAList as $key => $value) {
			$gADataBPages[$value->name]['account_id'] = $log_user_id;
			$gADataBPages[$value->name]['url_profile_id'] = $profId;
			$gADataBPages[$value->name]['account_page_name'] = $value->accountName;
			$gADataBPages[$value->name]['account_page_name_id'] = $value->name;
			$gADataBPages[$value->name]['account_page_name_url'] = $value->name;
			$locations = $this->GMBS->accounts_locations->listAccountsLocations($value->name);
			foreach ($locations->locations as $lk => $lData) {
				$lgt = $lat = $wsurl = $placeId = "";
				if (isset($lData->locationKey->placeId)) {
					$placeId = $lData->locationKey->placeId;
				}
				if (isset($lData->latlng->latitude)) {
					$lat = $lData->latlng->latitude;
				}
				if (isset($lData->latlng->longitude)) {
					$lgt = $lData->latlng->longitude;
				}
				if (isset($lData->websiteUrl)) {
					$wsurl = $lData->websiteUrl;
				}
				$gADataBPagesLocations[$lData->name]['account_id'] = $log_user_id;
				$gADataBPagesLocations[$lData->name]['url_profile_id'] = $profId;
				$gADataBPagesLocations[$lData->name]['website_url'] = $wsurl;
				$gADataBPagesLocations[$lData->name]['latitude'] = $lat;
				$gADataBPagesLocations[$lData->name]['longitude'] = $lgt;
				$gADataBPagesLocations[$lData->name]['account_page_name_ref'] = $value->name;
				$gADataBPagesLocations[$lData->name]['account_page_location_id'] = $lData->locationKey->placeId;
				$gADataBPagesLocations[$lData->name]['account_page_location_place'] = $lData->locationName;
			}
		}
		$this->SocialappModel->resetBusinessProfData($log_user_id, $profId);
		$this->SocialappModel->insertBusinessProfData($gADataBPages, $gADataBPagesLocations);
	}

	public function link_rankinity($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		$rank_token = $this->input->post('rankinity_token');
		if ($fetchedProfile && $rank_token && !$fetchedProfile['rankinity_access_token']) {
			$this->SocialappModel->updateRankinityAccessToken($rank_token, $fetchedProfile['id']);
			$this->updateRankinityProjects($fetchedProfile['id']);
		}
		redirect('accounts/list');
		exit();
	}

	public function link_rankinity_project($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		if ($fetchedProfile && $fetchedProfile['rankinity_access_token']) {
			$projects = $this->SocialModel->getRankinityProjects($fetchedProfile['id']);
			$projects = com_makelist($projects, 'rankinity_project_id', 'rankinity_project_name,rankinity_project_url', false);
			$inner = array();
			$shell = array();
			$log_user_id = com_user_data('id');
			$inner['profile'] = $fetchedProfile;
			$inner['projects'] = $projects;
			$shell['page_title'] = 'Link Rankinity';
			$shell['content'] = $this->load->view('link_rankinity_proj', $inner, true);
			$shell['footer_js'] = $this->load->view('link_rankinity_proj_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {
			redirect('accounts/list');
			exit();
		}
	}

	public function updateRankinityProjects($profId) {
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		if ($fetchedProfile) {
			$token = $fetchedProfile['rankinity_access_token'];
			$this->rankinity->setApiKey($token);
			$ttlItems = 0;
			$fetched = 0;
			$opt = [];
			$rankProjects = array();
			// $opt[ 'page' ] = 0;
			do {
				$projects = $this->rankinity->getProjects($opt);
				$ttlItems = $projects['meta']['total'];
				$fetched += count($projects['items']);
				if ($projects['items']) {
					foreach ($projects['items'] as $iK => $Item) {
						$rankProjects[$iK]['url_profile_id'] = $profId;
						$rankProjects[$iK]['rankinity_project_id'] = $Item['id'];
						$rankProjects[$iK]['rankinity_project_name'] = $Item['name'];
						$rankProjects[$iK]['rankinity_project_url'] = $Item['url'];
						$rankProjects[$iK]['rankinity_project_screenshot'] = $Item['screenshot'];
					}
				}
			} while ($fetched <= $ttlItems);
			$dWhere = array();
			$dWhere['url_profile_id'] = $profId;
			$this->SocialModel->updateRankinityProjects($rankProjects, $dWhere);
		}
	}

	public function updateAccountRankinity() {
		$profile_id = $this->input->post('fetched_profile');
		$rankinity_proj = $this->input->post('rankProjects');
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profile_id);
		if ($fetchedProfile) {
			$rankinityProj = $this->SocialModel->getRankinityProjDetail($rankinity_proj, $profile_id);
			// rankinity_project_url
			$this->AccountModel->linkRankinityAccount($rankinityProj, $fetchedProfile['id']);
		}
		redirect('accounts/list');
		exit;
	}

	public function link_adwords($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		if ($fetchedProfile && !$fetchedProfile['linked_adwords_acc_id']) {
			// $this->breadcrumb->addElement( 'Google Analytics', 'report/ganalyticreport' );
			$where = array();
			$where['prof_id'] = $fetchedProfile['id'];
			$inner = array();
			$shell = array();
			$optHtml = '';
			com_makelistElemFromTable($this, 'adword_account_list', 'account_id', 'account_name', 'parent_account_id', 0, $where, $optHtml);
			$log_user_id = com_user_data('id');
			$inner['profile'] = $fetchedProfile;
			$inner['projects'] = $optHtml;
			$shell['page_title'] = 'Link Adwords';
			$shell['content'] = $this->load->view('link_adwords', $inner, true);
			$shell['footer_js'] = $this->load->view('link_adwords_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {
			redirect("/");
			exit;
		}
	}

	public function updateAccountAdwords() {
		$profile_id = $this->input->post('fetched_profile');
		$adword_proj = $this->input->post('adwordProject');
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profile_id);
		$adwordProj = $this->SocialModel->getAdwordProjDetail($adword_proj);
		if ($fetchedProfile && !$fetchedProfile['linked_adwords_acc_id'] && $adwordProj) {
			// rankinity_project_url
			// $anl_prof_id, $adword_prj_id
			$this->AccountModel->updateGoogleAdwordsData($fetchedProfile['id'], $adwordProj);
		}
		redirect('accounts/list');
		exit;
	}
}