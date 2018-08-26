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

	public function getAccountGoogleAnalyticProfileAssos($account_id) {
		$profiles = array();
		$profiles = $this->db->select(' `account_id`, `property_id`, `adword_link_id`, `adword_link_name`, `adword_refs` ')
			->from('analytic_profile_property_adwords_associations')
			->where('account_id', $account_id)
			->get()->result_array();
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

	public function getGAdataDetail($whereArr, $orderBy) {
		if ($orderBy) {
			$this->db->order_by($orderBy);
		}
		return $this->db->where($whereArr)
			->get('analytic_profile_property_view_data_detail')
			->result_array();
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
		krsort($months_tstamps);
		$data = array();
		foreach ($months_tstamps as $month_time) {
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
		return $this->db->select('rankinity_projects.*')
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
}