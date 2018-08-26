<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_trellotoken extends CI_Migration {

	public function up(){
		if( !$this->db->field_exists( 'trello_access_token', 'users' ) ){
			$sql = "ALTER TABLE `users` ADD `trello_access_token` TEXT NULL AFTER `report_logo`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}