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
		if( isset($opt[ 'row_only' ]) ){
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

	public function updateWebmasterData($data, $where, $rowData = true) {
		$this->db->where($where)
			->delete('account_url_profile_webmaster_data');
		if ($rowData) {
			$this->db->insert('account_url_profile_webmaster_data', $data);
		} else {
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
		if ($profiles->num_rows() > 1) {
			return $profiles->result_array();
		}
		return $profiles->row_array();
	}

	public function getServiceUrlCost($service_url, $acc_id) {
		$service_url = com_get_domain( $service_url );
		return $this->db->select('SUBSTR(`url`, POSITION("'.$service_url.'" IN `url`), '.strlen( $service_url ).' ) `url`, 
			sum( monthly_price ) as `price`, services_master.name `services`')
			->from('services')
			->join('services_master', 'services_master.id=service_master_id')
			->where('account_id', $acc_id)
			->like('url', $service_url)
			->group_by('SUBSTR(`url`, POSITION("'.$service_url.'" IN `url`), '.strlen( $service_url ).' ), services')
			->get()->result_array();
	}

	public function getBoardDetail( $boardId ){
		return $this->db->from( 'trello_boards' )
				->where( 'board_id', $boardId)
				->get()->row_array();
	}
}