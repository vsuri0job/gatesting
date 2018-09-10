<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller {

	public function __construct() {
		parent::__construct();
		if (!in_array($this->router->method,
			array('viewPublicSocialReport'))) {
			$this->isLogin();
		}
		$this->load->model('ReportModel');
		$this->load->model('AccountModel');
		$this->load->library('Rankinity', RANKINITY_KEY);
	}

	public function viewPublicSocialReport($report) {
		$report = explode("/", com_b64UrlDecode($report));
		$profId = com_arrIndex($report, 1, 0);
		$reportName = com_arrIndex($report, 0, 0);
		if ($reportName && $profId) {
			if ($reportName == 'analytic') {
				$this->fetchedAnalytic($profId, 1);
			} else if ($reportName == 'adword') {
				$this->fetchedAdwords($profId, 1);
			} else if ($reportName == 'gsc') {
				$this->fetchedWebmaster($profId, 1);
			} else if ($reportName == 'gmb') {
				$this->fetchedGMB($profId, 1);
			} else if ($reportName == 'rankinity') {
				$this->rankinityProf($profId, 1);
			} else if ($reportName == 'overview') {
				$this->overview($profId, 1);
			} else if ($reportName == 'full') {
				$this->complete_full($profId, 1);
			} else {
				redirect("/");
				exit;
			}
		}
	}

	public function link_analytic($prof_id) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		$this->breadcrumb->addElement('Google Analytics', 'report/link_analytic/' . $prof_id);
		$inner = array();
		$shell = array();
		$profile_id = $prodDet['id'];
		$inner['profDet'] = $prodDet;
		$inner['profiles'] = $this->ReportModel->getAccountGoogleAnalyticProfiles($profile_id);
		$inner['props'] = $this->ReportModel->getAccountGoogleAnalyticProfileProps($profile_id);
		$inner['views'] = $this->ReportModel->getAccountGoogleAnalyticProfilePropViews($profile_id);
		$shell['page_title'] = 'Google Analytic';
		$shell['content'] = $this->load->view('ga_report', $inner, true);
		$shell['footer_js'] = $this->load->view('ga_report_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function gadwordreport() {
		if (!com_user_data('google_adwords_accid')) {
			redirect('dashboard/profile?sac=gadword');
			exit;
		}
		$this->breadcrumb->addElement('Google Adwords', 'report/gadwordreport');
		$inner = array();
		$shell = array();
		$log_user_id = com_user_data('id');
		$adword_acc_id = com_user_data('google_adwords_accid');
		$inner['linked_adwords'] = $this->SocialModel->getAccountGoogleAdwordsLinkDet($log_user_id);
		$inner['assoc_links'] = $this->ReportModel->getAccountGoogleAnalyticProfileAssos($log_user_id);
		$ga_data = $this->ReportModel->getAccAdwordsData($log_user_id, $adword_acc_id, "");
		$inner['ga_data'] = com_make2dArray($ga_data, 'month_ref');
		$inner['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAdwords', $inner, true);
		$inner['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAdwords', $inner, true);
		$shell['page_title'] = 'Google Adwords';
		$shell['content'] = $this->load->view('gad_report', $inner, true);
		$shell['footer_js'] = $this->load->view('gad_report_js', $inner, true);
		$this->load->view(TMP_DASHBOARD, $shell);
	}

	public function fetchedAnalytic($prof_id, $publicView = 0) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		if ($publicView) {
			$log_user_id = $prodDet['account_id'];
		} else {
			$log_user_id = com_user_data('id');
			if ($log_user_id != $prodDet['account_id']) {
				redirect('accounts/list');
				exit;
			}
		}
		$viewId = $prodDet['view_id'];
		$profileId = $prodDet['profile_id'];
		$propertyId = $prodDet['property_id'];
		$months_tstamps = com_lastMonths(13);
		$ga_data = array();
		$ga_data_organic = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->ReportModel->fetchViewAnalyticData($month_ref, $viewId, $prof_id);
			$gaDataOrganic = $this->ReportModel->fetchViewAnalyticDataOrganic($month_ref, $viewId, $prof_id);
			$gaData['month_ref'] = date('F Y', $mtstamp);
			$gaDataOrganic['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
			$ga_data_organic[$month_ref] = $gaDataOrganic;
		}
		$inner = array();
		$inner['ga_data'] = $ga_data;
		$inner['ga_data_organic'] = $ga_data_organic;
		$month_ref = date('Y-m', time());
		$whereStack = array();
		$whereStack['view_id'] = $viewId;
		$whereStack['report_type'] = 'medium';
		$whereStack['month_ref'] = $month_ref;
		$whereStack['url_profile_id'] = $prof_id;
		$whereStack['account_id'] = $log_user_id;
		$orderBy = ' ABS(users) desc ';
		$inner['ga_data_medium'] = $this->ReportModel->getGAdataDetail($whereStack, $orderBy);
		$whereStack = array();
		$whereStack['view_id'] = $viewId;
		$whereStack['month_ref'] = $month_ref;
		$whereStack['url_profile_id'] = $prof_id;
		$whereStack['report_type'] = 'source_medium';
		$whereStack['account_id'] = $log_user_id;
		$opt = array();
		$opt['offset'] = 0;
		$opt['limit'] = 15;
		$inner['ga_data_source_medium'] = $this->ReportModel->getGAdataDetail($whereStack, $orderBy, $opt);
		$whereStack = array();
		$whereStack['view_id'] = $viewId;
		$whereStack['month_ref'] = $month_ref;
		$whereStack['url_profile_id'] = $prof_id;
		$whereStack['report_type'] = 'landing_page';
		$whereStack['account_id'] = $log_user_id;
		$opt = array();
		$opt['offset'] = 0;
		$opt['limit'] = 15;
		$inner['ga_data_landing_page'] = $this->ReportModel->getGAdataDetail($whereStack, $orderBy, $opt);
		$whereStack = array();
		$whereStack['view_id'] = $viewId;
		$whereStack['month_ref'] = $month_ref;
		$whereStack['url_profile_id'] = $prof_id;
		$whereStack['report_type'] = 'session_graph';
		$whereStack['account_id'] = $log_user_id;
		$opt = array();
		$opt['row_only'] = 1;
		$graph_data = $this->ReportModel->getGAdataDetail($whereStack, $orderBy, $opt);
		$graph_data = json_decode($graph_data['session_data']);
		$chart_graph = array();
		if ($graph_data) {
			foreach ($graph_data as $key => $value) {
				$year = substr($value[0], 0, 4);
				$month = substr($value[0], 4, 2);
				$day = substr($value[0], 6, 2);
				$chart_graph[$key]['date'] = date('d-M-y', strtotime($year . '-' . $month . '-' . $day));
				$chart_graph[$key]['sess'] = $value[1];
				$chart_graph[$key]['conversion'] = com_arrIndex( $value, 2, 0);
			}
		}
		$inner['chart_graph'] = $chart_graph;
		$inner['report_setting'] = $this->AccountModel->getFetchedAccountDetailSetting($prof_id, com_user_data('id'));
		$inner['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAnalytics', $inner, true);
		$inner['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAnalytic', $inner, true);
		$inner['prodDet'] = $prodDet;
		$inner['show_public_url'] = !$publicView;
		$inner['log_user_det'] = com_get_user_data($log_user_id);
		$shell['page_title'] = 'Google Analytics';
		$shell['content'] = $this->load->view('ganalytic_report', $inner, true);
		$shell['footer_js'] = $this->load->view('ganalytic_report_js', $inner, true);
		$temp = TMP_DEFAULT;
		if ($publicView) {
			$temp = TMP_PREPORT;
		}
		$this->load->view($temp, $shell);
	}

	public function fetchedAdwords($prof_id, $publicView = 0) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		if ($publicView) {
			$log_user_id = $prodDet['account_id'];
		} else {
			$log_user_id = com_user_data('id');
			if ($log_user_id != $prodDet['account_id']) {
				redirect('accounts/list');
				exit;
			}
		}
		// $this->breadcrumb->addElement('Google Adwords', 'report/gadwordreport');
		$inner = array();
		$shell = array();
		$log_user_id = com_user_data('id');
		$fetch_prof_id = $prodDet['id'];
		$adword_acc_id = $prodDet['linked_adwords_acc_id'];
		$inner['prodDet'] = $prodDet;
		$inner['show_public_url'] = !$publicView;
		$inner['assoc_prof_id'] = $fetch_prof_id;
		$inner['linked_adwords'] = $this->SocialModel->getAdwordProjDetail($adword_acc_id);
		$inner['assoc_links'] = $this->ReportModel->getAccountGoogleAnalyticProfileAssos($log_user_id);
		$ga_data = $this->ReportModel->getAccAdwordsData($log_user_id, $adword_acc_id, "", $fetch_prof_id);
		$inner['ga_data'] = com_make2dArray($ga_data, 'month_ref');
		$inner['report_setting'] = $this->AccountModel->getFetchedAccountDetailSetting($fetch_prof_id, com_user_data('id'));
		$inner['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAdwords', $inner, true);
		$inner['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAdwords', $inner, true);
		$shell['page_title'] = 'Google Adwords';
		$shell['content'] = $this->load->view('gad_report', $inner, true);
		$shell['footer_js'] = $this->load->view('gad_report_js', $inner, true);
		// $this->load->view(TMP_DASHBOARD, $shell);
		$temp = TMP_DEFAULT;
		if ($publicView) {
			$temp = TMP_PREPORT;
		}
		$this->load->view($temp, $shell);
	}

	public function fetchedWebmaster($prof_id, $publicView = 0) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		if ($publicView) {
			$log_user_id = $prodDet['account_id'];
		} else {
			$log_user_id = com_user_data('id');
			if ($log_user_id != $prodDet['account_id']) {
				redirect('accounts/list');
				exit;
			}
		}
		// $this->breadcrumb->addElement('Google Adwords', 'report/gadwordreport');
		$inner = array();
		$shell = array();
		$fetch_prof_id = $prodDet['id'];
		$adword_acc_id = $prodDet['linked_adwords_acc_id'];
		$opt = array();
		$opt['row_only'] = 1;
		$inner['prodDet'] = $prodDet;
		$inner['show_public_url'] = !$publicView;
		$inner['kpis'] = $this->ReportModel->getWebmasterData($fetch_prof_id, 'kpi', $opt);
		$opt = array();
		$opt['limit'] = '25';
		$inner['queries'] = $this->ReportModel->getWebmasterData($fetch_prof_id, 'query', $opt);
		$inner['pages'] = $this->ReportModel->getWebmasterData($fetch_prof_id, 'page');
		$opt = array();
		$opt['limit'] = '13';
		$opt['order'] = 'month_ref desc';
		$inner['months'] = $this->ReportModel->getWebmasterData($fetch_prof_id, 'month', $opt);
		$inner['report_setting'] = $this->AccountModel->getFetchedAccountDetailSetting($fetch_prof_id, com_user_data('id'));
		$shell['page_title'] = 'Google Search Console';
		$shell['content'] = $this->load->view('gwmaster_report', $inner, true);
		$shell['footer_js'] = $this->load->view('gwmaster_report_js', $inner, true);
		// $this->load->view(TMP_DASHBOARD, $shell);
		$temp = TMP_DEFAULT;
		if ($publicView) {
			$temp = TMP_PREPORT;
		}
		$this->load->view($temp, $shell);
	}

	public function getViewData() {
		$viewId = $this->input->post('view');
		$propertyId = $this->input->post('prop');
		$profileId = $this->input->post('profile');
		$relProfId = $this->input->post('prof_id');
		$prodDet = $this->AccountModel->getProfileDetail($relProfId);
		$inner = $out = array();
		$inner['report_setting'] = array();
		$out['linkAccount'] = "";
		$out['lastMonthHtml'] = "";
		$out['currMonthHtml'] = "";
		if ($prodDet) {
			$opt = array();
			$opt['prod'] = 'analytic';
			$opt['profId'] = $prodDet['id'];
			$opt['log_user_id'] = com_user_data('id');
			$opt['refresh_token'] = $prodDet['analytic_refresh_token'];
			$opt['access_token'] = $prodDet['analytic_access_token'];
			$client_token = $this->loaddata->updateGoogleTokens(true, $opt);
			$client = $client_token['client'];
			$service = new Google_Service_Oauth2($client);
			$analytics = new Google_Service_Analytics($client);
			$this->updateFetchedAccountProfile($prodDet['id']);
			$inner = $this->ReportModel->fetchPropViewAnalyticData($analytics, $viewId, $prodDet['id'], $prodDet['account_id'], 13);
			$out['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAnalytics', $inner, true);
			$out['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAnalytic', $inner, true);
		}
		echo json_encode($out);
		exit;
	}

	private function updateFetchedAccountProfile($profId) {
		$viewId = $this->input->post('view');
		$propertyId = $this->input->post('prop');
		$profileId = $this->input->post('profile');
		$propDetail = $this->SocialModel->getPropertyDetail($propertyId);
		$data = array();
		$data['view_id'] = $viewId;
		$data['profile_id'] = $profileId;
		$data['property_id'] = $propertyId;
		$aProfileId = $this->ReportModel->updateProfileAnalytic($profId, $data);
	}

	private function getPropViewAnalyticData($analytics, $viewId) {
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$months_tstamps = com_lastMonths(13);
		$ga_data = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->ReportModel->fetchViewAnalyticData($month_ref, $viewId);
			if (!$gaData || in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
				$sday_month = date('Y-m-01', $mtstamp);
				$lday_month = date('Y-m-t', $mtstamp);
				$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
					$sday_month, $lday_month,
					'ga:sessions, ga:users, ga:pageviewsPerSession, ga:avgSessionDuration, ga:bounceRate, ga:avgPageDownloadTime, ga:goalConversionRateAll, ga:goalCompletionsAll');
				$gaData['account_id'] = com_user_data('id');
				$gaData['view_id'] = $viewId;
				$gaData['month_ref'] = $month_ref;
				$gaData['sessions'] = $ga_rstdata->totalsForAllResults['ga:sessions'];
				$gaData['users'] = $ga_rstdata->totalsForAllResults['ga:users'];
				$gaData['page_view_per_sessions'] = $ga_rstdata->totalsForAllResults['ga:pageviewsPerSession'];
				$gaData['avg_session_duration'] = $ga_rstdata->totalsForAllResults['ga:avgSessionDuration'];
				$gaData['bounce_rate'] = $ga_rstdata->totalsForAllResults['ga:bounceRate'];
				$gaData['avg_page_download_time'] = $ga_rstdata->totalsForAllResults['ga:avgPageDownloadTime'];
				$gaData['goal_conversion_rate'] = $ga_rstdata->totalsForAllResults['ga:goalConversionRateAll'];
				$gaData['goal_completion_all'] = $ga_rstdata->totalsForAllResults['ga:goalCompletionsAll'];
				if (in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
					$this->ReportModel->updateGAdata($month_ref, $viewId, $gaData);
				} else {
					$this->ReportModel->insertGAdata($gaData);
				}
				unset($gaData['account_id'], $gaData['view_id']);
			}
			$gaData['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
		}
		return $ga_data;
	}

	private function getViewAdwordData() {
		$viewId = $this->input->post('view');
		$access_token = com_user_data('google_access_token');
		$refresh_token = com_user_data('google_refresh_token');
		$redirect_uri = base_url('social/verify_google');
		$client = $this->loaddata->updateGoogleTokens();
		$service = new Google_Service_Oauth2($client);
		$analytics = new Google_Service_Analytics($client);
		$inner = array();
		$inner = $this->fetchPropViewAdwordData($analytics, $viewId);
		$out = array();
		$out['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAdwords', $inner, true);
		$out['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAdwords', $inner, true);
		echo json_encode($out);
		exit;
	}

	private function fetchPropViewAdwordData($analytics, $viewId) {
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$months_tstamps = com_lastMonths(13);
		$ga_data = array();
		$ga_data_organic = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->fetchAdwordTotalTraffic($analytics, $month_ref, $viewId);
			$gaData['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
		}
		$out = array();
		$out['ga_data'] = $ga_data;
		return $out;
	}

	private function fetchAdwordTotalTraffic($analytics, $month_ref, $viewId) {
		$logUserId = com_user_data('id');
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$gaData = $this->ReportModel->fetchViewAdwordData($month_ref, $viewId);
		if (!$gaData || in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
			$mtstamp = strtotime($month_ref . '-01');
			$sday_month = date('Y-m-01', $mtstamp);
			$lday_month = date('Y-m-t', $mtstamp);
			$opt = array();
			$opt['dimensions'] = '';
			$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
				$sday_month, $lday_month,
				'ga:adClicks, ga:impressions, ga:CTR, ga:CPC, ga:adCost');
			$allDetailRows = $ga_rstdata->rows;
			$gaData['account_id'] = $logUserId;
			$gaData['view_id'] = $viewId;
			$gaData['month_ref'] = $month_ref;
			$gaData['ctr'] = $ga_rstdata->totalsForAllResults['ga:CTR'];
			$gaData['cost'] = $ga_rstdata->totalsForAllResults['ga:adCost'];
			$gaData['avg_cpc'] = $ga_rstdata->totalsForAllResults['ga:CPC'];
			$gaData['clicks'] = $ga_rstdata->totalsForAllResults['ga:adClicks'];
			$gaData['impressions'] = $ga_rstdata->totalsForAllResults['ga:impressions'];
			$gaData['conversion'] = '0';
			$gaData['phone_calls'] = '0';
			$gaData['avg_position'] = '0';
			$gaData['cost_per_conversion'] = '0';
			if (in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
				$this->ReportModel->updateGAdwordData($month_ref, $viewId, $gaData);
			} else {
				$this->ReportModel->insertGAdwordData($gaData);
			}
			unset($gaData['account_id'], $gaData['view_id']);
		}
		return $gaData;
	}

	public function tboardreport($prof_id) {
		$profDet = $this->AccountModel->getProfileDetail($prof_id);
		$trelloToken = com_arrIndex($profDet, 'trello_access_token', '');
		$trello_board_id = com_arrIndex($profDet, 'linked_trello_board_id', '');
		if (!$profDet || !$trelloToken || !$trello_board_id) {
			redirect('accounts/list');
			exit;
		}
		$inner = $shell = $cards = $cardLists = array();
		$cardLists = array('curr_mon' => '', 'last_mon' => '');
		if ($trelloToken) {
			$query = http_build_query([
				'key' => TRELLO_DEV_KEY,
				'token' => $trelloToken,
			]);
			$opt = array();
			$opt['url'] = "https://api.trello.com/1/boards/" . $trello_board_id . '/cards?' . $query;
			$cards = $this->curlRequest($opt);
			$cards = json_decode($cards);

			list($currMonth, $lastMonth) = com_lastMonths(2, "", true);
			$tmNames = array(date("F", strtotime($currMonth)), date("M", strtotime($currMonth)));
			$lmNames = array(date("F", strtotime($lastMonth)), date("M", strtotime($lastMonth)));
			$query = http_build_query([
				'key' => TRELLO_DEV_KEY,
				'token' => $trelloToken,
			]);
			$opt = array();
			$opt['url'] = "https://api.trello.com/1/boards/" . $trello_board_id . '/lists?' . $query;
			$blists = $this->curlRequest($opt);
			$blists = json_decode($blists);
			$cYear = date("Y", time());
			if ($blists) {
				foreach ($blists as $blist) {
					if (!$cardLists['curr_mon']) {
						foreach ($tmNames as $tmName) {
							if ((strpos($blist->name, $tmName) !== FALSE)
								&& (strpos($blist->name, $cYear) !== FALSE)) {
								$cardLists['curr_mon'] = $blist;
								break;
							}
						}
					}
					if (!$cardLists['last_mon']) {
						foreach ($lmNames as $lmName) {
							if ((strpos($blist->name, $lmName) !== FALSE)
								&& (strpos($blist->name, $cYear) !== FALSE)) {
								$cardLists['last_mon'] = $blist;
								break;
							}
						}
					}
				}
			}
		}
		$this->breadcrumb->addElement('Trello Board Cards', 'report/tboardreport/' . $prof_id);		
		$inner['show_public_url'] = 0;
		$log_user_id = com_user_data('id');
		$inner['board'] = $this->ReportModel->getBoardDetail($trello_board_id);
		$inner['cards'] = $cards;
		$inner['cardLists'] = $cardLists;
		$inner['cardsColor'] = RandomColor::many(count($inner['cards']));
		$inner['profDet'] = $profDet;
		$shell['page_title'] = 'Trello Cards';
		$shell['content'] = $this->load->view('tboard_report', $inner, true);
		$shell['footer_js'] = $this->load->view('tboard_report_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
		// $opt[ 'url' ] = 'https://api.trello.com/1/members/me/boards?key='.TRELLO_CLIENT_ID.'&token='.com_user_data( 'trello_access_token' );
		// $boards = $this->curlRequest( $opt );
	}

	public function fetchBoardCards() {
		$this->load->library('RandomColor');
		$formData = $this->input->post('formData');
		$fdata = array();
		foreach ($formData as $form) {
			$fdata[$form['name']] = $form['value'];
		}
		$profId = $fdata['profId'];
		$prodDet = $this->AccountModel->getProfileDetail($profId);
		$out = array();
		$out['cardsHtml'] = "";

		echo json_encode($out);
		exit;
	}
	public function searchBoardCards() {
		$formData = $this->input->post('formData');
		$fdata = array();
		foreach ($formData as $form) {
			$fdata[$form['name']] = $form['value'];
		}
		$query = http_build_query([
			'query' => $fdata['text_query'],
			'idBoards' => $fdata['board'],
			'due' => 'month',
			'key' => TRELLO_CLIENT_ID,
			'token' => com_user_data('trello_access_token'),
			'card_attachments' => false,
		]);

		$opt = array();
		$opt['url'] = "https://api.trello.com/1/search?" . $query;
		$inner = array();
		$cards = $this->curlRequest($opt);
		$inner['cards'] = json_decode($cards);
		$out = array();
		$out['cardsHtml'] = $this->load->view('sub_views/boardCard', $inner, true);
		echo json_encode($out);
		exit;
	}

	public function updateTrelloBoards($token, $profId) {
		$opt = array();
		$opt['url'] = 'https://api.trello.com/1/members/me/boards?key=' . TRELLO_DEV_KEY . '&token=' . $token;
		$boards = $this->curlRequest($opt);
		$boards = json_decode($boards);
		$boardStack = array();
		if ($boards) {
			foreach ($boards as $bIndex => $board) {
				$boardStack[$board->id] = $board->name;
				/*
					$boardStack[ $bIndex ][ 'account_id' ] = com_user_data( 'id' );
					$boardStack[ $bIndex ][ 'board_id' ] = $board->id;
					$boardStack[ $bIndex ][ 'board_url' ] = $board->url;
					$boardStack[ $bIndex ][ 'board_name' ] = $board->name;
					$boardStack[ $bIndex ][ 'board_closed' ] = $board->closed;
				*/
			}
		}
		return $boardStack;
	}

	public function citation_and_content($prof_id) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		$linked_account_id = com_arrIndex($prodDet, 'linked_account_id', '');
		if (!$prodDet || !$linked_account_id) {
			redirect('accounts/list');
			exit;
		}
		$log_user_id = com_user_data('id');
		if ($log_user_id != $prodDet['account_id']) {
			redirect('accounts/list');
			exit;
		}
		$url = base_url('report/citation_and_content/' . $prof_id);
		$this->breadcrumb->addElement('Citation & Content', $url);
		$inner = array();
		$shell = array();
		$log_user_id = com_user_data('id');
		$inner['prof_id'] = $prof_id;
		$inner['cc_counts'] = $this->ReportModel->getCitationContentCount($linked_account_id);
		$shell['page_title'] = 'Citation & Content';
		$shell['content'] = $this->load->view('citation_content', $inner, true);
		$shell['footer_js'] = $this->load->view('citation_content_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function contentView($tstamp, $prof_id = 0) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		$linked_account_id = com_arrIndex($prodDet, 'linked_account_id', '');
		if (!$prodDet || !$linked_account_id) {
			redirect('accounts/list');
			exit;
		}
		$this->breadcrumb->addElement('Citation & Content', base_url('report/citation_and_content/' . $prof_id));
		$this->breadcrumb->addElement('Content', base_url('report/contentView'));
		$inner = array();
		$shell = array();
		$log_user_id = com_user_data('id');
		$inner['month'] = date("Y F", $tstamp);
		$inner['contents'] = $this->ReportModel->contentReport($tstamp, $linked_account_id);
		$shell['page_title'] = 'Content';
		$shell['content'] = $this->load->view('content_report', $inner, true);
		$shell['footer_js'] = $this->load->view('content_report_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function citationView($tstamp, $accountId = 0) {
		$month_date = date("Y-m", $tstamp);
		$this->breadcrumb->addElement('Citation & Content', base_url('report/citation_and_content/' . $accountId));
		$this->breadcrumb->addElement('Citation', base_url('report/citationView'));
		$inner = array();
		$shell = array();
		$log_user_id = com_user_data('id');
		$inner['month'] = date("Y F", $tstamp);
		$inner['citations'] = $this->ReportModel->citationReport($month_date, $accountId);
		$shell['page_title'] = 'Citation';
		$shell['content'] = $this->load->view('citation_report', $inner, true);
		$shell['footer_js'] = $this->load->view('citation_report_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function getRankinityProject($propDet = '', $aProfId = 0) {
		$fetched = 0;
		if (isset($propDet['property_website_url'])) {
			$propDet['rankinity_project_url'] = $propDet['property_website_url'];
			$fetched = $this->AccountModel->linkRankinityAccount($propDet, $aProfId);
		}
		return $fetched;
	}

	public function rankinityProf($profId, $publicView = 0) {
		$rankProfile = $this->ReportModel->getRankinityProfile($profId);
		if (!$rankProfile) {
			redirect('accounts/list');
			exit;
		}
		if ($publicView) {
			$log_user_id = $rankProfile['account_id'];
		} else {
			$log_user_id = com_user_data('id');
			if ($log_user_id != $rankProfile['account_id']) {
				redirect('accounts/list');
				exit;
			}
		}
		$inner = array();
		$profileEngines = $this->ReportModel->getRankinityProfileEngines($rankProfile['rankinity_project_id'], $profId);
		$profileEnginesRanks = array();
		$profileEnginesVisibility = array();
		foreach ($profileEngines as $engine) {
			$profileEnginesRanks[$engine['engine_id']] =
			$this->ReportModel->getRankinityProfileEngineRanks($engine['project_id'], $engine['engine_id'], $profId);
			$profileEnginesVisibility[$engine['engine_id']] =
			$this->ReportModel->getRankinityProfileEngineVisibility($engine['project_id'], $engine['engine_id'], $profId);
			$profileEnginesVisibilityHistory[$engine['engine_id']] = $this->buildHistory($profileEnginesVisibility[$engine['engine_id']]);
		}
		$inner['show_public_url'] = !$publicView;
		$inner['rankProfile'] = $rankProfile;
		$inner['profileEngines'] = $profileEngines;
		$inner['profileEnginesRanks'] = $profileEnginesRanks;
		$inner['profileEnginesVisibility'] = $profileEnginesVisibility;
		$inner['profileEnginesVisibilityHistory'] = $profileEnginesVisibilityHistory;
		$shell['page_title'] = 'Keyword Ranking Report';
		$shell['content'] = $this->load->view('rankinity_report', $inner, true);
		$shell['footer_js'] = $this->load->view('rankinity_report_js', $inner, true);
		$temp = TMP_DEFAULT;
		if ($publicView) {
			$temp = TMP_PREPORT;
		}
		$this->load->view($temp, $shell);
	}

	private function buildHistory($visibility) {
		$visHistory = array();
		$historyStDate = $visibility['visibility_created_at'];
		$historyStack = json_decode($visibility['position_history']);
		if ($historyStDate && $historyStack) {
			$defSize = 29;
			$historyStDate = explode(".", $historyStDate);
			$format = 'Y-m-d\TH:i:s';
			$historyStDate = DateTime::createFromFormat($format, $historyStDate[0]);
			$historyStDateShow = $historyStDate->format('Y-m-d');
			$hisShow = array();
			$min = $historyStack[0][0];
			$max = $historyStack[count($historyStack) - 1][0];
			$size = $max - $min;
			if ($size < $defSize) {
				$hisShow = array_fill(0, ($min - ($defSize - $size)), 0);
			}
			// $hisShow = array_merge($hisShow, );
			foreach ($historyStack as $hisStack) {
				$add_days = "+" . $hisStack[0] . " day";
				$start = new DateTimeImmutable($historyStDateShow);
				$datetime = $start->modify($add_days);
				// $visHistory[ $datetime->format('Y-m-d') ] = $hisStack[ 1 ];
				$visHistory[] = array(
					'date' => $datetime->format('Y-m-d'),
					'vdata' => $hisStack[1],
				);
			}
		}
		return $visHistory;
	}

	public function fetchedGMB($prof_id, $publicView = 0) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		if ($publicView) {
			$log_user_id = $prodDet['account_id'];
		} else {
			$log_user_id = com_user_data('id');
			if ($log_user_id != $prodDet['account_id']) {
				redirect('accounts/list');
				exit;
			}
		}
		// $this->breadcrumb->addElement('Google Adwords', 'report/gadwordreport');
		$inner = array();
		$shell = array();
		$fetch_prof_id = $prodDet['id'];
		$locs = explode(",", $prodDet['linked_google_page_location']);
		$inner['prodDet'] = $prodDet;
		$inner['show_public_url'] = !$publicView;
		$inner['gmb_data'] = $inner['gmb_locs'] = array();
		$gmb_data = $gmb_locs = $gmb_loc_kpis = $gmb_loc_kpi_diff = array();
		list( $firMonthDate, $secMonthDate) = com_lastMonths( 2, "", 1, 1 );
		$gmb_loc_id = '';
		foreach ($locs as $loc) {
			$gmb_rawdata = $this->ReportModel->fetchUrlGoogleMyBusiness($fetch_prof_id, $loc, 13);			
			foreach ($gmb_rawdata as $key => $value) {
				if (!$gmb_loc_id) {
					$gmb_loc_id = $value['location_name'];
				}
				if( !isset( $gmb_loc_kpis[ $value['location_name'] ] ) ){
					$gmb_loc_kpis[ $value['location_name'] ] = 
					array( $firMonthDate => com_initGMBData(),  $secMonthDate => com_initGMBData());
					$gmb_loc_kpi_diff[ $value['location_name'] ] = com_initGMBData();
				}
				$gmb_locs[$value['location_name']] = $value['account_page_location_place'];
				$date_ref = $value['month_ref'] . '-01';				
				$monthRefReport = date("F Y", strtotime($date_ref));
				if( in_array($date_ref, array($firMonthDate, $secMonthDate)) ){
					$gmb_loc_kpis[ $value['location_name'] ][ $date_ref ][ 'clicks' ] = $value['actions_website'];
					$gmb_loc_kpis[ $value['location_name'] ][ $date_ref ][ 'direc' ] = $value['actions_driving_directions'];
					$gmb_loc_kpis[ $value['location_name'] ][ $date_ref ][ 'calls' ] = $value['actions_phone'];
					$gmb_loc_kpi_diff[ $value['location_name'] ][ 'clicks' ] = 
					com_compKPI( $gmb_loc_kpis[ $value['location_name'] ][ $firMonthDate ][ 'clicks' ], 
						$gmb_loc_kpis[ $value['location_name'] ][ $secMonthDate ][ 'clicks' ]);
					$gmb_loc_kpi_diff[ $value['location_name'] ][ 'direc' ] = 
					com_compKPI( $gmb_loc_kpis[ $value['location_name'] ][ $firMonthDate ][ 'direc' ], 
						$gmb_loc_kpis[ $value['location_name'] ][ $secMonthDate ][ 'direc' ]);
					$gmb_loc_kpi_diff[ $value['location_name'] ][ 'calls' ] = 
					com_compKPI( $gmb_loc_kpis[ $value['location_name'] ][ $firMonthDate ][ 'calls' ], 
						$gmb_loc_kpis[ $value['location_name'] ][ $secMonthDate ][ 'calls' ]);
				}
				$gmb_data[$value['location_name']][] =
				array(
					$monthRefReport,
					$value['actions_website'],
					$value['actions_driving_directions'],
					$value['actions_phone'],
				);
			}
		}
		$inner['gmb_data'] = $gmb_data;
		$inner['gmb_locs'] = $gmb_locs;
		$inner['gmb_loc_id'] = $gmb_loc_id;
		$inner['gmb_loc_kpis'] = $gmb_loc_kpi_diff;		
		$shell['page_title'] = 'Google My Business';
		$shell['content'] = $this->load->view('gmb_report', $inner, true);
		$shell['footer_js'] = $this->load->view('gmb_report_js', $inner, true);
		$temp = TMP_DEFAULT;
		if ($publicView) {
			$temp = TMP_PREPORT;
		}
		$this->load->view($temp, $shell);
	}

	public function overview($profId, $publicView = 0) {
		$prodDet = $this->AccountModel->getProfileDetail($profId);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		if ($publicView) {
			$log_user_id = $prodDet['account_id'];
		} else {
			$log_user_id = com_user_data('id');
			if ($log_user_id != $prodDet['account_id']) {
				redirect('accounts/list');
				exit;
			}
		}

		$linkAccId = $prodDet['linked_account_id'];
		$linkServUrl = $prodDet['linked_service_url'];
		$serviceUrlData = $this->ReportModel->getServiceUrlCost($linkServUrl, $linkAccId);
		$serviceUrlData = com_makelist($serviceUrlData, 'services', 'price', false);
		$inner = array();
		$shell = array();
		$inner['prodDet'] = $prodDet;
		$inner['service_url_data'] = $serviceUrlData;
		$viewId = $prodDet['view_id'];
		$profileId = $prodDet['profile_id'];
		$propertyId = $prodDet['property_id'];
		$months_tstamps = com_lastMonths(13);
		$ga_data = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->ReportModel->fetchViewAnalyticData($month_ref, $viewId, $profId);
			$gaData['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
		}
		$inner['show_public_url'] = !$publicView;
		$inner['ga_data'] = $ga_data;
		$adword_acc_id = $prodDet['linked_adwords_acc_id'];
		$inner['linked_adwords'] = $this->SocialModel->getAdwordProjDetail($adword_acc_id);
		$gad_data = $this->ReportModel->getAccAdwordsData($log_user_id, $adword_acc_id, "", $profId);
		$inner['gad_data'] = com_make2dArray($gad_data, 'month_ref');
		$locs = explode(",", $prodDet['linked_google_page_location']);
		$inner['gmb_data'] = $inner['gmb_locs'] = array();
		$gmb_data = $gmb_locs = array();
		$gmb_data = $this->ReportModel->fetchUrlGoogleMyBusinessMonthData($profId, $locs, 13);
		$gmb_data = com_make2dArray($gmb_data, 'month_ref');
		$inner['gmb_data'] = $gmb_data;
		$inner['gmb_locs'] = $gmb_locs;
		$shell['page_title'] = 'Overview Report';
		$shell['content'] = $this->load->view('overview_report', $inner, true);
		$shell['footer_js'] = $this->load->view('overview_report_js', $inner, true);
		// $this->load->view(TMP_DASHBOARD, $shell);
		$temp = TMP_DEFAULT;
		if ($publicView) {
			$temp = TMP_PREPORT;
		}
		$this->load->view($temp, $shell);
	}

	public function complete_full($profId, $publicView = 0) {
		$prodDet = $this->AccountModel->getProfileDetail($profId);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		if ($publicView) {
			$log_user_id = $prodDet['account_id'];
		} else {
			$log_user_id = com_user_data('id');
			if ($log_user_id != $prodDet['account_id']) {
				redirect('accounts/list');
				exit;
			}
		}
		$report_setting = $this->AccountModel->getFetchedAccountDetailSetting($profId, $log_user_id);
		$linkAccId = $prodDet['linked_account_id'];
		$linkServUrl = $prodDet['linked_service_url'];
		$serviceUrlData = $this->ReportModel->getServiceUrlCost($linkServUrl, $linkAccId);
		$serviceUrlData = com_makelist($serviceUrlData, 'services', 'price', false);
		$inner = array();
		$shell = array();
		$inner['full_report_show'] = true;
		$inner['prodDet'] = $prodDet;
		$inner['service_url_data'] = $serviceUrlData;
		$viewId = $prodDet['view_id'];
		$profileId = $prodDet['profile_id'];
		$propertyId = $prodDet['property_id'];
		$months_tstamps = com_lastMonths(13);
		$ga_data = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->ReportModel->fetchViewAnalyticData($month_ref, $viewId, $profId);

			$gaData['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
		}
		$inner['ga_data'] = $ga_data;
		$adword_acc_id = $prodDet['linked_adwords_acc_id'];
		$inner['linked_adwords'] = $this->SocialModel->getAdwordProjDetail($adword_acc_id);
		$gad_data = $this->ReportModel->getAccAdwordsData($log_user_id, $adword_acc_id, "", $profId);
		$inner['gad_data'] = com_make2dArray($gad_data, 'month_ref');
		$locs = explode(",", $prodDet['linked_google_page_location']);
		$inner['gmb_data'] = $inner['gmb_locs'] = array();
		$gmb_data = $gmb_locs = array();
		$gmb_data = $this->ReportModel->fetchUrlGoogleMyBusinessMonthData($profId, $locs, 13);
		$gmb_data = com_make2dArray($gmb_data, 'month_ref');		
		$inner['gmb_data'] = $gmb_data;
		$inner['gmb_locs'] = $gmb_locs;
		$inner['report_setting'] = $report_setting;
		$inner['overview_report'] = $this->load->view('overview_report', $inner, true);
		$inner['overview_report_js'] = $this->load->view('overview_report_js', $inner, true);

		$prof_id = $profId;
		$viewId = $prodDet['view_id'];
		$profileId = $prodDet['profile_id'];
		$propertyId = $prodDet['property_id'];

		$months_tstamps = com_lastMonths(13);
		$ga_data = array();
		$ga_data_organic = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->ReportModel->fetchViewAnalyticData($month_ref, $viewId, $prof_id);
			$gaDataOrganic = $this->ReportModel->fetchViewAnalyticDataOrganic($month_ref, $viewId, $prof_id);
			$gaData['month_ref'] = date('F Y', $mtstamp);
			$gaDataOrganic['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
			$ga_data_organic[$month_ref] = $gaDataOrganic;
		}

		$anly_repo = array();
		$anly_repo['ga_data'] = $ga_data;
		$anly_repo['ga_data_organic'] = $ga_data_organic;
		$month_ref = date('Y-m', time());
		$whereStack = array();
		$whereStack['view_id'] = $viewId;
		$whereStack['report_type'] = 'medium';
		$whereStack['month_ref'] = $month_ref;
		$whereStack['url_profile_id'] = $prof_id;
		$whereStack['account_id'] = $log_user_id;
		$orderBy = ' ABS(users) desc ';
		$anly_repo['ga_data_medium'] = $this->ReportModel->getGAdataDetail($whereStack, $orderBy);
		$whereStack = array();
		$whereStack['view_id'] = $viewId;
		$whereStack['month_ref'] = $month_ref;
		$whereStack['url_profile_id'] = $prof_id;
		$whereStack['report_type'] = 'source_medium';
		$whereStack['account_id'] = $log_user_id;
		$opt = array();
		$opt['offset'] = 0;
		$opt['limit'] = 15;
		$anly_repo['ga_data_source_medium'] = $this->ReportModel->getGAdataDetail($whereStack, $orderBy, $opt);
		$whereStack = array();
		$whereStack['view_id'] = $viewId;
		$whereStack['month_ref'] = $month_ref;
		$whereStack['url_profile_id'] = $prof_id;
		$whereStack['report_type'] = 'landing_page';
		$whereStack['account_id'] = $log_user_id;
		$opt = array();
		$opt['offset'] = 0;
		$opt['limit'] = 15;
		$anly_repo['ga_data_landing_page'] = $this->ReportModel->getGAdataDetail($whereStack, $orderBy, $opt);
		$whereStack = array();
		$whereStack['view_id'] = $viewId;
		$whereStack['month_ref'] = $month_ref;
		$whereStack['url_profile_id'] = $prof_id;
		$whereStack['report_type'] = 'session_graph';
		$whereStack['account_id'] = $log_user_id;
		$opt = array();
		$opt['row_only'] = 1;
		$graph_data = $this->ReportModel->getGAdataDetail($whereStack, $orderBy, $opt);
		$graph_data = json_decode($graph_data['session_data']);
		$chart_graph = array();
		if ($graph_data) {
			foreach ($graph_data as $key => $value) {
				$year = substr($value[0], 0, 4);
				$month = substr($value[0], 4, 2);
				$day = substr($value[0], 6, 2);
				$chart_graph[$key]['date'] = date('d-M-y', strtotime($year . '-' . $month . '-' . $day));
				$chart_graph[$key]['sess'] = $value[1];
			}
		}		
		$anly_repo['report_setting'] = $report_setting;
		$anly_repo['chart_graph'] = $chart_graph;
		$anly_repo['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAnalytics', $anly_repo, true);
		$anly_repo['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAnalytic', $anly_repo, true);
		$inner['ganalytic_report'] = $this->load->view('ganalytic_report', $anly_repo, true);
		$inner['ganalytic_report_js'] = $this->load->view('ganalytic_report_js', $anly_repo, true);
		$adw_repo = array();		
		$adw_repo['report_setting'] = $report_setting;
		$fetch_prof_id = $prodDet['id'];
		$adword_acc_id = $prodDet['linked_adwords_acc_id'];
		$adw_repo['assoc_prof_id'] = $fetch_prof_id;
		$adw_repo['linked_adwords'] = $this->SocialModel->getAdwordProjDetail($adword_acc_id);
		$adw_repo['assoc_links'] = $this->ReportModel->getAccountGoogleAnalyticProfileAssos($log_user_id);
		$ga_data = $this->ReportModel->getAccAdwordsData($log_user_id, $adword_acc_id, "", $fetch_prof_id);
		$adw_repo['ga_data'] = com_make2dArray($ga_data, 'month_ref');
		$adw_repo['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAdwords', $adw_repo, true);
		$adw_repo['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAdwords', $adw_repo, true);

		$inner['gad_report'] = $this->load->view('gad_report', $adw_repo, true);
		$inner['gad_report_js'] = $this->load->view('gad_report_js', $adw_repo, true);

		$fetch_prof_id = $prodDet['id'];
		$locs = explode(",", $prodDet['linked_google_page_location']);
		$gmb_repo['gmb_data'] = $gmb_repo['gmb_locs'] = array();
		$gmb_data = $gmb_locs = array();
		$gmb_loc_id = '';
		foreach ($locs as $loc) {
			$gmb_rawdata = $this->ReportModel->fetchUrlGoogleMyBusiness($fetch_prof_id, $loc, 13);
			foreach ($gmb_rawdata as $key => $value) {
				if (!$gmb_loc_id) {
					$gmb_loc_id = $value['location_name'];
				}
				$gmb_locs[$value['location_name']] = $value['account_page_location_place'];
				$monthRefReport = date("F Y", strtotime($value['month_ref'] . '-01'));
				$gmb_data[$value['location_name']][] =
				array(
					$monthRefReport,
					$value['actions_website'],
					$value['actions_driving_directions'],
					$value['actions_phone'],
				);
			}
		}
		$gmb_repo['gmb_data'] = $gmb_data;
		$gmb_repo['gmb_locs'] = $gmb_locs;
		$gmb_repo['gmb_loc_id'] = $gmb_loc_id;
		$gmb_repo['report_setting'] = $report_setting;
		$inner['gmb_report'] = $this->load->view('gmb_report', $gmb_repo, true);
		$inner['gmb_report_js'] = $this->load->view('gmb_report_js', $gmb_repo, true);

		$rankProfile = $this->ReportModel->getRankinityProfile($profId);
		$rank_repo = array();
		$profileEngines = $this->ReportModel->getRankinityProfileEngines($rankProfile['rankinity_project_id'], $profId);
		$profileEnginesRanks = array();
		$profileEnginesVisibility = array();
		$profileEnginesVisibilityHistory = array();
		foreach ($profileEngines as $engine) {
			$profileEnginesRanks[$engine['engine_id']] =
			$this->ReportModel->getRankinityProfileEngineRanks($engine['project_id'], $engine['engine_id'], $profId);
			$profileEnginesVisibility[$engine['engine_id']] =
			$this->ReportModel->getRankinityProfileEngineVisibility($engine['project_id'], $engine['engine_id'], $profId);
			$profileEnginesVisibilityHistory[$engine['engine_id']] = $this->buildHistory($profileEnginesVisibility[$engine['engine_id']]);
		}
		$rank_repo['rankProfile'] = $rankProfile;
		$rank_repo['profileEngines'] = $profileEngines;
		$rank_repo['profileEnginesRanks'] = $profileEnginesRanks;
		$rank_repo['profileEnginesVisibility'] = $profileEnginesVisibility;
		$rank_repo['profileEnginesVisibilityHistory'] = $profileEnginesVisibilityHistory;

		$inner['rankinity_report'] = $this->load->view('rankinity_report', $rank_repo, true);
		$inner['rankinity_report_js'] = $this->load->view('rankinity_report_js', $rank_repo, true);

		$linked_account_id = com_arrIndex($prodDet, 'linked_account_id', '');		
		$cc_repo['prof_id'] = $profId;
		$cc_repo['cc_counts'] = $this->ReportModel->getCitationContentCount($linked_account_id);
		$cc_repo['skip_det_link'] = 1;
		$cc_repo['report_setting'] = $report_setting;
		$inner['citation_content'] = $this->load->view('citation_content', $cc_repo, true);
		$inner['citation_content_js'] = $this->load->view('citation_content_js', $cc_repo, true);

		$trello = array();
		$trelloToken = com_arrIndex($prodDet, 'trello_access_token', '');
		$trello_board_id = com_arrIndex($prodDet, 'linked_trello_board_id', '');
		if ($trelloToken && $trello_board_id) {
			$trello['board'] = $trello['cards'] = array();
			$trello['cardLists'] = array('curr_mon' => '', 'last_mon' => '');
			$cards = $cardLists = array('curr_mon' => '', 'last_mon' => '');
			$query = http_build_query([
				'key' => TRELLO_DEV_KEY,
				'token' => $trelloToken,
			]);
			$opt = array();
			$opt['url'] = "https://api.trello.com/1/boards/" . $trello_board_id . '/cards?' . $query;
			$cards = $this->curlRequest($opt);
			$cards = json_decode($cards);
			list($currMonth, $lastMonth) = com_lastMonths(2, "", true);
			$tmNames = array(date("F", strtotime($currMonth)), date("M", strtotime($currMonth)));
			$lmNames = array(date("F", strtotime($lastMonth)), date("M", strtotime($lastMonth)));
			$query = http_build_query([
				'key' => TRELLO_DEV_KEY,
				'token' => $trelloToken,
			]);
			$opt = array();
			$opt['url'] = "https://api.trello.com/1/boards/" . $trello_board_id . '/lists?' . $query;
			$blists = $this->curlRequest($opt);
			$blists = json_decode($blists);
			$cYear = date("Y", time());
			if ($blists) {
				foreach ($blists as $blist) {
					if (!$cardLists['curr_mon']) {
						foreach ($tmNames as $tmName) {
							if ((strpos($blist->name, $tmName) !== FALSE)
								&& (strpos($blist->name, $cYear) !== FALSE)) {
								$cardLists['curr_mon'] = $blist;
								break;
							}
						}
					}
					if (!$cardLists['last_mon']) {
						foreach ($lmNames as $lmName) {
							if ((strpos($blist->name, $lmName) !== FALSE)
								&& (strpos($blist->name, $cYear) !== FALSE)) {
								$cardLists['last_mon'] = $blist;
								break;
							}
						}
					}
				}
			}
			$trello['board'] = $this->ReportModel->getBoardDetail($trello_board_id);
			$trello['cards'] = $cards;
			$trello['cardLists'] = $cardLists;
			$trello['skip_det_link'] = 1;
			$trello['report_setting'] = $report_setting;
			$inner['tboard_report'] = $this->load->view('tboard_report', $trello, true);
			$inner['tboard_report_js'] = $this->load->view('tboard_report_js', $trello, true);
		}

		$webmaster['full_report_show'] = true;
		$opt = array();
		$opt['row_only'] = 1;
		$webmaster['kpis'] = $this->ReportModel->getWebmasterData($profId, 'kpi', $opt);
		$opt = array();
		$opt['limit'] = '25';
		$webmaster['queries'] = $this->ReportModel->getWebmasterData($profId, 'query', $opt);
		$webmaster['pages'] = $this->ReportModel->getWebmasterData($profId, 'page');
		$opt = array();
		$opt['limit'] = '13';
		$opt['order'] = 'month_ref desc';
		$webmaster['months'] = $this->ReportModel->getWebmasterData($profId, 'month', $opt);
		$webmaster['report_setting'] = $report_setting;
		$inner['gwmaster_report'] = $this->load->view('gwmaster_report', $webmaster, true);
		$inner['gwmaster_report_js'] = $this->load->view('gwmaster_report_js', $webmaster, true);

		$inner['show_public_url'] = !$publicView;
		$inner['profDet'] = $prodDet;
		$shell['content'] = $this->load->view('fullreport_report', $inner, true);
		$shell['footer_js'] = $this->load->view('fullreport_report_js', $inner, true);
		$shell['page_title'] = 'Full Report';
		// $this->load->view(TMP_DASHBOARD, $shell);
		$temp = TMP_DEFAULT;
		if ($publicView) {
			$temp = TMP_PREPORT;
		}
		$this->load->view($temp, $shell);
	}
}