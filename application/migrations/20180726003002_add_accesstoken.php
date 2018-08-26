<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_accesstoken extends CI_Migration {

    public function up(){

    	if( !$this->db->field_exists( 'google_access_token', 'users' ) ){
    		$sql = "ALTER TABLE `users`  ADD `google_access_token` VARCHAR(400) NOT NULL 
    				DEFAULT ''  AFTER `report_logo`;";
    		$this->db->query( $sql );
    	}
    }

    public function down(){
    }
}