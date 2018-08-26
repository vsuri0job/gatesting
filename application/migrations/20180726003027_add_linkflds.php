<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_linkflds extends CI_Migration {

	public function up(){

		if( !$this->db->field_exists( 'linked_adwords_acc_id', 'fetched_analytic_profiles' ) ){
			$sql = "ALTER TABLE `fetched_analytic_profiles` ADD `linked_adwords_acc_id` VARCHAR(255) NOT NULL DEFAULT '' AFTER `account_id`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'linked_google_page', 'fetched_analytic_profiles' ) ){
			$sql = "ALTER TABLE `fetched_analytic_profiles` ADD `linked_google_page` VARCHAR(500) NOT NULL DEFAULT '' AFTER `linked_adwords_acc_id`;";
			$this->db->query( $sql );

			$sql = "ALTER TABLE `fetched_analytic_profiles`  ADD `linked_google_page_location` VARCHAR(500) NOT NULL DEFAULT ''  AFTER `linked_google_page`,  ADD `linked_google_page_id` VARCHAR(500) NOT NULL DEFAULT ''  AFTER `linked_google_page_location`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}