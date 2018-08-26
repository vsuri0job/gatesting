<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_adwords_user_ref extends CI_Migration {

	public function up(){

		if( !$this->db->field_exists( 'google_adwords_accid', 'users' ) ){
			$sql = "ALTER TABLE `users` ADD `google_adwords_accid` VARCHAR(255) NOT NULL DEFAULT '' AFTER `google_token_expiration_time`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}