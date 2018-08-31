<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crontool extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library( 'adwords' );
		$this->load->library( 'loaddata' );
		$this->load->model( 'ReportModel' );
		$this->load->model( 'AccountModel' );		
	}

    public function index($to = 'World')
    {
            echo "Hello {$to}!".PHP_EOL;
    }

    public function UpdateAccountSocialData(){    	
    	$urlProfiles = $this->db->select( 'account_url_profiles_social_token.*, account_url_profiles.*, 
    							account_url_profiles.profile_id as `analytic_prof_id`,
    							account_url_profiles_social_token.profile_id as `profile_id`' )
    							->from( 'account_url_profiles' )
    							->join( 'account_url_profiles_social_token', 
    								'account_url_profiles_social_token.profile_id=account_url_profiles.id', 'left' )
    							->get()->result_array();		
		foreach( $urlProfiles as $urlProfile ){
			$urlProfile = $this->checkTokenValid( $urlProfile );
			$socialAcc = array('mbusiness' => 'gmb', 
								'analytic' => 'analytic', 
								'adwords' => 'adword');			
			foreach ($socialAcc as $socRef => $fldRef) {
				if( !$urlProfile[ $fldRef."_reset_token" ] ){
					if( $socRef == 'mbusiness' ){

					}
					if( $socRef == 'analytic' ){
						$access_token = $urlProfile[$fldRef."_access_token"];
						$refresh_token = $urlProfile[$fldRef."_refresh_token"];
						$view_id = $urlProfile["view_id"];
						$prop_id = $urlProfile["property_id"];
						if( $access_token && $refresh_token && $view_id && $prop_id){
							$opt = array();
							$opt['prod'] = $socRef;
							$opt['profId'] = $urlProfile['id'];
							$opt['log_user_id'] = $urlProfile[ 'account_id' ];
							$opt['access_token'] = $access_token;
							$opt['refresh_token'] = $refresh_token;
							$client_token = $this->loaddata->updateGoogleTokens(true, $opt);
							$client = $client_token['client'];
							$this->getViewData( $urlProfile, $client);
						}
					}
					if( $socRef == 'adwords' ){
						$access_token = $urlProfile[$fldRef."_access_token"];
						$refresh_token = $urlProfile[$fldRef."_refresh_token"];
						if( $access_token && $refresh_token ){
							$this->updateGoogleAdwordsData( $urlProfile );
						}
					}
				}
			}

			if( $urlProfile[ 'rankinity_access_token' ] && 
				$urlProfile[ 'linked_rankinity_id' ] ){
				$rankProj = array();
				$rankProj[ 'rankinity_project_id' ] = $urlProfile[ 'linked_rankinity_id' ];
				com_e( "Rankinity", 0 );
				$this->AccountModel->linkRankinityAccount($rankinityProj, $urlProfile['id']);
			}

			if( $urlProfile[ 'trello_access_token' ] ){
				// $this->updateTrelloBoards( $urlProfile[ 'trello_access_token' ], 
				// 	$urlProfile['id']);
				com_e( "Trello", 0 );
			}
		}
    }

	public function checkTokenValid($profDet) {
		$socialAcc = array('mbusiness' => 'gmb', 
							'analytic' => 'analytic', 
							'adwords' => 'adword');
		$urlProf = $profDet;
		foreach ($socialAcc as $socRef => $fldRef) {
			$access_token = $profDet["$fldRef_access_token"];
			$refresh_token = $profDet["$fldRef_refresh_token"];
			if( $access_token && $refresh_token){
				$opt = array();
				$opt['prod'] = $socRef;
				$opt['profId'] = $profDet[ 'id' ];
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

	public function getViewData( $profileDet, $gClient ) {
		$viewId = $profileDet[ 'view_id' ];
		$profileId = $profileDet[ 'analytic_prof_id' ];
		$propertyId = $profileDet[ 'property_id' ];
		$relProfId = $this->input->post('prof_id');
		$prodDet = $profileDet;	
		$client = $gClient;		
		$analytics = new Google_Service_Analytics($client);		
		$inner = $this->fetchPropViewAnalyticData($analytics, $viewId, $prodDet['id'], $prodDet['account_id']);
	}


	private function fetchPropViewAnalyticData($analytics, $viewId, $profId, $logUserId) {
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$months_tstamps = array(
			strtotime("-1 months"),
			time(),
		);
		krsort($months_tstamps);
		$ga_data = array();
		$ga_data_organic = array();
		foreach ($months_tstamps as $mtstamp) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->fetchTotalTraffic($analytics, $month_ref, $viewId, $profId, $logUserId);
			$gaDataOrganic = $this->fetchTotalTrafficOrganic($analytics, $month_ref, $viewId, $profId, $logUserId);
			$gaData['month_ref'] = date('F Y', $mtstamp);
			$gaDataOrganic['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
			$ga_data_organic[$month_ref] = $gaDataOrganic;
		}
		$out = array();
		$out['ga_data'] = $ga_data;
		$out['ga_data_organic'] = $ga_data_organic;
		$out['ga_data_medium'] = $this->fetchMediumPerformance($analytics, $viewId, $profId, $logUserId);
		$out['ga_data_source_medium'] = $this->fetchSourMediumPerformance($analytics, $viewId, $profId, $logUserId);
		$out['ga_data_landing_page'] = $this->fetchLandingPagePerformance($analytics, $viewId, $profId);
		return $out;
	}

	private function fetchTotalTraffic($analytics, $month_ref, $viewId, $profId, $logUserId) {
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

	private function fetchTotalTrafficOrganic($analytics, $month_ref, $viewId, $profId, $logUserId) {
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

	private function fetchMediumPerformance($analytics, $viewId, $profId, $logUserId) {		
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

	private function fetchSourMediumPerformance($analytics, $viewId, $profId, $logUserId) {
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

	public function updateGoogleAdwordsData($profileDet) {
			$access_token = $profileDet['adword_access_token'];
			$refresh_token = $profileDet['adword_refresh_token'];
			$fld_con = array();
			$fld_con['CTR'] = 'ctr';
			$fld_con['Cost'] = 'cost';
			$fld_con['Clicks'] = 'clicks';
			$fld_con['Avg.CPC'] = 'avg_cpc';
			$fld_con['Avg.Cost'] = 'avg_cost';
			$fld_con['CampaignID'] = 'campaign_id';
			$fld_con['Impressions'] = 'impressions';
			$fld_con['Conversions'] = 'conversion';
			$fld_con['Cost/conv.'] = 'cost_per_conversion';
			$fld_con['Avg.position'] = 'avg_position';
			$this->load->library('CSVReader');
			$log_user_id = $profileDet[ 'account_id' ];
			$linked_adword_acc_id = $profileDet[ 'linked_adwords_acc_id' ];
			$months_tstamps = array(
				strtotime("-1 months"),
				time(),
			);
			krsort($months_tstamps);
			$currMonthRef = date('Y-m', time());
			$lastMonthRef = date('Y-m', strtotime("-1 months"));
			foreach ($months_tstamps as $mtstamp) {
				$adw_msm = array();
				$adw_mdt = array();
				$month_ref = date('Y-m', $mtstamp);
				$sday_month = date('Ym01', $mtstamp);
				$lday_month = date('Ymt', $mtstamp);
				$gaData = $this->SocialModel->fetchViewAdwordData($month_ref, $log_user_id,
					$linked_adword_acc_id, "", $profileDet[ 'id' ]);
				// $gaData = null;
				$params = array();
				$params['clientCustomerId'] = $linked_adword_acc_id;
				$params['prod'] = 'adwords';
				$params['profId'] = $profileDet[ 'id' ];
				$params['log_user_id'] = $log_user_id;
				$params['access_token'] = $access_token;
				$params['refresh_token'] = $refresh_token;				
				if (!$gaData || in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
					$downloadedReportPath = $this->adwords->getAdwordsData($params, $sday_month, $lday_month);
					$data = $this->csvreader->parse_file($downloadedReportPath);
					$lIndex = count($data);
					foreach ($data as $dIndex => $dDet) {
						if ($dIndex < ($lIndex - 1)) {
							// $fld_con
							$adw_mdt[$dIndex]['view_id'] = "";
							$adw_mdt[$dIndex]['month_ref'] = $month_ref;
							$adw_mdt[$dIndex]['url_profile_id'] = $profileDet[ 'id' ];
							$adw_mdt[$dIndex]['account_id'] = $log_user_id;
							$adw_mdt[$dIndex]['adword_acc_id'] = $linked_adword_acc_id;
							foreach ($dDet as $dK => $dV) {
								$kText = isset($fld_con[$dK]) ? $fld_con[$dK] : '';
								if ($kText) {
									$adw_mdt[$dIndex][$kText] = $dV;
								}
							}
						} else {
							$adw_msm[$dIndex]['view_id'] = "";
							$adw_msm[$dIndex]['month_ref'] = $month_ref;
							$adw_msm[$dIndex]['url_profile_id'] = $profileDet[ 'id' ];
							$adw_msm[$dIndex]['account_id'] = $log_user_id;
							$adw_msm[$dIndex]['adword_acc_id'] = $linked_adword_acc_id;
							foreach ($dDet as $dK => $dV) {
								$kText = isset($fld_con[$dK]) ? $fld_con[$dK] : '';
								if ($kText) {
									$adw_msm[$dIndex][$kText] = $dV;
								}
							}
							$adw_msm[$dIndex]['campaign_id'] = "";
						}
					}
					$where = array();
					$where['view_id'] = "";
					$where['month_ref'] = $month_ref;
					$where['account_id'] = $log_user_id;
					$where['url_profile_id'] = $profileDet[ 'id' ];
					$where['adword_acc_id'] = $linked_adword_acc_id;
					$this->SocialModel->updateUserGoogleAdwordsProfilePropertyViewGData($adw_msm, $where);
					$this->SocialModel->updateUserGoogleAdwordsProfilePropertyViewGDataDet($adw_mdt, $where);
				}
			}

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