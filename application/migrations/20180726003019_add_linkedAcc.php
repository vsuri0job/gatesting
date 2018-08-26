<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_linkedAcc extends CI_Migration {

	public function up(){

		if( !$this->db->field_exists( 'linked_account_id', 'fetched_analytic_profiles' ) ){
			$sql = "ALTER TABLE `fetched_analytic_profiles`  ADD `linked_account_id` INT(11) DEFAULT 0  AFTER `account_id`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}