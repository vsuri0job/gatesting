<?php

class ReportModel extends CI_Model {

	public function getAccountGoogleAnalyticProfiles($profile_id) {
		$profiles = array();
		$profiles = $this->db->select('profile_id, profile_name')
			->from('analytic_profiles')
			->where('url_profile_id', $profile_id)
			->get()->result_array();
		return $profiles;
	}

	public function getAccountGoogleAnalyticProfileProps($profile_id) {
		$profiles = array();
		$profiles = $this->db->select(' `account_id`, `profile_id`, `property_id`, `property_name`, `property_website_url`')
			->from('analytic_profile_properties')
			->where('url_profile_id', $profile_id)
			->get()->result_array();
		return $profiles;
	}

	public function getAccountGoogleAnalyticProfilePropViews($profile_id) {
		$profiles = array();
		$profiles = $this->db->select(' `account_id`, `property_id`, `view_id`, `view_name` ')
			->from('analytic_profile_property_views')
			->where('url_profile_id', $profile_id)
			->get()->result_array();
		return $profiles;
	}

	public function getAccountWebmasterProfiles($profile_id) {
		$profiles = array();
		$profiles = $this->db
			->from('google_webmaster_sites')
			->where('url_profile_id', $profile_id)
			->where('permission_level <> ', 'siteUnverifiedUser')
			->get()->result_array();
		return $profiles;
	}

	public function getAccountGoogleAnalyticProfileAssos($account_id) {
		$profiles = array();
		$profiles = $this->db->select(' `account_id`, `property_id`, `adword_link_id`, `adword_link_name`, `adword_refs` ')
			->from('analytic_profile_property_adwords_associations')
			->where('account_id', $account_id)
			->get()->result_array();
		return $profiles;
	}

	public function fetchUrlGoogleMyBusiness($profId, $locationss, $limit = 0) {
		$profiles = array();
		if ($limit) {
			$this->db->limit($limit, 0);
		}
		$profiles = $this->db->select('account_url_profile_gmb_data.*, account_page_location_place')
			->from('account_url_profile_gmb_data')
			->join('google_business_page_locations',
				'account_page_location_name=location_name
			and account_url_profile_gmb_data.url_profile_id=google_business_page_locations.url_profile_id')
			->where('account_url_profile_gmb_data.url_profile_id', $profId)
			->where('location_name', $locationss)
			->order_by('month_ref desc, location_name')
			->get()
			->result_array();
		return $profiles;
	}

	public function fetchUrlGoogleMyBusinessMonthData($profId, $locationss, $limit = 0) {
		$profiles = array();
		if ($limit) {
			$this->db->limit($limit, 0);
		}
		$profiles = $this->db->select('month_ref, sum( `actions_website` ) `clicks`, sum(`actions_phone`) `calls`, sum(`actions_driving_directions`) `direc`')
			->from('account_url_profile_gmb_data')
			->where('account_url_profile_gmb_data.url_profile_id', $profId)
			->where_in('location_name', $locationss)
			->order_by('month_ref desc')
			->group_by('month_ref')
			->get()
			->result_array();
		return $profiles;
	}

	public function fetchViewAnalyticData($monthRef, $viewId, $profId) {
		return $this->db->select('month_ref, sessions, users, page_view_per_sessions, avg_session_duration, bounce_rate,
						avg_page_download_time, goal_conversion_rate, goal_completion_all, page_views, per_new_sessions')
			->from('analytic_profile_property_view_data')
			->where('view_id', $viewId)
			->where('month_ref', $monthRef)
			->where('url_profile_id', $profId)
			->get()->row_array();
	}

	public function fetchViewAdwordData($monthRef, $viewId) {
		return $this->db->select('`month_ref`, `clicks`, `impressions`, `ctr`, `avg_cpc`, `cost`,
					`conversion`, `cost_per_conversion`, `avg_position`, `phone_calls`')
			->from('account_url_profile_adword_data')
			->where('view_id', $viewId)
			->where('month_ref', $monthRef)
			->get()->row_array();
	}

	public function fetchViewAnalyticDataOrganic($monthRef, $viewId, $profId) {
		return $this->db->select('month_ref, sessions, users, page_view_per_sessions, avg_session_duration, bounce_rate,
						avg_page_download_time, goal_conversion_rate, goal_completion_all, page_views, per_new_sessions')
			->from('analytic_profile_property_view_data_detail')
			->where('view_id', $viewId)
			->where('month_ref', $monthRef)
			->where('url_profile_id', $profId)
			->where('report_type', 'organic')
			->get()->row_array();
	}

	public function insertGAdata($udata) {
		return $this->db->insert('analytic_profile_property_view_data', $udata);
	}

	public function updateGAdata($wStack, $udata) {
		$rowId = $this->db->select('id')
			->where($wStack)
			->get('analytic_profile_property_view_data')
			->row_array();
		if ($rowId) {
			return $this->db->where('id', $rowId['id'])
				->update('analytic_profile_property_view_data', $udata);
		} else {
			return $this->db->insert('analytic_profile_property_view_data', $udata);
		}
	}

	public function insertGAdwordData($udata) {
		return $this->db->insert('account_url_profile_adword_data', $udata);
	}

	public function updateGAdwordData($monthRef, $viewId, $udata) {
		return $this->db->where('month_ref', $monthRef)
			->where('view_id', $viewId)
			->update('account_url_profile_adword_data', $udata);
	}

	public function insertGAdataOrganic($udata) {
		return $this->db->insert('analytic_profile_property_view_data_detail', $udata);
	}

	public function updateGAdataOrganic($wStack, $udata) {
		$rowId = $this->db->select('id')
			->where($wStack)
			->get('analytic_profile_property_view_data_detail')
			->row_array();
		if ($rowId) {
			return $this->db->where('id', $rowId['id'])
				->update('analytic_profile_property_view_data_detail', $udata);
		} else {
			return $this->db->insert('analytic_profile_property_view_data_detail', $udata);
		}
	}

	public function insertGAdataDetail($udata) {
		return $this->db->insert_batch('analytic_profile_property_view_data_detail', $udata);
	}

	public function updateGAdataDetail($whereArr, $udata) {
		$this->db->where($whereArr)
			->delete('analytic_profile_property_view_data_detail');
		$this->insertGAdataDetail($udata);
	}

	public function getGAdataDetail($whereArr, $orderBy, $opt = array()) {
		if ($orderBy) {
			$this->db->order_by($orderBy);
		}
		$limit = com_arrIndex($opt, 'limit', 0);
		$offset = com_arrIndex($opt, 'offset', 0);
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$rst_func = 'result_array';
		if (isset($opt['row_only'])) {
			$rst_func = 'row_array';
		}

		return $this->db->where($whereArr)
			->get('analytic_profile_property_view_data_detail')
			->$rst_func();
	}

	public function getTrelloBoards() {
		return $this->db->from('trello_boards')
			->where('account_id', com_user_data('id'))
			->get()->result_array();
	}

	public function getFetchedAccountDetail($account_id) {
		return $this->db->from('account_url_profiles')
			->where('id', $account_id)
			->get()->row_array();
	}

	public function updateProfileAnalytic($profId, $data) {
		$this->db->where('id', $profId)
			->update('account_url_profiles', $data);
	}

	public function getCitationContentCount($account_id = 0) {
		$months_tstamps = com_lastMonths(13);
		$data = array();
		foreach ($months_tstamps as $month_time => $month_date) {
			$month = date("Y-m", $month_time);
			$month_date = date('m/%/Y', $month_time);
			$month_date2 = date('m/%/y', $month_time);
			$data[$month] = array();
			$data[$month]['stamp'] = $month_time;
			$data[$month]['month'] = date("Y F", $month_time);
			if ($account_id) {
				$this->db->where('account_id', $account_id);
			}
			$data[$month]['contents'] = $this->db->select('id')
				->from('contents')
				->where('contents.status', 1)
				->where(' ( content_date like "%' . $month_date . '%" OR content_date like "%' . $month_date2 . '%" ) ')
				->get()->num_rows();
			if ($account_id) {
				$this->db->where('account_id', $account_id);
			}
			$data[$month]['citation'] = $this->db->select('id')
				->from('client_citations')
				->where('status', 1)
				->like('created_at', $month, 'after')
				->get()->num_rows();
		}
		return $data;
	}

	public function citationReport($month_date, $account_id = 0) {
		if ($account_id) {
			$this->db->where('account_id', $account_id);
		}
		return $this->db->select('accounts.name `account_name`, accounts.id as `account_id`,
            citation_status, live_link, directory, login_url, username, password, domain_authority, notes')
			->from('client_citations')
			->join('accounts', 'accounts.id=account_id and client_citations.status = 1')
			->like('client_citations.created_at', $month_date, 'after')
			->get()->result_array();
	}

	public function contentReport($month_dtStamp, $account_id = 0) {
		$month_date = date("m/%/Y", $month_dtStamp);
		$month_date2 = date("m/%/y", $month_dtStamp);
		if ($account_id) {
			$this->db->where('account_id', $account_id);
		}
		return $this->db->select('accounts.name `account_name`, accounts.id as `account_id`,
				            contents.content_date, contents.content_topic_title,
				            contents.keyword_focus, contents.blog_url')
			->from('contents')
			->join('accounts', 'accounts.id=account_id')
			->where('contents.status', 1)
			->where(' ( content_date like "%' . $month_date . '%" OR content_date like "%' . $month_date2 . '%" ) ')
			->get()->result_array();
	}

	public function citationAccountReport($month_date, $account_id = 0) {
		if ($account_id) {
			$this->db->where('account_id', $account_id);
		}
		return $this->db->select('accounts.name `account_name`, accounts.id as `account_id`,
            citation_status, live_link, directory, login_url, username, password, domain_authority, notes')
			->from('client_citations')
			->join('accounts', 'accounts.id=account_id and client_citations.status = 1')
			->like('client_citations.created_at', $month_date, 'after')
			->get()->result_array();
	}

	public function searchPropertyDomainAccount($prop) {
		$out = array();
		$msg = 'Domain reference account has not been found!';
		$linkAccountId = 0;
		$domainName = com_get_domain($prop['property_website_url']);
		if ($domainName) {
			$loginWhere = ' name LIKE "%' . $domainName . '%" or url LIKE "%' . $domainName . '%"';
			$rst = $this->db->distinct()
				->select('account_id')
				->from('logins')
				->where($loginWhere)
				->get()->result_array();
			if ($rst) {
				$msg = $domainName . ' Domain is assigned to multiple accounts';
				if (count($rst) == 1) {
					$msg = '';
					$linkAccountId = $rst[0]['account_id'];
				}
			}
			if (!$linkAccountId) {
				$msg = $domainName . ' Domain reference account has not been found!';
				$rst = $this->db->distinct()
					->select('account_id')
					->from('services')
					->like('url', $domainName)
					->get()->result_array();
				if ($rst) {
					$msg = $domainName . ' Domain is assigned to multiple accounts';
					if (count($rst) == 1) {
						$msg = '';
						$linkAccountId = $rst[0]['account_id'];
					}
				}
			}
		}
		if ($linkAccountId) {
			$linkAccount = $this->getLinkAccountDet($linkAccountId);
			if ($linkAccount) {
				$msg = $domainName . ' Account has been linked to ' . $linkAccount['name'];
			}
		}
		$out['msg'] = $msg;
		$out['linkAccountId'] = $linkAccountId;
		return $out;
	}

	private function getLinkAccountDet($linkAccountId) {
		return $this->db->select('name, email, phone, firstname, lastname')
			->from('accounts')
			->where('id', $linkAccountId)
			->get()->row_array();
	}

	public function getRankinityProfile($profileId) {
		return $this->db->select('account_url_profiles.*,rankinity_projects.*')
			->from('rankinity_projects')
			->join('account_url_profiles', 'rankinity_projects.rankinity_project_id=linked_rankinity_id', 'left')
			->where('account_url_profiles.id', $profileId)
			->get()->row_array();
	}

	public function getRankinityProfileEngines($projectId, $profID) {
		return $this->db->from('rankinity_projects_engines')
			->where('project_id', $projectId)
			->where('url_profile_id', $profID)
			->get()->result_array();
	}

	public function getRankinityProfileEngineVisibility($projectId, $engineId, $profId) {
		return $this->db->from('rankinity_projects_engine_rank_visibility')
			->where('project_id', $projectId)
			->where('url_profile_id', $profId)
			->where('search_engine_id', $engineId)
			->get()->row_array();
	}

	public function getRankinityProfileEngineRanks($projectId, $engineId, $profId) {
		return $this->db->from('rankinity_projects_engine_rank')
			->where('project_id', $projectId)
			->where('url_profile_id', $profId)
			->where('search_engine_id', $engineId)
			->get()->result_array();
	}

	public function getAccAdwordsData($log_user_id, $adword_acc_id, $view_id, $fetched_analytic_id) {
		return $this->db->from('account_url_profile_adword_data')
			->where('view_id', "")
			->where('account_id', $log_user_id)
			->where('adword_acc_id', $adword_acc_id)
			->where('url_profile_id', $fetched_analytic_id)
			->order_by('month_ref', 'desc')
			->limit(13, 0)
			->get()->result_array();
	}

	public function updateWebmasterData($data, $where, $rowData = true, $mRef = array()) {
		if ($mRef) {
			$this->db->where_in("month_ref", $mRef);
		}
		$this->db->where($where)
			->delete('account_url_profile_webmaster_data');
		if ($rowData) {
			$this->db->insert('account_url_profile_webmaster_data', $data);
		} else if ($data) {
			$this->db->insert_batch('account_url_profile_webmaster_data', $data);
		}
	}

	public function getWebmasterData($prof_id, $report_type, $opt = array()) {
		$profiles = array();
		if (isset($opt['order'])) {
			$this->db->order_by($opt['order']);
		}
		if (isset($opt['limit'])) {
			$this->db->limit($opt['limit'], 0);
		}
		$profiles = $this->db->from('account_url_profile_webmaster_data')
			->where('report_type', $report_type)
			->where('url_profile_id', $prof_id)
			->get();
		if (isset($opt['row_only'])) {
			return $profiles->row_array();
		}
		return $profiles->result_array();
	}

	public function getServiceUrlCost($service_url, $acc_id) {
		$service_url = com_get_domain($service_url);
		return $this->db->select('SUBSTR(`url`, POSITION("' . $service_url . '" IN `url`), ' . strlen($service_url) . ' ) `url`,
			sum( monthly_price ) as `price`, services_master.name `services`')
			->from('services')
			->join('services_master', 'services_master.id=service_master_id')
			->where('account_id', $acc_id)
			->like('url', $service_url)
			->group_by('SUBSTR(`url`, POSITION("' . $service_url . '" IN `url`), ' . strlen($service_url) . ' ), services')
			->get()->result_array();
	}

	public function getBoardDetail($boardId) {
		return $this->db->from('trello_boards')
			->where('board_id', $boardId)
			->get()->row_array();
	}

	public function fetchPropViewAnalyticData($analytics, $viewId, $profId, $logUserId, $monthNum) {
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$months_tstamps = com_lastMonths($monthNum);
		$ga_data = array();
		$ga_data_organic = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m', $mtstamp);
			$gaData = $this->fetchTotalTraffic($analytics, $month_ref, $viewId, $profId, $logUserId, false);
			$gaDataOrganic = $this->fetchTotalTrafficOrganic($analytics, $month_ref, $viewId, $profId, $logUserId, false);
			$gaData['month_ref'] = date('F Y', $mtstamp);
			$gaDataOrganic['month_ref'] = date('F Y', $mtstamp);
			$ga_data[$month_ref] = $gaData;
			$ga_data_organic[$month_ref] = $gaDataOrganic;
		}
		$out = array();
		$out['ga_data'] = $ga_data;
		$out['ga_data_organic'] = $ga_data_organic;
		$out['ga_data_graph_data'] = $this->fetchViewGraphData($analytics, $viewId, $profId, $logUserId, false);
		$out['ga_data_medium'] = $this->fetchMediumPerformance($analytics, $viewId, $profId, $logUserId, false);
		$out['ga_data_source_medium'] = $this->fetchSourMediumPerformance($analytics, $viewId, $profId, $logUserId, false);
		$out['ga_data_landing_page'] = $this->fetchLandingPagePerformance($analytics, $viewId, $profId, $logUserId, false);
		return $out;
	}

	private function fetchTotalTraffic($analytics, $month_ref, $viewId, $profId, $logUserId, $skipLiveData = false) {
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$gaData = $this->fetchViewAnalyticData($month_ref, $viewId, $profId);
		if (!$skipLiveData && (!$gaData || in_array($month_ref, array($currMonthRef, $lastMonthRef)))) {
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
				$this->updateGAdata($wStack, $gaData);
			} else {
				$this->insertGAdata($gaData);
			}
			unset($gaData['account_id'], $gaData['view_id']);
		}
		return $gaData;
	}

	private function fetchTotalTrafficOrganic($analytics, $month_ref, $viewId, $profId, $logUserId, $skipLiveData = false) {
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		$gaData = $this->fetchViewAnalyticDataOrganic($month_ref, $viewId, $profId);
		if (!$skipLiveData && (!$gaData || in_array($month_ref, array($currMonthRef, $lastMonthRef)))) {
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
				$this->updateGAdataOrganic($wStack, $gaData);
			} else {
				$this->insertGAdataOrganic($gaData);
			}
			unset($gaData['account_id'], $gaData['view_id']);
		}
		return $gaData;
	}

	private function fetchViewGraphData($analytics, $viewId, $profId, $logUserId, $skipLiveData = false) {
		$sday_month = date('Y-m-01', strtotime('-30 days'));
		$lday_month = date('Y-m-d', time());
		$month_ref = date('Y-m', time());
		if (!$skipLiveData) {
			$opt = array();
			$opt['dimensions'] = 'ga:date';
			$ga_rstdata = $analytics->data_ga->get('ga:' . $viewId,
				$sday_month, $lday_month, 'ga:sessions, ga:goalCompletionsAll', $opt);
			$detailData = array();
			$rIndex = 0;
			$detailData[$rIndex]['view_id'] = $viewId;
			$detailData[$rIndex]['report_type'] = 'session_graph';
			$detailData[$rIndex]['month_ref'] = $month_ref;
			$detailData[$rIndex]['account_id'] = $logUserId;
			$detailData[$rIndex]['url_profile_id'] = $profId;
			$detailData[$rIndex]['medium'] = '';
			$detailData[$rIndex]['source_medium'] = '';
			$detailData[$rIndex]['landing_page'] = '';
			$detailData[$rIndex]['sessions'] = "";
			$detailData[$rIndex]['users'] = "";
			$detailData[$rIndex]['new_users'] = "";
			$detailData[$rIndex]['per_new_sessions'] = "";
			$detailData[$rIndex]['page_views'] = "";
			$detailData[$rIndex]['page_view_per_sessions'] = "";
			$detailData[$rIndex]['avg_session_duration'] = "";
			$detailData[$rIndex]['bounce_rate'] = "";
			$detailData[$rIndex]['avg_page_download_time'] = "";
			$detailData[$rIndex]['goal_conversion_rate'] = "";
			$detailData[$rIndex]['goal_completion_all'] = "";
			$detailData[$rIndex]['session_data'] = json_encode($ga_rstdata->rows);
			$whereStack = array();
			$whereStack['view_id'] = $viewId;
			$whereStack['month_ref'] = $month_ref;
			$whereStack['account_id'] = $logUserId;
			$whereStack['report_type'] = 'session_graph';
			$whereStack['url_profile_id'] = $profId;
			$this->updateGAdataDetail($whereStack, $detailData);
		} else {
			$whereStack = array();
			$whereStack['view_id'] = $viewId;
			$whereStack['month_ref'] = $month_ref;
			$whereStack['account_id'] = $logUserId;
			$whereStack['report_type'] = 'session_graph';
			$whereStack['url_profile_id'] = $profId;
			$detailData = $this->db->from('analytic_profile_property_view_data_detail')
				->where($whereStack)
				->get()->result_array();
		}
		return $detailData;
	}

	private function fetchMediumPerformance($analytics, $viewId, $profId, $logUserId, $skipLiveData = false) {
		$sday_month = date('Y-m-01', time());
		$lday_month = date('Y-m-t', time());
		$month_ref = date('Y-m', time());
		if (!$skipLiveData) {
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
				$this->updateGAdataDetail($whereStack, $detailData);
			}
		} else {
			$whereStack = array();
			$whereStack['view_id'] = $viewId;
			$whereStack['month_ref'] = $month_ref;
			$whereStack['account_id'] = $logUserId;
			$whereStack['report_type'] = 'medium';
			$whereStack['url_profile_id'] = $profId;
			$detailData = $this->db->from('analytic_profile_property_view_data_detail')
				->where($whereStack)
				->get()->result_array();
		}
		return $detailData;
	}

	private function fetchSourMediumPerformance($analytics, $viewId, $profId, $logUserId, $skipLiveData = false) {
		$month_ref = date('Y-m', time());
		$sday_month = date('Y-m-01', time());
		$lday_month = date('Y-m-t', time());
		if (!$skipLiveData) {
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
				$this->updateGAdataDetail($whereStack, $detailData);
			}
		} else {
			$whereStack = array();
			$whereStack['view_id'] = $viewId;
			$whereStack['month_ref'] = $month_ref;
			$whereStack['account_id'] = $logUserId;
			$whereStack['report_type'] = 'medium';
			$whereStack['url_profile_id'] = $profId;
			$detailData = $this->db->from('analytic_profile_property_view_data_detail')
				->where($whereStack)
				->get()->result_array();
		}
		return $detailData;
	}

	private function fetchLandingPagePerformance($analytics, $viewId, $profId, $logUserId, $skipLiveData = false) {
		$month_ref = date('Y-m', time());
		$sday_month = date('Y-m-01', time());
		$lday_month = date('Y-m-t', time());
		if (!$skipLiveData) {
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
				$this->updateGAdataDetail($whereStack, $detailData);
			}
		} else {
			$whereStack = array();
			$whereStack['view_id'] = $viewId;
			$whereStack['month_ref'] = $month_ref;
			$whereStack['account_id'] = $logUserId;
			$whereStack['url_profile_id'] = $profId;
			$whereStack['report_type'] = 'landing_page';
			$detailData = $this->db->from('analytic_profile_property_view_data_detail')
				->where($whereStack)
				->get()->result_array();
		}
		return $detailData;
	}

	public function updateUrlWebMasterData($prodDet, $monthNum) {
		$prof_id = $prodDet['id'];
		$webmaster_site = $prodDet['linked_webmaster_site'];
		$opt = array();
		$opt['prod'] = 'webmaster';
		$opt['profId'] = $prodDet['id'];
		$opt['log_user_id'] = $prodDet['account_id'];
		$opt['refresh_token'] = $prodDet['gsc_refresh_token'];
		$opt['access_token'] = $prodDet['gsc_access_token'];
		$client_token = $this->loaddata->updateGoogleTokens(true, $opt);
		$client = $client_token['client'];
		$webMaster = new Google_Service_Webmasters($client);

		$flds = array('month_ref' => '', 'queries' => '', 'pages' => '', 'report_type' => '',
			'clicks' => '', 'ctr' => '', 'impressions' => '', 'server_error' => '', 'url_profile_id' => '',
			'soft_404' => '', 'not_found' => '', 'other' => '', 'total_pages' => '', 'total_links' => '',
		);
		// webmasters.urlcrawlerrorscounts.query
		$wmKpi = $flds;
		$wmKpi['report_type'] = 'kpi';
		$wmKpi['url_profile_id'] = $prodDet['id'];
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
		$months_tstamps = com_lastMonths($monthNum);
		$web_month_data = array();
		$monthUpRef = array();
		foreach ($months_tstamps as $mtstamp => $mDate) {
			$month_ref = date('Y-m-01', $mtstamp);
			$lday_month = date('Y-m-t', $mtstamp);
			$sday_month = date('Y-m-01', $mtstamp);
			$reqObj = new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
			$reqObj->setEndDate($lday_month);
			$reqObj->setStartDate($sday_month);
			$webResult = $webMaster->searchanalytics->query($webmaster_site, $reqObj);
			if ($webResult && $webResult->rows) {
				foreach ($webResult->rows as $rKey => $rData) {
					$monthUpRef[] = $month_ref;
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
		$this->updateWebmasterData($wmKpi, $where);
		$where['report_type'] = 'query';
		$where['url_profile_id'] = $prof_id;
		$this->updateWebmasterData($queries, $where, false);
		$where['report_type'] = 'page';
		$where['url_profile_id'] = $prof_id;
		$this->updateWebmasterData($pages, $where, false);
		$where['report_type'] = 'month';
		$where['url_profile_id'] = $prof_id;
		$this->updateWebmasterData($web_month_data, $where, false, $monthUpRef);
	}
}