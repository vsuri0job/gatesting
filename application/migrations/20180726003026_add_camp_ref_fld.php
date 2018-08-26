<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_camp_ref_fld extends CI_Migration {

	public function up(){

		if( !$this->db->field_exists( 'campaign_id', 'analytic_profile_property_view_adword_data_detail' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_adword_data_detail` ADD `campaign_id` 
					VARCHAR(255) NOT NULL DEFAULT '' AFTER `adword_acc_id`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'avg_cost', 'analytic_profile_property_view_adword_data_detail' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_adword_data_detail` ADD `avg_cost` VARCHAR(255) NOT NULL DEFAULT '' AFTER `avg_cpc`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'avg_cost', 'analytic_profile_property_view_adword_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_adword_data` ADD `avg_cost` VARCHAR(255) NOT NULL DEFAULT '' AFTER `avg_cpc`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'campaign_id', 'analytic_profile_property_view_adword_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_adword_data` ADD `campaign_id` 
					VARCHAR(255) NOT NULL DEFAULT '' AFTER `adword_acc_id`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}