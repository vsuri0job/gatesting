<?php

class SocialappModel extends CI_Model {

	public function getSocialAppData( $social_name ){
		return $this->db->select( '`client_id`, `client_secret`' )
			->from( 'social_app_data' )
			->where( 'social_name', $social_name )
			->get()->row_array();
	}
	
	public function updateGoogleTokens($prodType, $access_token, $refresh_token, $profId, $reset = false){
		$profData = $this->db->from( 'account_url_profiles_social_token' )
						->where( 'profile_id', $profId )
						->get()->row_array();
		$data = $profData;
		$data[ 'profile_id' ] = $profId;		
		if( $prodType == 'analytic' && !$reset){
		 	$data[ 'anlytic_refresh_token' ] = $refresh_token;
		 	$data[ 'anlytic_access_token' ] = json_encode( $access_token );
		 	$data[ 'anlytic_token_expiration_time' ] = date( 'Y-m-d h:i:s', $access_token[ 'created' ] + $access_token[ 'expires_in' ] - 30 );
		} else if( $prodType == 'adwords' && !$reset){
		 	$data[ 'adword_refresh_token' ] = $refresh_token;
		 	$data[ 'adword_access_token' ] = json_encode( $access_token );
		 	$data[ 'adword_token_expiration_time' ] = date( 'Y-m-d h:i:s', $access_token[ 'created' ] + $access_token[ 'expires_in' ] - 30 );
		} else if( $prodType == 'mbusiness' && !$reset){
		 	$data[ 'gmb_refresh_token' ] = $refresh_token;
		 	$data[ 'gmb_access_token' ] = json_encode( $access_token );
		 	$data[ 'gmb_token_expiration_time' ] = date( 'Y-m-d h:i:s', $access_token[ 'created' ] + $access_token[ 'expires_in' ] - 30 );
		} else if( $reset ){
			$fldRef = $prodType == 'mbusiness' ? 'gmb'
				: ( $prodType == 'adwords' ? 'adword' : (  $prodType == 'analytic' ? 'analytic' : '' ) );
			if( $fldRef ){
			 	$data[ "$fldRef_refresh_token" ] = "";
			 	$data[ "$fldRef_access_token" ] = "";
			 	$data[ "$fldRef_token_expiration_time" ] = date( 'Y-m-d h:i:s', time() );
			}
		}
        if( !$profData ){
        	$this->db->insert( 'account_url_profiles_social_token', $data);
        } else {
	        $this->db->where( 'id', $data[ 'id' ] )
	                ->update( 'account_url_profiles_social_token', $data);
        }
	}

	public function updateTrelloAccessToken($access_token, $profId){
        if( $access_token ){			
			$userdata[ 'trello_access_token' ] = $access_token;
	        $this->db->where( 'profile_id', $profId)
	                ->update( 'account_url_profiles_social_token', $userdata);
        }		
	}

	public function updateRankinityAccessToken($access_token, $profId){
        if( $access_token ){
			$userdata[ 'rankinity_access_token' ] = $access_token;
	        $this->db->where( 'profile_id', $profId)
	                ->update( 'account_url_profiles_social_token', $userdata);
        }		
	}

	public function emptyAnalyticData( $accountId, $profId ){
		if( $accountId && $profId ){
			$this->db->where( 'account_id', $accountId )
					->where( 'url_profile_id', $profId )
					->delete( 'analytic_profiles' );
			$this->db->where( 'account_id', $accountId )
					->where( 'url_profile_id', $profId )
					->delete( 'analytic_profile_properties' );
			$this->db->where( 'account_id', $accountId )
					->where( 'url_profile_id', $profId )
					->delete( 'analytic_profile_property_views' );
			$this->db->where( 'account_id', $accountId )
					->where( 'url_profile_id', $profId )
					->delete( 'analytic_profile_property_adwords_associations' );
		}
	}

	public function getTrelloBoards(){
		return $this->db->from( 'trello_boards' )
					->where( 'account_id', com_user_data( 'id' ) )
					->get()->result_array();
	}

	public function updateTrelloBoards( $boards ){
		$this->db->where( 'account_id', com_user_data( 'id' ) )
				->delete( 'trello_boards' );
		$this->db->insert_batch( 'trello_boards', $boards );
	}

	public function updateGoogleAdwordRefreshToken( $refresh_token, $tokenUserId = 0 ){
        if( $refresh_token ){
			$account_id = $tokenUserId ? $tokenUserId : com_user_data( 'id' );
			$userdata[ 'google_adword_refresh_token' ] = $refresh_token;			
	        $this->db->where( 'id', $account_id )
	                ->update( 'users', $userdata);
        }
	}

	public function updateGoogleAdwordAccessToken( $access_token, $tokenUserId = 0 ){
        if( $access_token ){
			$account_id = $tokenUserId ? $tokenUserId : com_user_data( 'id' );
			$userdata[ 'google_adword_access_token' ] = json_encode( $access_token );
			$userdata[ 'google_adword_token_expiration_time' ] = date( 'Y-m-d h:i:s', $access_token[ 'created' ] + $access_token[ 'expires_in' ] - 30 );
	        $this->db->where( 'id', $account_id )
	                ->update( 'users', $userdata);
        }
	}

	public function resetBusinessProfData( $log_user_id, $profId ){
		$this->db->where( 'url_profile_id', $profId )
				->where( 'account_id', $log_user_id )
				->delete( 'google_business_pages' );

		$this->db->where( 'url_profile_id', $profId )
				->where( 'account_id', $log_user_id )
				->delete( 'google_business_page_locations' );
	}

	public function insertBusinessProfData( $businessPages, $pagesLocation ){
		$this->db->insert_batch( 'google_business_pages', $businessPages );
		$this->db->insert_batch( 'google_business_page_locations', $pagesLocation );
	}

	public function linkProfileAdword( $prfId, $custId ){
		$data = array();
		$data[ 'adword_customer_id' ] = $custId;
		$this->db->where( 'profile_id', $prfId )
			->update( 'account_url_profiles_social_token', $data);
	}

	public function getProfileDetail( $profId ){
		return $this->db->where( 'id', $profId )
					->from( 'account_url_profiles' )
					->get()->row_array();;
	}
}