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
		$inner['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAnalytics', $inner, true);
		$inner['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAnalytic', $inner, true);
		$inner['prodDet'] = $prodDet;
		$inner['show_public_url'] = !$publicView;
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
		$inner['prodDet'] = $prodDet;
		$inner['show_public_url'] = !$publicView;
		$inner['kpis'] = $this->ReportModel->getWebmasterData($fetch_prof_id, 'kpi');
		$opt = array();
		$opt['limit'] = '25';
		$inner['queries'] = $this->ReportModel->getWebmasterData($fetch_prof_id, 'query', $opt);
		$inner['pages'] = $this->ReportModel->getWebmasterData($fetch_prof_id, 'page');
		$inner['months'] = $this->ReportModel->getWebmasterData($fetch_prof_id, 'month');
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

	public function link_webmaster($prof_id) {
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		if (!$prodDet) {
			redirect('accounts/list');
			exit;
		}
		$this->breadcrumb->addElement('Google Webmaster', 'report/link_webmaster/' . $prof_id);
		$this->form_validation->set_rules('webmaster_sites', 'Webmaster Site', 'required');
		if ($this->form_validation->run() == false) {
			$inner = array();
			$shell = array();
			$profile_id = $prodDet['id'];
			$inner['profDet'] = $prodDet;
			$inner['webmaster_sites'] = com_makelist($this->ReportModel->getAccountWebmasterProfiles($profile_id),
				'site_url', 'site_url', false);
			$shell['page_title'] = 'Google Analytic';
			$shell['content'] = $this->load->view('gwmaster_report', $inner, true);
			$shell['footer_js'] = $this->load->view('gwmaster_report_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {
			$webmaster_site = $this->input->post('webmaster_sites');
			$data = array();
			$data['linked_webmaster_site'] = $webmaster_site;
			$this->ReportModel->updateProfileAnalytic($prof_id, $data);
			$opt = array();
			$opt['prod'] = 'webmaster';
			$opt['profId'] = $prodDet['id'];
			$opt['log_user_id'] = com_user_data('id');
			$opt['refresh_token'] = $prodDet['gsc_refresh_token'];
			$opt['access_token'] = $prodDet['gsc_access_token'];
			$client_token = $this->loaddata->updateGoogleTokens(true, $opt);
			$client = $client_token['client'];
			$webMaster = new Google_Service_Webmasters($client);
			$prodDet = $this->AccountModel->getProfileDetail($prof_id);

			$flds = array('month_ref' => '', 'queries' => '', 'pages' => '', 'report_type' => '',
				'clicks' => '', 'ctr' => '', 'impressions' => '', 'server_error' => '', 'url_profile_id' => '',
				'soft_404' => '', 'not_found' => '', 'other' => '', 'total_pages' => '', 'total_links' => '',
			);
			// webmasters.urlcrawlerrorscounts.query

			$wmKpi = $flds;
			$wmKpi['report_type'] = 'kpi';
			$wmKpi['url_profile_id'] = $prof_id;
			$kpiStack = array();
			$kpiStack['other'] = 'other';
			$kpiStack['soft404'] = 'soft_404';
			$kpiStack['notFound'] = 'not_found';
			$kpiStack['serverError'] = 'server_error';
			foreach ($kpiStack as $kKey => $kVal) {
				$opts = array();
				$opts['platform'] = 'web';
				$opts['category'] = $kKey;
				$webResult = $webMaster->urlcrawlerrorscounts->query($webmaster_site, $opts);
				if ($webResult && isset($webResult->countPerTypes)) {
					if (isset($webResult->countPerTypes[0])) {
						$typeDet = $webResult->countPerTypes[0];
						if ($typeDet->entries && isset($typeDet->entries[0])) {
							$wmKpi[$kVal] = $typeDet->entries[0]->count;
						}
					}
				}
			}
			$sday_month = date('Y-m-01', time());
			$lday_month = date('Y-m-t', time());
			// webmasters.searchanalytics.query
			$queries = $pages = array();
			$kTopFilter = array('query' => array('limit' => 50, 'fld' => 'queries'),
				'page' => array('limit' => 20, 'fld' => 'pages'));
			foreach ($kTopFilter as $Kfilter => $filterDet) {
				$reqObj = new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
				// responseAggregationType,rows
				$reqObj->setRowLimit($filterDet['limit']);
				$reqObj->setDimensions(array($Kfilter));
				$reqObj->setEndDate($lday_month);
				$reqObj->setStartDate($sday_month);
				$webResult = $webMaster->searchanalytics->query($webmaster_site, $reqObj);
				if ($webResult && $webResult->rows) {
					foreach ($webResult->rows as $rKey => $rData) {
						${$filterDet['fld']}[$rKey] = $flds;
						$keyRef = implode(",", $rData->keys);
						${$filterDet['fld']}[$rKey]['report_type'] = $Kfilter;
						${$filterDet['fld']}[$rKey]['url_profile_id'] = $prof_id;
						${$filterDet['fld']}[$rKey][$filterDet['fld']] = $keyRef;
						${$filterDet['fld']}[$rKey]['clicks'] = $rData->clicks;
						${$filterDet['fld']}[$rKey]['ctr'] = $rData->ctr;
						${$filterDet['fld']}[$rKey]['impressions'] = $rData->impressions;
					}
				}
			}
			$months_tstamps = com_lastMonths(13);
			$web_month_data = array();
			foreach ($months_tstamps as $mtstamp => $mDate) {
				$month_ref = date('Y-m-t', $mtstamp);
				$lday_month = date('Y-m-t', $mtstamp);
				$sday_month = date('Y-m-01', $mtstamp);
				$reqObj = new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
				$reqObj->setEndDate($lday_month);
				$reqObj->setStartDate($sday_month);
				$webResult = $webMaster->searchanalytics->query($webmaster_site, $reqObj);
				if ($webResult && $webResult->rows) {
					foreach ($webResult->rows as $rKey => $rData) {
						$web_month_data[$month_ref] = $flds;
						$web_month_data[$month_ref]['month_ref'] = $month_ref;
						$web_month_data[$month_ref]['report_type'] = 'month';
						$web_month_data[$month_ref]['url_profile_id'] = $prof_id;
						$web_month_data[$month_ref]['clicks'] = $rData->clicks;
						$web_month_data[$month_ref]['ctr'] = $rData->ctr;
						$web_month_data[$month_ref]['impressions'] = $rData->impressions;
					}
				}
			}
			$where = array();
			$where['report_type'] = 'kpi';
			$where['url_profile_id'] = $prof_id;
			$this->ReportModel->updateWebmasterData($wmKpi, $where);
			$where['report_type'] = 'query';
			$where['url_profile_id'] = $prof_id;
			$this->ReportModel->updateWebmasterData($queries, $where, false);
			$where['report_type'] = 'page';
			$where['url_profile_id'] = $prof_id;
			$this->ReportModel->updateWebmasterData($pages, $where, false);
			$where['report_type'] = 'month';
			$where['url_profile_id'] = $prof_id;
			$this->ReportModel->updateWebmasterData($web_month_data, $where, false);

			redirect('dashboard');
			exit;
		}
	}

	public function getViewData() {
		$viewId = $this->input->post('view');
		$propertyId = $this->input->post('prop');
		$profileId = $this->input->post('profile');
		$relProfId = $this->input->post('prof_id');
		$prodDet = $this->AccountModel->getProfileDetail($relProfId);
		$inner = $out = array();
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
			$inner = $this->fetchPropViewAnalyticData($analytics, $viewId, $prodDet['id']);
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

	private function fetchPropViewAnalyticData($analytics, $viewId, $profId) {
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$months_tstamps = com_lastMonths(13);
		$ga_data = array();
		$ga_data_organic = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->fetchTotalTraffic($analytics, $month_ref, $viewId, $profId);
			$gaDataOrganic = $this->fetchTotalTrafficOrganic($analytics, $month_ref, $viewId, $profId);
			$gaData['month_ref'] = date('F Y', $mtstamp);
			$gaDataOrganic['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
			$ga_data_organic[$month_ref] = $gaDataOrganic;
		}
		$out = array();
		$out['ga_data'] = $ga_data;
		$out['ga_data_organic'] = $ga_data_organic;
		$out['ga_data_medium'] = $this->fetchMediumPerformance($analytics, $viewId, $profId);
		$out['ga_data_source_medium'] = $this->fetchSourMediumPerformance($analytics, $viewId, $profId);
		$out['ga_data_landing_page'] = $this->fetchLandingPagePerformance($analytics, $viewId, $profId);
		return $out;
	}

	private function fetchTotalTraffic($analytics, $month_ref, $viewId, $profId) {
		$logUserId = com_user_data('id');
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$gaData = $this->ReportModel->fetchViewAnalyticData($month_ref, $viewId, $profId);
		if (!$gaData || in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
			$mtstamp = strtotime($month_ref . '-01');
			$sday_month = date('Y-m-01', $mtstamp);
			$lday_month = date('Y-m-t', $mtstamp);
			$opt = array();
			$opt['dimensions'] = 'ga:medium, ga:sourceMedium, ga:landingPagePath';
			$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
				$sday_month, $lday_month,
				'ga:sessions, ga:users, ga:newUsers, ga:percentNewSessions, ga:pageviews, ga:avgSessionDuration, ga:bounceRate, ga:avgPageDownloadTime, ga:goalConversionRateAll, ga:goalCompletionsAll');
			$allDetailRows = $ga_rstdata->rows;
			$pview_per_sessions = 0;
			if ($ga_rstdata->totalsForAllResults['ga:pageviews'] && $ga_rstdata->totalsForAllResults['ga:sessions']) {
				$pview_per_sessions = $ga_rstdata->totalsForAllResults['ga:pageviews'] / $ga_rstdata->totalsForAllResults['ga:sessions'];
			}
			$gaData['view_id'] = $viewId;
			$gaData['account_id'] = $logUserId;
			$gaData['month_ref'] = $month_ref;
			$gaData['url_profile_id'] = $profId;
			$gaData['page_view_per_sessions'] = $pview_per_sessions;
			$gaData['users'] = $ga_rstdata->totalsForAllResults['ga:users'];
			$gaData['sessions'] = $ga_rstdata->totalsForAllResults['ga:sessions'];
			$gaData['new_users'] = $ga_rstdata->totalsForAllResults['ga:newUsers'];
			$gaData['page_views'] = $ga_rstdata->totalsForAllResults['ga:pageviews'];
			$gaData['bounce_rate'] = $ga_rstdata->totalsForAllResults['ga:bounceRate'];
			$gaData['per_new_sessions'] = $ga_rstdata->totalsForAllResults['ga:percentNewSessions'];
			$gaData['goal_completion_all'] = $ga_rstdata->totalsForAllResults['ga:goalCompletionsAll'];
			$gaData['avg_session_duration'] = $ga_rstdata->totalsForAllResults['ga:avgSessionDuration'];
			$gaData['avg_page_download_time'] = $ga_rstdata->totalsForAllResults['ga:avgPageDownloadTime'];
			$gaData['goal_conversion_rate'] = $ga_rstdata->totalsForAllResults['ga:goalConversionRateAll'];
			if (in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
				$wStack = array();
				$wStack['view_id'] = $viewId;
				$wStack['month_ref'] = $month_ref;
				$wStack['account_id'] = $logUserId;
				$wStack['url_profile_id'] = $profId;
				$this->ReportModel->updateGAdata($wStack, $gaData);
			} else {
				$this->ReportModel->insertGAdata($gaData);
			}
			unset($gaData['account_id'], $gaData['view_id']);
		}
		return $gaData;
	}

	private function fetchTotalTrafficOrganic($analytics, $month_ref, $viewId, $profId) {
		$logUserId = com_user_data('id');
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$gaData = $this->ReportModel->fetchViewAnalyticDataOrganic($month_ref, $viewId, $profId);
		if (!$gaData || in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
			$mtstamp = strtotime($month_ref . '-01');
			$sday_month = date('Y-m-01', $mtstamp);
			$lday_month = date('Y-m-t', $mtstamp);
			$opt = array();
			$opt['filters'] = 'ga:medium==organic';
			$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
				$sday_month, $lday_month,
				'ga:sessions, ga:users, ga:newUsers, ga:percentNewSessions, ga:pageviews, ga:avgSessionDuration, ga:bounceRate, ga:avgPageDownloadTime, ga:goalConversionRateAll, ga:goalCompletionsAll', $opt);
			$allDetailRows = $ga_rstdata->rows;
			$pview_per_sessions = 0;
			if ($ga_rstdata->totalsForAllResults['ga:pageviews'] && $ga_rstdata->totalsForAllResults['ga:sessions']) {
				$pview_per_sessions = $ga_rstdata->totalsForAllResults['ga:pageviews'] / $ga_rstdata->totalsForAllResults['ga:sessions'];
			}
			$gaData['medium'] = "";
			$gaData['source_medium'] = "";
			$gaData['landing_page'] = "";
			$gaData['account_id'] = $logUserId;
			$gaData['view_id'] = $viewId;
			$gaData['month_ref'] = $month_ref;
			$gaData['report_type'] = 'organic';
			$gaData['url_profile_id'] = $profId;
			$gaData['page_view_per_sessions'] = $pview_per_sessions;
			$gaData['users'] = $ga_rstdata->totalsForAllResults['ga:users'];
			$gaData['sessions'] = $ga_rstdata->totalsForAllResults['ga:sessions'];
			$gaData['new_users'] = $ga_rstdata->totalsForAllResults['ga:newUsers'];
			$gaData['page_views'] = $ga_rstdata->totalsForAllResults['ga:pageviews'];
			$gaData['bounce_rate'] = $ga_rstdata->totalsForAllResults['ga:bounceRate'];
			$gaData['per_new_sessions'] = $ga_rstdata->totalsForAllResults['ga:percentNewSessions'];
			$gaData['goal_completion_all'] = $ga_rstdata->totalsForAllResults['ga:goalCompletionsAll'];
			$gaData['avg_session_duration'] = $ga_rstdata->totalsForAllResults['ga:avgSessionDuration'];
			$gaData['avg_page_download_time'] = $ga_rstdata->totalsForAllResults['ga:avgPageDownloadTime'];
			$gaData['goal_conversion_rate'] = $ga_rstdata->totalsForAllResults['ga:goalConversionRateAll'];
			if (in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
				$wStack = array();
				$wStack['view_id'] = $viewId;
				$wStack['month_ref'] = $month_ref;
				$wStack['report_type'] = 'organic';
				$wStack['account_id'] = $logUserId;
				$wStack['url_profile_id'] = $profId;
				$this->ReportModel->updateGAdataOrganic($wStack, $gaData);
			} else {
				$this->ReportModel->insertGAdataOrganic($gaData);
			}
			unset($gaData['account_id'], $gaData['view_id']);
		}
		return $gaData;
	}

	private function fetchMediumPerformance($analytics, $viewId, $profId) {
		$logUserId = com_user_data('id');
		$sday_month = date('Y-m-01', time());
		$lday_month = date('Y-m-t', time());
		$month_ref = date('Y-m', time());
		$opt = array();
		$opt['dimensions'] = 'ga:medium';
		$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
			$sday_month, $lday_month,
			'ga:sessions, ga:users, ga:newUsers, ga:percentNewSessions, ga:pageviews, ga:avgSessionDuration, ga:bounceRate, ga:avgPageDownloadTime, ga:goalConversionRateAll, ga:goalCompletionsAll', $opt);
		$detailData = array();
		if ($ga_rstdata->rows) {
			foreach ($ga_rstdata->rows as $rIndex => $detRow) {
				$pview_per_sessions = 0;
				if ($detRow[7] && $detRow[3]) {
					$pview_per_sessions = $detRow[7] / $detRow[3];
				}
				$detailData[$rIndex]['view_id'] = $viewId;
				$detailData[$rIndex]['report_type'] = 'medium';
				$detailData[$rIndex]['month_ref'] = $month_ref;
				$detailData[$rIndex]['account_id'] = $logUserId;
				$detailData[$rIndex]['url_profile_id'] = $profId;
				$detailData[$rIndex]['medium'] = $detRow[0];
				$detailData[$rIndex]['source_medium'] = '';
				$detailData[$rIndex]['landing_page'] = "";
				$detailData[$rIndex]['sessions'] = $detRow[1];
				$detailData[$rIndex]['users'] = $detRow[2];
				$detailData[$rIndex]['new_users'] = $detRow[3];
				$detailData[$rIndex]['per_new_sessions'] = $detRow[4];
				$detailData[$rIndex]['page_views'] = $detRow[5];
				$detailData[$rIndex]['page_view_per_sessions'] = $pview_per_sessions;
				$detailData[$rIndex]['avg_session_duration'] = $detRow[6];
				$detailData[$rIndex]['bounce_rate'] = $detRow[7];
				$detailData[$rIndex]['avg_page_download_time'] = $detRow[8];
				$detailData[$rIndex]['goal_conversion_rate'] = $detRow[9];
				$detailData[$rIndex]['goal_completion_all'] = $detRow[10];
			}
			$whereStack = array();
			$whereStack['view_id'] = $viewId;
			$whereStack['month_ref'] = $month_ref;
			$whereStack['account_id'] = $logUserId;
			$whereStack['report_type'] = 'medium';
			$whereStack['url_profile_id'] = $profId;
			$this->ReportModel->updateGAdataDetail($whereStack, $detailData);
		}
		return $detailData;
	}

	private function fetchSourMediumPerformance($analytics, $viewId, $profId) {
		$logUserId = com_user_data('id');
		$month_ref = date('Y-m', time());
		$sday_month = date('Y-m-01', time());
		$lday_month = date('Y-m-t', time());
		$opt = array();
		$opt['dimensions'] = 'ga:sourceMedium';
		$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
			$sday_month, $lday_month,
			'ga:sessions, ga:users, ga:newUsers, ga:percentNewSessions, ga:pageviews, ga:avgSessionDuration, ga:bounceRate, ga:avgPageDownloadTime, ga:goalConversionRateAll, ga:goalCompletionsAll', $opt);
		$detailData = array();
		if ($ga_rstdata->rows) {
			foreach ($ga_rstdata->rows as $rIndex => $detRow) {
				$pview_per_sessions = 0;
				if ($detRow[7] && $detRow[3]) {
					$pview_per_sessions = $detRow[7] / $detRow[3];
				}
				$detailData[$rIndex]['report_type'] = 'source_medium';
				$detailData[$rIndex]['view_id'] = $viewId;
				$detailData[$rIndex]['month_ref'] = $month_ref;
				$detailData[$rIndex]['account_id'] = $logUserId;
				$detailData[$rIndex]['url_profile_id'] = $profId;
				$detailData[$rIndex]['medium'] = '';
				$detailData[$rIndex]['source_medium'] = $detRow[0];
				$detailData[$rIndex]['landing_page'] = "";
				$detailData[$rIndex]['sessions'] = $detRow[1];
				$detailData[$rIndex]['users'] = $detRow[2];
				$detailData[$rIndex]['new_users'] = $detRow[3];
				$detailData[$rIndex]['per_new_sessions'] = $detRow[4];
				$detailData[$rIndex]['page_views'] = $detRow[5];
				$detailData[$rIndex]['page_view_per_sessions'] = $pview_per_sessions;
				$detailData[$rIndex]['avg_session_duration'] = $detRow[6];
				$detailData[$rIndex]['bounce_rate'] = $detRow[7];
				$detailData[$rIndex]['avg_page_download_time'] = $detRow[8];
				$detailData[$rIndex]['goal_conversion_rate'] = $detRow[9];
				$detailData[$rIndex]['goal_completion_all'] = $detRow[10];
			}
			$whereStack = array();
			$whereStack['view_id'] = $viewId;
			$whereStack['month_ref'] = $month_ref;
			$whereStack['account_id'] = $logUserId;
			$whereStack['url_profile_id'] = $profId;
			$whereStack['report_type'] = 'source_medium';
			$this->ReportModel->updateGAdataDetail($whereStack, $detailData);
		}
		return $detailData;
	}

	private function fetchLandingPagePerformance($analytics, $viewId, $profId) {
		$logUserId = com_user_data('id');
		$month_ref = date('Y-m', time());
		$sday_month = date('Y-m-01', time());
		$lday_month = date('Y-m-t', time());
		$opt = array();
		$opt['dimensions'] = 'ga:landingPagePath';
		$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
			$sday_month, $lday_month,
			'ga:sessions, ga:users, ga:newUsers, ga:percentNewSessions, ga:pageviews, ga:avgSessionDuration, ga:bounceRate, ga:avgPageDownloadTime, ga:goalConversionRateAll, ga:goalCompletionsAll', $opt);
		$detailData = array();
		if ($ga_rstdata->rows) {
			foreach ($ga_rstdata->rows as $rIndex => $detRow) {
				$pview_per_sessions = 0;
				if ($detRow[7] && $detRow[3]) {
					$pview_per_sessions = $detRow[7] / $detRow[3];
				}
				$detailData[$rIndex]['report_type'] = 'landing_page';
				$detailData[$rIndex]['view_id'] = $viewId;
				$detailData[$rIndex]['month_ref'] = $month_ref;
				$detailData[$rIndex]['account_id'] = $logUserId;
				$detailData[$rIndex]['url_profile_id'] = $profId;
				$detailData[$rIndex]['medium'] = '';
				$detailData[$rIndex]['source_medium'] = "";
				$detailData[$rIndex]['landing_page'] = $detRow[0];
				$detailData[$rIndex]['sessions'] = $detRow[1];
				$detailData[$rIndex]['users'] = $detRow[2];
				$detailData[$rIndex]['new_users'] = $detRow[3];
				$detailData[$rIndex]['per_new_sessions'] = $detRow[4];
				$detailData[$rIndex]['page_views'] = $detRow[5];
				$detailData[$rIndex]['page_view_per_sessions'] = $pview_per_sessions;
				$detailData[$rIndex]['avg_session_duration'] = $detRow[6];
				$detailData[$rIndex]['bounce_rate'] = $detRow[7];
				$detailData[$rIndex]['avg_page_download_time'] = $detRow[8];
				$detailData[$rIndex]['goal_conversion_rate'] = $detRow[9];
				$detailData[$rIndex]['goal_completion_all'] = $detRow[10];
			}
			$whereStack = array();
			$whereStack['view_id'] = $viewId;
			$whereStack['month_ref'] = $month_ref;
			$whereStack['account_id'] = $logUserId;
			$whereStack['url_profile_id'] = $profId;
			$whereStack['report_type'] = 'landing_page';
			$this->ReportModel->updateGAdataDetail($whereStack, $detailData);
		}
		return $detailData;
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
		$prodDet = $this->AccountModel->getProfileDetail($prof_id);
		$trelloToken = com_arrIndex($prodDet, 'trello_access_token', '');
		if (!$prodDet || !$trelloToken) {
			redirect('accounts/list');
			exit;
		}
		$boards = $this->updateTrelloBoards($trelloToken, $prof_id);
		$this->breadcrumb->addElement('Trello Boards', 'report/tboardreport/' . $prof_id);
		$inner = array();
		$shell = array();
		$log_user_id = com_user_data('id');
		$inner['boards'] = $boards;
		$inner['prodDet'] = $prodDet;
		$shell['page_title'] = 'Trello Boards';
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
		$trello_token = com_arrIndex($prodDet, 'trello_access_token', '');
		if ($trello_token) {
			$query = http_build_query([
				'key' => TRELLO_DEV_KEY,
				'token' => $trello_token,
			]);
			$opt = array();
			$opt['url'] = "https://api.trello.com/1/boards/" . $fdata['board'] . '/cards?' . $query;
			$inner = array();
			$cards = $this->curlRequest($opt);
			$inner['cards'] = json_decode($cards);
			$inner['cardsColor'] = RandomColor::many(count($inner['cards']));
			$out = array();
			$out['cardsHtml'] = $this->load->view('sub_views/boardCard', $inner, true);
		}
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
		$inner['gmb_data'] = $gmb_data;
		$inner['gmb_locs'] = $gmb_locs;
		$inner['gmb_loc_id'] = $gmb_loc_id;
		$shell['page_title'] = 'Google My Business';
		$shell['content'] = $this->load->view('gmb_report', $inner, true);
		$shell['footer_js'] = $this->load->view('gmb_report_js', $inner, true);
		$temp = TMP_DEFAULT;
		if ($publicView) {
			$temp = TMP_PREPORT;
		}
		$this->load->view($temp, $shell);
	}

	public function overview($profId) {
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
		$linkServUrl = $prodDet['linked_service_url'];
		$serviceUrlData = $this->ReportModel->getServiceUrlCost($linkServUrl, $log_user_id);
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

	public function complete_full($profId) {
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
		$linkServUrl = $prodDet['linked_service_url'];
		$serviceUrlData = $this->ReportModel->getServiceUrlCost($linkServUrl, $log_user_id);
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
		$anly_repo['lastMonthHtml'] = $this->load->view('sub_views/lastMonthGAnalytics', $anly_repo, true);
		$anly_repo['currMonthHtml'] = $this->load->view('sub_views/currentMonthGAnalytic', $anly_repo, true);
		$inner['ganalytic_report'] = $this->load->view('ganalytic_report', $anly_repo, true);
		$inner['ganalytic_report_js'] = $this->load->view('ganalytic_report_js', $anly_repo, true);

		$adw_repo = array();
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

		$inner['gmb_report'] = $this->load->view('gmb_report', $gmb_repo, true);
		$inner['gmb_report_js'] = $this->load->view('gmb_report_js', $gmb_repo, true);

		$rankProfile = $this->ReportModel->getRankinityProfile($profId);
		$rank_repo = array();
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
		$rank_repo['rankProfile'] = $rankProfile;
		$rank_repo['profileEngines'] = $profileEngines;
		$rank_repo['profileEnginesRanks'] = $profileEnginesRanks;
		$rank_repo['profileEnginesVisibility'] = $profileEnginesVisibility;
		$rank_repo['profileEnginesVisibilityHistory'] = $profileEnginesVisibilityHistory;

		$inner['rankinity_report'] = $this->load->view('rankinity_report', $rank_repo, true);
		$inner['rankinity_report_js'] = $this->load->view('rankinity_report_js', $rank_repo, true);

		$linked_account_id = com_arrIndex($prodDet, 'linked_account_id', '');
		$cc_repo['cc_counts'] = $this->ReportModel->getCitationContentCount($linked_account_id);

		$inner['citation_content'] = $this->load->view('citation_content', $inner, true);
		$inner['citation_content_js'] = $this->load->view('citation_content_js', $inner, true);

		$inner['prodDet'] = $prodDet;
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