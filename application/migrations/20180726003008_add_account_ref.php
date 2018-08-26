<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_account_ref extends CI_Migration {

	public function up(){

		if( !$this->db->field_exists( 'account_id', 'analytic_profile_properties' ) ){
			$sql = "ALTER TABLE `analytic_profile_properties` ADD `account_id` INT(11) NOT NULL AFTER `id`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'account_id', 'analytic_profile_property_views' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_views` ADD `account_id` INT(11) NOT NULL AFTER `id`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'account_id', 'analytic_profile_property_view_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_data` ADD `account_id` INT(11) NOT NULL AFTER `id`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}