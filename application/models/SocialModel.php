<?php

class SocialModel extends CI_Model {

	public function getSocialAppData($social_name) {
		$this->db->select('`client_id`, `client_secret`')
			->from('social_app_data')
			->where('social_name', $social_name)
			->get()->row_array();
	}

	public function addUserGoogleAnalyticsProfiles($profiles) {
		if ($profiles) {
			$this->db->insert_batch('analytic_profiles', $profiles);
		}
	}

	public function addUserGoogleAnalyticsProfileProperties($properties) {
		if ($properties) {
			$this->db->insert_batch('analytic_profile_properties', $properties);
		}
	}

	public function addUserGoogleAnalyticsProfilePropertyView($views) {
		if ($views) {
			$this->db->insert_batch('analytic_profile_property_views', $views);
		}
	}

	public function addUserGoogleAnalyticsProfilePropertyAdwordAssoc($adword_ass) {
		if ($adword_ass) {
			$this->db->insert_batch('analytic_profile_property_adwords_associations', $adword_ass);
		}
	}

	public function addUserGoogleAnalyticsProfilePropertyViewGData($gData) {
		if ($gData) {
			$this->db->insert_batch('analytic_profile_property_view_data', $gData);
		}
	}

	public function addUserGoogleMasterSites($webSites, $resetWhere) {
		if( $resetWhere ){
			$this->db->where( $resetWhere  )
					->delete('google_webmaster_sites');
		}
		if ($webSites) {
			$this->db->insert_batch('google_webmaster_sites', $webSites);
		}
	}

	public function getPropertyDetail($propId) {
		return $this->db->from('analytic_profile_properties')
			->where('property_id', $propId)
			->get()->row_array();
	}

	public function resetAdwordsAccounts($where, $data) {
		$this->db->where($where)
			->delete('adword_account_list');
		if ($data) {
			$this->db->insert_batch('adword_account_list', $data);
		}
	}

	public function getAdwordsAccount($parent_id = 0) {
		return $this->db->select('itm.account_id, itm.account_name, par.account_name as `grp_key`')
			->from('adword_account_list as `itm`')
			->join('adword_account_list as `par`', 'itm.parent_account_id=par.account_id', 'left')
		// ->where('parent_account_id', $parent_id )
			->order_by('`grp_key`')
			->get()
			->result_array();
	}

	public function getAccountGoogleAdwordsLinkDet($profId) {
		$profiles = array();
		$profiles = $this->db->select(' adword_account_list.* ')
			->from('adword_account_list')
			->join('account_url_profiles', 'account_url_profiles.linked_adwords_acc_id=adword_account_list.account_id')
			->where('account_url_profiles.id', $profId)
			->get()->row_array();
		return $profiles;
	}

	public function getAdwordsLinkDet($adword_id) {
		$profile = $this->db->select(' adword_account_list.* ')
			->from('adword_account_list')
			->where('account_id', $profId)
			->get()->row_array();
		return $profile;
	}

	public function addUserGoogleAdwordsProfilePropertyViewGData($gData) {
		if ($gData) {
			$this->db->insert_batch('account_url_profile_adword_data', $gData);
		}
	}

	public function addUserGoogleAdwordsProfilePropertyViewGDataDet($gData) {
		if ($gData) {
			$this->db->insert_batch('account_url_profile_adword_data_detail', $gData);
		}
	}

	public function updateUserGoogleAdwordsProfilePropertyViewGData($gData, $where) {
		if ($where) {
			$rem = $this->db->where($where)
				->delete('account_url_profile_adword_data');
			if ($gData) {
				$this->addUserGoogleAdwordsProfilePropertyViewGData($gData);
			}
		}
	}

	public function updateUserGoogleAdwordsProfilePropertyViewGDataDet($gData, $where) {
		if ($where) {
			$rem = $this->db->where($where)
				->delete('account_url_profile_adword_data_detail');
			if ($gData) {
				$this->addUserGoogleAdwordsProfilePropertyViewGDataDet($gData);
			}
		}
	}

	public function fetchViewAdwordData($mon_ref, $acc_id, $adword_acc_id, $view_id, $profId) {
		return $this->db->from('account_url_profile_adword_data')
			->where('view_id', $view_id)
			->where('month_ref', $mon_ref)
			->where('account_id', $acc_id)
			->where('adword_acc_id', $adword_acc_id)
			->where('url_profile_id', $profId)
			->get()->row_array();
	}

	public function updateRankinityProjects($data, $dWhere) {
		if ($dWhere) {
			$this->db->where($dWhere)
				->delete('rankinity_projects');
		}
		$this->db->insert_batch('rankinity_projects', $data);
	}

	public function getRankinityProjects($profId) {
		return $this->db->from('rankinity_projects')
			->where('url_profile_id', $profId)
			->get()->result_array();
	}

	public function getRankinityProjDetail($rank_id, $profId) {
		return $this->db->from('rankinity_projects')
			->where('url_profile_id', $profId)
			->where('rankinity_project_id', $rank_id)
			->get()->row_array();
	}

	public function getAdwordList() {
		return $this->db->from('rankinity_projects')
			->where('rankinity_project_id', $rank_id)
			->get()->row_array();
	}

	public function getAdwordProjDetail($adword_id) {
		return $this->db->from('adword_account_list')
			->where('account_id', $adword_id)
			->get()->row_array();
	}

	public function getGbusinessDetail($profId) {
		return $this->db->select( 'account_page_location_name as `gpId`, 
				account_page_location_place, account_page_name' )
			->from('google_business_pages')
			->join('google_business_page_locations', 'account_page_name_id=account_page_name_ref', 'left')
			->where('google_business_pages.url_profile_id', $profId)
			->get()->result_array();
	}

	public function getGbusinessPageDetail($profId, $page_id) {
		return $this->db->from('google_business_page_locations')			
			->where('google_business_page_locations.url_profile_id', $profId)
			->where('google_business_page_locations.account_page_location_id', $page_id)
			->get()->row_array();
	}
}