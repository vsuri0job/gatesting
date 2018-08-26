<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_accountlogo extends CI_Migration {

        public function up(){
        	
        	if( !$this->db->field_exists( 'last_active_services', 'accounts' ) ){
        		$sql = 'ALTER TABLE `accounts` ADD `last_active_services` VARCHAR(300) NOT NULL 
        		DEFAULT "" AFTER `added_by`;';
        		$this->db->query( $sql );        		
        	}

        	if( !$this->db->field_exists( 'report_logo', 'accounts' ) ){
        		$sql = 'ALTER TABLE `accounts` ADD `report_logo` VARCHAR(300) NOT NULL 
        		DEFAULT "" AFTER `last_active_services`;';
        		$this->db->query( $sql );
        	}
        }

        public function down(){
        }
}