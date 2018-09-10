<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_sharelinks extends CI_Migration {

	public function up(){
		$urlProfiles = $this->db->from( 'account_url_profiles' )
				->where( 'share_adwords_link', '' )
				->or_where( 'share_adwords_link IS NULL' )
				->get()->result_array();
		foreach ($urlProfiles as $uProfile) {
			$profile_id = $uProfile[ 'id' ];
			$data = array();
			$data['share_gmb_link'] = com_b64UrlEncode('gmb/'.$profile_id);
			$data['share_trello_link'] = com_b64UrlEncode('trello/'.$profile_id);
			$data['share_adwords_link'] = com_b64UrlEncode('adword/'.$profile_id);
			$data['share_analytic_link'] = com_b64UrlEncode('analytic/'.$profile_id);
			$data['share_citation_link'] = com_b64UrlEncode('citation/'.$profile_id);
			$data['share_rankinity_link'] = com_b64UrlEncode('rankinity/'.$profile_id);
			$this->db->where( 'id', $profile_id)
					->update('account_url_profiles', $data);
		}
	}

	public function down(){
	}
}