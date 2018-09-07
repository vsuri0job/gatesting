<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_sharelinks extends CI_Migration {

        public function up(){
        	$url_profiles = $this->db->from( 'account_url_profiles' )->get()->result_array();
        	foreach($url_profiles as $profile){
        		$profId = $profile[ 'id' ];
        		$data = array();        		
        		if( !$profile[ 'share_analytic_link' ] ){
        			$data['share_analytic_link'] = com_b64UrlEncode('analytic/'.$profId);
        		}
        		if( !$profile[ 'share_gsc_link' ] ){
        			$data['share_gsc_link'] = com_b64UrlEncode('gsc/'.$profId);
        		}
                        if( !$profile[ 'share_full_link' ] ){
                                $data['share_full_link'] = com_b64UrlEncode('full/'.$profId);
                        }
        		if( !$profile[ 'share_gmb_link' ] ){
        			$data['share_gmb_link'] = com_b64UrlEncode('gmb/'.$profId);
        		}
        		if( !$profile[ 'share_trello_link' ] ){
        			$data['share_trello_link'] = com_b64UrlEncode('trello/'.$profId);
        		}
        		if( !$profile[ 'share_adwords_link' ] ){
        			$data['share_adwords_link'] = com_b64UrlEncode('adword/'.$profId);
        		}
        		if( !$profile[ 'share_citation_link' ] ){
        			$data['share_citation_link'] = com_b64UrlEncode('citation/'.$profId);
        		}
        		if( !$profile[ 'share_rankinity_link' ] ){
        			$data['share_rankinity_link'] = com_b64UrlEncode('rankinity/'.$profId);
        		}
                        if( !$profile[ 'share_overview_link' ] ){
                                $data['share_overview_link'] = com_b64UrlEncode('overview/'.$profId);
                        }
                        
        		if( $data ){
        			$this->db->where( 'id', $profId )
        					->update( 'account_url_profiles', $data );
        		}
        	}
        }

        public function down(){
        }
}