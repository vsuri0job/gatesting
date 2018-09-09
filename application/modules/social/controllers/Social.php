<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Social extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->library('Loaddata');
		$this->load->library('Adwords');
		$this->load->model('SocialModel');
		$this->load->model('ReportModel');
		$this->load->model('AccountModel');
	}

	public function google($prod, $profId) {
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		$ex_state = "";
		$fldCheck = "";
		switch ($prod) {
		case 'adwords':
			$fldCheck = "adword_";
			break;

		case 'analytic':
			$fldCheck = "analytic_";
			break;

		case 'mbusiness':
			$fldCheck = "gmb_";
			break;

		case 'webmaster':
			$fldCheck = "gsc_";
			break;
		}
		if ($fldCheck && $fetchedProfile[$fldCheck . 'reset_token']) {
			$ex_state .= '|RESET_TOKEN:1';
		}
		$customer_id = $this->input->post('customer_id');
		if ($customer_id) {
			$customer_id = str_replace("-", "", $customer_id);
			$ex_state .= '|CUS_ID:' . trim($customer_id);
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

	public function checkTokenValid($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		$profDet = $fetchedProfile;
		if ($fetchedProfile) {
			$profDet = $fetchedProfile;
			$socialAcc = array('mbusiness', 'analytic', 'adwords');
			foreach ($socialAcc as $sAcc) {
				$opt = array();
				$opt['prod'] = $sAcc;
				$opt['profId'] = $profId;
				$opt['log_user_id'] = $fetchedProfile['account_id'];
				$opt['access_token'] = $fetchedProfile['adword_access_token'];
				$opt['refresh_token'] = $fetchedProfile['adword_refresh_token'];
				$ctoken = $this->loaddata->updateGoogleTokens(true, $opt);
				$client = $ctoken['client'];
				$access_token = $client->getAccessToken();
				$refresh_token = $client->getRefreshToken();
				$profDet = $ctoken['profile_detail'];
			}
		}
	}

	public function verify_google() {
		$ret_code = $this->input->get('code');
		$state = $this->input->get('state');
		$state = $this->encrypt->decode($state, GEKEY);
		if ($ret_code && $state) {
			$state = explode('|', $state);
			$sD = array('PROD' => '', 'PID' => '');
			$ex = array('CUS_ID' => '', 'RESET_TOKEN' => 0);
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
				if (!$ex["RESET_TOKEN"]) {
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
					} else if ($sD['PROD'] == 'webmaster') {
						$opt = array();
						$opt['prod'] = $sD['PROD'];
						$opt['profId'] = $sD['PID'];
						$opt['access_token'] = $access_token;
						$opt['refresh_token'] = $refresh_token;
						$opt['log_user_id'] = com_user_data('id');
						$this->update_google_website_list($opt);
					}
				}
				redirect('accounts/list');
				exit;
			}
		}
		redirect('/');
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
		// $this->SocialModel->addUserGoogleAnalyticsProfilePropertyViewGData($profile_property_view_adata);
	}

	private function getPropViewAnalyticData($analytics, $viewId) {
		$months_tstamps = com_lastMonths(13);
		$ga_data = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
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

	public function updateGoogle($sRef, $profId) {
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		$sStack = array('adwords', 'analytic', 'mbusiness', 'webmaster');
		if ($fetchedProfile && in_array($sRef, $sStack)) {
			$fldCheck = "";
			$process = true;
			switch ($sRef) {
			case 'adwords':
				$process = false;
				$opt = array();
				$opt['prod'] = 'adwords';
				$opt['profId'] = $profId;
				$opt['access_token'] = $fetchedProfile['adword_access_token'];
				$opt['refresh_token'] = $fetchedProfile['adword_refresh_token'];
				$opt['clientCustomerId'] = $fetchedProfile['adword_customer_id'];
				$opt['log_user_id'] = com_user_data('id');
				$adWordsAccounts = $this->adwords->getList($opt);
				$rsWhere = array();
				$rsWhere['prof_id'] = $profId;
				$rsWhere['log_acc_id'] = com_user_data('id');
				$this->SocialModel->resetAdwordsAccounts($rsWhere, $adWordsAccounts);
				break;

			case 'analytic':
				$opt = array();
				$opt['prod'] = $sRef;
				$opt['profId'] = $profId;
				$opt['log_user_id'] = $fetchedProfile['account_id'];
				$opt['access_token'] = $fetchedProfile[$fldCheck . 'access_token'];
				$opt['refresh_token'] = $fetchedProfile[$fldCheck . 'refresh_token'];
				$ctoken = $this->loaddata->updateGoogleTokens(true, $opt);
				$client = $ctoken['client'];
				$this->fetchAnalyticData($client, $profId);
				break;

			case 'mbusiness':
				$opt = array();
				$opt['prod'] = 'mbusiness';
				$opt['profId'] = $profId;
				$opt['access_token'] = $fetchedProfile['gmb_access_token'];
				$opt['refresh_token'] = $fetchedProfile['gmb_refresh_token'];
				$opt['log_user_id'] = com_user_data('id');
				$this->update_google_business_list($opt);
				break;

			case 'webmaster':
				$opt = array();
				$opt['prod'] = 'webmaster';
				$opt['profId'] = $profId;
				$opt['access_token'] = $fetchedProfile['gsc_access_token'];
				$opt['refresh_token'] = $fetchedProfile['gsc_refresh_token'];
				$opt['log_user_id'] = com_user_data('id');
				$this->update_google_website_list($opt);
				break;
			}
		}
		redirect('dashboard');
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
				$gADataBPagesLocations[$lData->name]['account_page_location_name'] = $lData->name;
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
			$this->AccountModel->updateGoogleAdwordsData($fetchedProfile, com_user_data('id'), 13);
		}
		redirect('accounts/list');
		exit;
	}

	public function link_account_admin($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		if ($fetchedProfile && !$fetchedProfile['linked_account_id']) {
			// $this->breadcrumb->addElement( 'Google Analytics', 'report/ganalyticreport' );
			$agencies = explode(",", com_user_data('agencies'));
			// $accounts = $this->AccountModel->getAgencyAccounts($agencies);
			// $accounts_list = com_makelist($accounts, 'id', 'name');
			$serviceUrls = $this->AccountModel->getAccountServiceUrls($agencies);
			$accounts_list = com_makelist($serviceUrls, 'url', 'services', false, "", [], "name");
			$inner = array();
			$shell = array();
			$log_user_id = com_user_data('id');
			$inner['profile'] = $fetchedProfile;
			$inner['accounts'] = $accounts_list;
			$shell['page_title'] = 'Link Admin Account';
			$shell['content'] = $this->load->view('link_admin_account', $inner, true);
			$shell['footer_js'] = $this->load->view('link_admin_account_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {
			redirect("/");
			exit;
		}
	}

	public function updateAdminAccount($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		$accServiceUrl = $this->input->post('adminAccounts');
		$adminAccounts = $this->AccountModel->getAccountDetailFromServiceUrl($accServiceUrl);
		if ($fetchedProfile && !$fetchedProfile['linked_account_id'] && $adminAccounts) {
			$data = array();
			$data['linked_account_id'] = $adminAccounts['id'];
			$data['linked_service_url'] = $accServiceUrl;
			$this->SocialappModel->updateAdminAccount($fetchedProfile['id'], $data);
		}
		redirect("accounts/list");
		exit;
	}

	public function link_gbusiness($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		if ($fetchedProfile && !$fetchedProfile['linked_google_page']) {
			$gList = $this->SocialModel->getGbusinessDetail($fetchedProfile['id']);
			$gList = com_makelist($gList, 'gpId', 'account_page_location_place', false, "Select", [], "account_page_name");
			$inner = array();
			$shell = array();
			$inner['gList'] = $gList;
			$log_user_id = com_user_data('id');
			$inner['profile'] = $fetchedProfile;
			$inner['accounts'] = $accounts_list;
			$shell['page_title'] = 'Link Google My Business';
			$shell['content'] = $this->load->view('link_gbusiness', $inner, true);
			$shell['footer_js'] = $this->load->view('link_gbusiness_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {
			redirect("/");
			exit;
		}
	}

	public function updateGbusinessAccount($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		$gBusinessAccounts = $this->input->post('gbuissAccounts');
		if ($fetchedProfile && !$fetchedProfile['linked_google_page'] && $gBusinessAccounts) {
			$udata = array();
			$gPages = '';
			$gLocations = implode(',', $gBusinessAccounts);
			if ($gLocations) {
				$udata['linked_google_page'] = $gPages;
				$udata['linked_google_page_location'] = $gLocations;
				$this->SocialappModel->updateGBuissList($fetchedProfile['id'], $udata);
				$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
				$this->SocialappModel->updateProfileGBuissData($fetchedProfile, 13);
			}
		}
		redirect("accounts/list");
		exit;
	}

	public function update_google_website_list($opt) {
		$profId = $opt['profId'];
		$log_user_id = $opt['log_user_id'];
		$ctoken = $this->loaddata->updateGoogleTokens(true, $opt);
		$client = $ctoken['client'];
		$webMaster = new Google_Service_Webmasters($client);
		$web_urls = $webMaster->sites->listSites();
		$web_urls_data = array();
		if ($web_urls->siteEntry) {
			foreach ($web_urls->siteEntry as $siteKey => $siteDetail) {
				$web_urls_data[$siteKey]['site_url'] = $siteDetail->siteUrl;
				$web_urls_data[$siteKey]['permission_level'] = $siteDetail->permissionLevel;
				$web_urls_data[$siteKey]['url_profile_id'] = $profId;
			}
			$where = array();
			$where['url_profile_id'] = $profId;
			$this->SocialModel->addUserGoogleMasterSites($web_urls_data, $where);
		}
	}

	public function link_trello($profId) {
		$this->load->model('AccountModel');
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profId);
		if ($fetchedProfile && !$fetchedProfile['linked_trello_board_id']) {
			$this->form_validation->set_rules('board', 'Boards', 'required');
			if ($this->form_validation->run() == false) {
				// $this->breadcrumb->addElement( 'Google Analytics', 'report/ganalyticreport' );
				$boards = $this->SocialappModel->getTrelloBoards($profId);
				// $this->breadcrumb->addElement('Trello Boards', 'report/tboardreport/' . $prof_id);
				$inner = array();
				$shell = array();
				$log_user_id = com_user_data('id');
				$boards = com_makelist($boards, 'board_id', 'board_name', 1);
				$inner['boards'] = $boards;
				$inner['profDet'] = $fetchedProfile;
				$shell['page_title'] = 'Link Trello Boards';
				$shell['content'] = $this->load->view('link_tboard', $inner, true);
				$shell['footer_js'] = $this->load->view('link_tboard_js', $inner, true);
				$this->load->view(TMP_DEFAULT, $shell);
			} else {
				$board = $this->input->post('board');
				$data = array();
				$data['linked_trello_board_id'] = $board;
				$this->SocialappModel->updateAdminAccount($profId, $data);
				redirect("/");
				exit;
			}
		} else {
			redirect("/");
			exit;
		}
	}

	public function link_webmaster($prof_id) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		// $this->breadcrumb->addElement('Google Webmaster', 'report/link_webmaster/' . $prof_id);
		$this->form_validation->set_rules('webmaster_sites', 'Webmaster Site', 'required');
		if ($this->form_validation->run() == false) {
			$inner = array();
			$shell = array();
			$profile_id = $prodDet['id'];
			$inner['profDet'] = $prodDet;
			$inner['webmaster_sites'] = com_makelist($this->ReportModel->getAccountWebmasterProfiles($profile_id),
				'site_url', 'site_url', false);
			$shell['page_title'] = 'Google Search Console';
			$shell['content'] = $this->load->view('link_gwmaster', $inner, true);
			$shell['footer_js'] = $this->load->view('link_gwmaster_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {
			$webmaster_site = $this->input->post('webmaster_sites');
			$data = array();
			$data['linked_webmaster_site'] = $webmaster_site;
			$this->ReportModel->updateProfileAnalytic($prof_id, $data);
			$prodDet = $this->AccountModel->getProfileDetail($prof_id);
			$this->ReportModel->updateWebMasterData($prodDet, 13);
			redirect('dashboard');
			exit;
		}
	}

	public function reset_link($accId, $ref) {
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($accId);
		$data = array();
		switch ($ref) {
		case 'adwords':
			$data['linked_adwords_acc_id'] = '';
			break;

		case 'analytic':
			$data['view_id'] = '';
			$data['profile_id'] = '';
			$data['property_id'] = '';
			break;

		case 'mbusiness':
			$data['linked_google_page'] = '';
			$data['linked_google_page_id'] = '';
			$data['linked_google_page_location'] = '';
			break;

		case 'webmaster':
			$data['linked_webmaster_site'] = '';
			break;

		case 'admin':
			$data['linked_account_id'] = '0';
			$data['linked_service_url'] = '0';
			break;

		case 'trello':
			$data['linked_trello_board_id'] = '0';
			break;

		case 'rankinity':
			$data['linked_rankinity_id'] = '0';
			break;
		}
		if ($data) {
			$this->AccountModel->updateProfile($accId, $data);
			$this->AccountModel->resetLinkedData($accId, $ref);
		}
		redirect('accounts/list');
		exit;
	}
}