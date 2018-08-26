<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_userflds extends CI_Migration {

	public function up(){
		
		$sql = "ALTER TABLE `users` CHANGE `google_access_token` `google_access_token` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;";
		$this->db->query( $sql );

		if( !$this->db->field_exists( 'modified_at', 'analytic_profile_property_view_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_data`  ADD `modified_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  AFTER `goal_completion_all`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'google_token_expiration_time', 'users' ) ){
			$sql = "ALTER TABLE `users` ADD `google_token_expiration_time` DATETIME NULL DEFAULT NULL AFTER `google_access_token`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'google_refresh_token', 'users' ) ){
			$sql = "ALTER TABLE `users` ADD `google_refresh_token` TEXT NULL AFTER `report_logo`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}