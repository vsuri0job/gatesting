<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_adwordtokenflds extends CI_Migration {

	public function up(){
		if( !$this->db->field_exists( 'google_adword_access_token', 'users' ) ){
			$sql = "ALTER TABLE `users` ADD `google_adword_access_token` text NULL AFTER `google_token_expiration_time`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}