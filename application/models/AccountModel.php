<?php

class AccountModel extends CI_Model {

	public function getAgencyAccounts($agencies = array()) {

		$rst = array();
		if ($agencies) {
			$rst = $this->db->select('accounts.id, accounts.agency_id, accounts.name,
						accounts.email, accounts.parent_account_id, accounts.firstname, accounts.lastname,
						accounts.account_status, accounts.report_logo,
						accounts.status, agencies.name `agency_name`,
						group_concat( services_master.name ) `services`')
				->from('accounts')
				->join('agencies', 'agency_id=agencies.id')
				->join('services', 'services.account_id=accounts.id and services.status = 1')
				->join('services_master', 'services_master.id=service_master_id and services_master.status = 1')
				->where_in('agency_id', $agencies)
				->where_in('accounts.status', 1)
				->group_by('accounts.id, accounts.agency_id, accounts.name,
						accounts.email, accounts.parent_account_id, accounts.firstname, accounts.lastname,
						accounts.account_status, accounts.report_logo,
						accounts.status,agency_name')
				->get()
				->result_array();
		}
		return $rst;
	}

	public function getAccountDetail($account_id, $agencies = array()) {
		$rst = array();
		if ($account_id) {
			$rst = $this->db->select('accounts.id, accounts.agency_id, accounts.name,
						accounts.email, accounts.parent_account_id, accounts.firstname, accounts.lastname,
						accounts.account_status, accounts.report_logo,
						accounts.status, agencies.name `agency_name`')
				->from('accounts')
				->join('agencies', 'agency_id=agencies.id')
				->where_in('agency_id', $agencies)
				->where_in('accounts.status', 1)
				->where('accounts.id', $account_id)
				->get()->row_array();
		}
		return $rst;
	}

	public function updateAccount($account, $extra = array()) {
		$userdata = array();
		if ($extra) {
			$userdata = array_merge($userdata, $extra);
		}
		if ($userdata) {
			$this->db->where('id', $account['id'])
				->update('accounts', $userdata);
		}
	}

	public function getFetchedAccounts() {
		return $this->db->select('analytic_profiles.profile_name,
					analytic_profile_properties.property_name,
					analytic_profile_properties.property_website_url,
					analytic_profile_property_views.view_name, account_url_profiles.modified_at,
					account_url_profiles.id, account_url_profiles.linked_account_id, accounts.name `account_name`,
					rankinity_projects.id `rankinity_ref`,
					rankinity_projects.rankinity_project_name `rankinity_name`,
					group_concat( UPPER(services_master.name) ) as `services`,
					linked_adwords_acc_id, linked_google_page, linked_google_page_location, linked_google_page_id')
			->from('account_url_profiles')
			->join('analytic_profiles', 'account_url_profiles.profile_id=analytic_profiles.profile_id')
			->join('analytic_profile_properties', 'account_url_profiles.profile_id=analytic_profile_properties.profile_id
						and account_url_profiles.property_id=analytic_profile_properties.property_id')
			->join('analytic_profile_property_views', 'analytic_profile_property_views.property_id=analytic_profile_properties.property_id
						and analytic_profile_property_views.view_id=account_url_profiles.view_id')
			->join('accounts', 'accounts.id=account_url_profiles.linked_account_id', 'left')
			->join('services', 'services.account_id=account_url_profiles.linked_account_id and services.status = 1', 'left')
			->join('services_master', 'services.service_master_id=services_master.id and services_master.status = 1', 'left')
			->join('rankinity_projects',
				'rankinity_projects.analytic_profile_id=account_url_profiles.id', 'left')
			->where('account_url_profiles.account_id', com_user_data('id'))
			->where('account_url_profiles.linked_account_id <> ', 0)
			->group_by('analytic_profiles.profile_name, analytic_profile_properties.property_name,
						analytic_profile_properties.property_website_url, analytic_profile_property_views.view_name,
						account_url_profiles.modified_at, account_url_profiles.id,
						account_url_profiles.linked_account_id, account_name, rankinity_ref, rankinity_name,
						linked_adwords_acc_id, linked_google_page, linked_google_page_location, linked_google_page_id')
			->get()->result_array();
	}

	public function getAccounts() {
		$rst = $this->db->select('accounts.id, accounts.agency_id, accounts.name,
					accounts.email, accounts.parent_account_id, accounts.firstname, accounts.lastname,
					accounts.account_status, accounts.report_logo,
					accounts.status, agencies.name `agency_name`')
			->from('accounts')
			->join('agencies', 'agency_id=agencies.id')
			->where_in('accounts.status', 1)
			->get()->result_array();
		return $rst;
	}

	public function getFetchedAccountDetail($aId) {
		return $this->db->select('analytic_profiles.profile_name, analytic_profile_properties.property_name,
					analytic_profile_property_views.view_name, account_url_profiles.modified_at,
					account_url_profiles_social_token.*,
					account_url_profiles.linked_account_id, linked_adwords_acc_id, linked_google_page,
					linked_google_page_location, linked_google_page_id,
					account_url_profiles.profile_id,
					analytic_profile_property_views.view_id,
					account_url_profiles.property_id,
					account_url_profiles.id')
			->from('account_url_profiles')
			->join('account_url_profiles_social_token', 'account_url_profiles_social_token.profile_id=account_url_profiles.id')
			->join('analytic_profiles', 'account_url_profiles.profile_id=analytic_profiles.profile_id', 'left')
			->join('analytic_profile_properties', 'account_url_profiles.profile_id=analytic_profile_properties.profile_id
						and account_url_profiles.property_id=analytic_profile_properties.property_id', 'left')
			->join('analytic_profile_property_views', 'analytic_profile_property_views.property_id=analytic_profile_properties.property_id
						and analytic_profile_property_views.view_id=account_url_profiles.view_id', 'left')
			->where('account_url_profiles.account_id', com_user_data('id'))
			->where('account_url_profiles.id', $aId)
			->get()->row_array();
	}

	public function linkAnalyticAccount($analytic_id, $account_id) {
		$data = array();
		$data['linked_account_id'] = $account_id;
		$this->db->where('id', $analytic_id)
			->update('account_url_profiles', $data);
	}

	public function linkRankinityAccount($propDet, $aProfId) {
		$data = array();
		$opt = array();
		if ($propDet) {
			$eRanks = $data = $eData = array();
			$data['linked_rankinity_id'] = $propDet['rankinity_project_id'];
			$eVisibility = array();
			$opt = array();
			$opt['project_id'] = $data['linked_rankinity_id'];
			$engines = $this->rankinity->getProjectEngine($opt);
			if ($engines['meta']['total']) {
				foreach ($engines['items'] as $eKey => $eItem) {
					$eData[$eKey]['project_id'] = $data['linked_rankinity_id'];
					$eData[$eKey]['engine_id'] = $eItem['id'];
					$eData[$eKey]['engine_name'] = $eItem['name'];
					$eData[$eKey]['url_profile_id'] = $aProfId;
					$eData[$eKey]['engine_title'] = $eItem['title'];
					$eData[$eKey]['engine_device'] = $eItem['device'];
					$eData[$eKey]['engine_domain'] = $eItem['domain'];
					$eData[$eKey]['engine_service'] = $eItem['service'];
					$eData[$eKey]['engine_language'] = $eItem['language'];
					$eData[$eKey]['engine_location'] = $eItem['location'];
					$eVisibility[$eKey] = $this->getProjectVisibilityData($data['linked_rankinity_id'], $eItem['id'], $aProfId);
					$eRanks[$eKey] = $this->getProjectRankingData($data['linked_rankinity_id'], $eItem['id'], $aProfId);
				}
			}
			if ($data) {
				$where = array();
				$where['url_profile_id'] = $aProfId;
				$this->removeLinkedRankinity($where, $aProfId);
				$this->addRankinityProject($data, $aProfId);
				$this->addRankinityProjectEngine($eData);
				$this->addRankinityProjectEngineVisibility($eVisibility);
				foreach ($eRanks as $eKey => $rankData) {
					$this->addRankinityProjectEngineRank($rankData);
				}
			}
		}
		return true;
	}

	public function addRankinityProject($data, $prfId) {
		if ($data) {
			$this->db->where('id', $prfId)
				->update('account_url_profiles', $data);
		}
	}

	public function addRankinityProjectEngine($data) {
		if ($data) {
			$this->db->insert_batch('rankinity_projects_engines', $data);
		}
	}

	public function addRankinityProjectEngineRank($data) {
		if ($data) {
			$this->db->insert_batch('rankinity_projects_engine_rank', $data);
		}
	}

	public function addRankinityProjectEngineVisibility($data) {
		if ($data) {
			$this->db->insert_batch('rankinity_projects_engine_rank_visibility', $data);
		}
	}

	public function resetRankinityProject($projectId, $engineId) {
		$this->db->where('rankinity_project_id', $projectId)
			->delete('rankinity_projects');
		$this->db->where('engine_id', $engineId)
			->where('project_id', $projectId)
			->delete('rankinity_projects_engines');
		$this->db->where('search_engine_id', $engineId)
			->where('project_id', $projectId)
			->delete('rankinity_projects_engine_rank');
		$this->db->where('project_id', $projectId)
			->delete('rankinity_projects_engine_rank_visibility');
	}

	public function removeLinkedRankinity($whereArr, $profId) {
		$data = [];
		$data['linked_rankinity_id'] = '';
		$this->db->where('id', $profId)
			->update('account_url_profiles', $data);
		$this->db->where($whereArr)->delete('rankinity_projects_engines');
		$this->db->where($whereArr)->delete('rankinity_projects_engine_rank');
		$this->db->where($whereArr)->delete('rankinity_projects_engine_rank_visibility');
	}

	private function getProjectVisibilityData($projectId, $engineId, $aProfId) {
		$data = $opt = array();
		$opt['search_engine_id'] = $engineId;
		$opt['project_id'] = $projectId;
		$visibilities = $this->rankinity->getProjectEngineVisibilities($opt);
		if (isset($visibilities['items'][0])) {
			$visibility = $visibilities['items'][0];
			$data['url_profile_id'] = $aProfId;
			$data['visibility_id'] = $visibility['id'];
			$data['position'] = $visibility['position'];
			$data['project_id'] = $visibility['project_id'];
			$data['project_name'] = $visibility['project_name'];
			$data['search_engine_id'] = $visibility['search_engine_id'];
			$data['position_updated_at'] = $visibility['position_updated_at'];
			$data['position_boost'] = $visibility['position_boost'];
			$data['position_best'] = $visibility['position_best'];
			$data['position_lowest'] = $visibility['position_lowest'];
			$data['position_top3'] = $visibility['position_top3'];
			$data['position_top10'] = $visibility['position_top10'];
			$data['position_top100'] = $visibility['position_top100'];
			$data['position_overtop100'] = $visibility['position_overtop100'];
			$data['position_top4_10'] = $visibility['position_top4_10'];
			$data['position_top11_20'] = $visibility['position_top11_20'];
			$data['position_top21_50'] = $visibility['position_top21_50'];
			$data['position_top51_100'] = $visibility['position_top51_100'];
			$data['position_up'] = $visibility['position_up'];
			$data['position_down'] = $visibility['position_down'];
			$data['position_unchanged'] = $visibility['position_unchanged'];
			$data['position_history'] = json_encode($visibility['position_history']);
		}
		return $data;
	}

	private function getProjectRankingData($projectId, $engineId, $aProfId) {
		$data = array();
		$opt = array();
		$opt['page'] = '0';
		$opt['size'] = '100';
		$opt['sort[direction]'] = 'asc';
		$opt['sort[name]'] = 'keywordOrder';
		$opt['project_id'] = $projectId;
		$opt['search_engine_id'] = $engineId;
		$ranks = $this->rankinity->getProjectEngineRanks($opt);
		$this->buildRankInsertStack($data, $ranks, $projectId, $engineId, $aProfId);
		$hasMore = $ranks['meta']['total'] == count($ranks['items']) ? false : true;
		if ($hasMore) {
			$numPages = round(count($ranks['items']) / $ranks['meta']['total']);
			for ($i = 1; $i < $numPages; $i++) {
				$opt['page'] = 1;
				$ranks = $this->rankinity->getProjectEngineRanks($opt);
				$this->buildRankInsertStack($data, $ranks, $projectId, $engineId);
			}
		}
		return $data;
	}

	private function buildRankInsertStack(&$dataStack = [], $rstSet, $projectId, $engineId, $aProfId) {
		if ($rstSet['items']) {
			foreach ($rstSet['items'] as $item) {
				$dataStack[] = array(
					'url_profile_id' => $aProfId,
					'project_id' => $projectId,
					'search_engine_id' => $engineId,
					'keyword_id' => $item['id'],
					'keyword_name' => $item['keyword_name'],
					'keyword_weight' => $item['keyword_weight'],
					'group_ids' => json_encode($item['group_ids']),
					'groups' => json_encode($item['groups']),
					'position' => $item['position'],
					'position_boost' => $item['position_boost'],
					'position_best' => $item['position_best'],
					'position_lowest' => $item['position_lowest'],
					'position_history' => json_encode($item['position_history']),
				);
			}
		}
	}

	public function linkAdwordAccount($propDet, $aProfId) {
		$data = array();
		$opt = array();
		$opt['search'] = com_get_domain($propDet['rankinity_project_url']);
		$projects = $this->rankinity->getProjects($opt);
		if ($projects['meta']['total']) {
			$eRanks = $data = array();
			$eData = array();
			$eVisibility = array();
			foreach ($projects['items'] as $key => $item) {
				$data[$key]['analytic_profile_id'] = $aProfId;
				$data[$key]['rankinity_project_id'] = com_arrIndex($item, 'id');
				$data[$key]['rankinity_project_url'] = com_arrIndex($item, 'url');
				$data[$key]['rankinity_project_name'] = com_arrIndex($item, 'name');
				$data[$key]['rankinity_project_screenshot'] = com_arrIndex($item, 'screenshot');
				$opt = array();
				$opt['project_id'] = $data[$key]['rankinity_project_id'];
				$engines = $this->rankinity->getProjectEngine($opt);
				if ($engines['meta']['total']) {
					foreach ($engines['items'] as $eKey => $eItem) {
						$eData[$eKey]['project_id'] = $item['id'];
						$eData[$eKey]['engine_id'] = $eItem['id'];
						$eData[$eKey]['engine_name'] = $eItem['name'];
						$eData[$eKey]['analytic_profile_id'] = $aProfId;
						$eData[$eKey]['engine_title'] = $eItem['title'];
						$eData[$eKey]['engine_device'] = $eItem['device'];
						$eData[$eKey]['engine_domain'] = $eItem['domain'];
						$eData[$eKey]['engine_service'] = $eItem['service'];
						$eData[$eKey]['engine_language'] = $eItem['language'];
						$eData[$eKey]['engine_location'] = $eItem['location'];
						$eVisibility[$eKey] = $this->getProjectVisibilityData($data[$key]['rankinity_project_id'], $eItem['id']);
						$eRanks[$eKey] = $this->getProjectRankingData($data[$key]['rankinity_project_id'], $eItem['id']);
					}
				}
			}
			if ($data) {
				$where = array();
				$where['analytic_profile_id'] = $aProfId;
				$this->removeLinkedRankinity($where);
				$this->addRankinityProject($data);
				$this->addRankinityProjectEngine($eData);
				$this->addRankinityProjectEngineVisibility($eVisibility);
				foreach ($eRanks as $eKey => $rankData) {
					$this->addRankinityProjectEngineRank($rankData);
				}
			}
		}
		return $projects['meta']['total'];
	}

	public function updateGoogleAdwordsData($profId, $adword_prj) {
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
		$log_user_id = com_user_data('id');
		// $linked_adwords = $this->SocialModel->getAccountGoogleAdwordsLinkDet($log_user_id);
		$linked_adwords = $adword_prj;
		$linked_adword_acc_id = $linked_adwords['account_id'];
		$upWhere = $upData = array();
		$upData['linked_adwords_acc_id'] = $linked_adword_acc_id;
		$upWhere['id'] = $profId;
		$upWhere['account_id'] = $log_user_id;
		$this->db->where($upWhere)
			->update('account_url_profiles', $upData);
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
		$currMonthRef = date('Y-m', time());
		$lastMonthRef = date('Y-m', strtotime("-1 months"));
		foreach ($months_tstamps as $mtstamp) {
			$profDet = $this->getFetchedAccountDetail($profId);
			$adw_msm = array();
			$adw_mdt = array();
			$month_ref = date('Y-m', $mtstamp);
			$sday_month = date('Ym01', $mtstamp);
			$lday_month = date('Ymt', $mtstamp);
			$gaData = $this->SocialModel->fetchViewAdwordData($month_ref, $log_user_id,
				com_user_data('google_adwords_accid'), "", $profId);
			// $gaData = null;
			$params = array();
			$params['clientCustomerId'] = $linked_adword_acc_id;
			$params['prod'] = 'adwords';
			$params['profId'] = $profId;
			$params['access_token'] = $profDet['adword_access_token'];
			$params['refresh_token'] = $profDet['adword_refresh_token'];
			$params['log_user_id'] = com_user_data('id');
			if (!$gaData || in_array($month_ref, array($currMonthRef, $lastMonthRef))) {
				$downloadedReportPath = $this->adwords->getAdwordsData($params, $sday_month, $lday_month);
				$data = $this->csvreader->parse_file($downloadedReportPath);
				$lIndex = count($data);
				foreach ($data as $dIndex => $dDet) {
					if ($dIndex < ($lIndex - 1)) {
						// $fld_con
						$adw_mdt[$dIndex]['view_id'] = "";
						$adw_mdt[$dIndex]['month_ref'] = $month_ref;
						$adw_mdt[$dIndex]['url_profile_id'] = $profId;
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
						$adw_msm[$dIndex]['url_profile_id'] = $profId;
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
				$where['url_profile_id'] = $profId;
				$where['adword_acc_id'] = $linked_adword_acc_id;
				$this->SocialModel->updateUserGoogleAdwordsProfilePropertyViewGData($adw_msm, $where);
				$this->SocialModel->updateUserGoogleAdwordsProfilePropertyViewGDataDet($adw_mdt, $where);
			}
		}
	}

	public function getProfDetail($anlyProfId) {
		return $this->db->from('account_url_profiles')
			->where('id', $anlyProfId)
			->get()->row_array();
	}

	public function addProfile() {
		$data = [];
		$data['view_id'] = "";
		$data['profile_id'] = "";
		$data['property_id'] = "";
		$data['account_id'] = com_user_data('id');
		$data['account_url'] = $this->input->post('account_url');
		$data['linked_account_id'] = 0;
		$data['linked_google_page'] = "";
		$data['linked_google_page_id'] = "";
		$data['linked_adwords_acc_id'] = "";
		$data['linked_google_page_location'] = "";
		$this->db->insert('account_url_profiles', $data);

		$data = [];
		$data['profile_id'] = $this->db->insert_id();
		$data['trello_access_token'] = "";
		$data['rankinity_access_token'] = "";
		$data['anlytic_access_token'] = "";
		$data['anlytic_refresh_token'] = "";
		$data['anlytic_token_expiration_time'] = date("Y-m-d h:i:s", time());
		$data['adword_customer_id'] = "";
		$data['adword_access_token'] = "";
		$data['adword_refresh_token'] = "";
		$data['adword_token_expiration_time'] = date("Y-m-d h:i:s", time());
		$data['gmb_refresh_token'] = "";
		$data['gmb_access_token'] = "";
		$data['gmb_token_expiration_time'] = date("Y-m-d h:i:s", time());
		$this->db->insert('account_url_profiles_social_token', $data);
	}

	public function getProfiles() {
		return $this->db->select('account_url_profiles_social_token.*, account_url_profiles.*')
			->from('account_url_profiles')
			->join('account_url_profiles_social_token', 'account_url_profiles.id=account_url_profiles_social_token.profile_id', 'left')
			->where('account_url_profiles.account_id', com_user_data('id'))
			->get()->result_array();
	}

	public function getProfileDetail($prof_id) {
		return $this->db->select('account_url_profiles_social_token.*, account_url_profiles.*')
			->from('account_url_profiles')
			->join('account_url_profiles_social_token', 'account_url_profiles.id=account_url_profiles_social_token.profile_id', 'left')
			->where('account_url_profiles.id', $prof_id)
			->get()->row_array();
	}
}