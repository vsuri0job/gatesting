<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Mod_add_flds extends CI_Migration {

	public function up(){
		if( !$this->db->field_exists( 'per_new_sessions', 'analytic_profile_property_view_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_data` CHANGE `medium` `per_new_sessions` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0';";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'page_views', 'analytic_profile_property_view_data' ) ){
		$sql = "ALTER TABLE `analytic_profile_property_view_data` CHANGE `source_medium` `page_views` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0';";
		$this->db->query( $sql );
		}

		if( $this->db->field_exists( 'landing_page_path', 'analytic_profile_property_view_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_data` DROP `landing_page_path`;";
			$this->db->query( $sql );
		}

		if( $this->db->field_exists( 'landing_page_path', 'analytic_profile_property_view_data_detail' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_data_detail` DROP `landing_page_path`;";
			$this->db->query( $sql );
		}
			
		if( !$this->db->field_exists('report_type', 'analytic_profile_property_view_data_detail') ){
			$sql = "ALTER TABLE `analytic_profile_property_view_data_detail` ADD `report_type` VARCHAR(255) NOT NULL AFTER `month_ref`;";
			$this->db->query( $sql );
		}

		$sql = 'TRUNCATE TABLE analytic_profile_property_view_data';
		$this->db->query( $sql );

		$sql = 'TRUNCATE TABLE analytic_profile_property_view_data_detail';
		$this->db->query( $sql );

		if( !$this->db->field_exists('new_users', 'analytic_profile_property_view_data') ){
			$sql = "ALTER TABLE `analytic_profile_property_view_data` ADD `new_users` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `users`;";
		}
	}

	public function down(){
	}
}