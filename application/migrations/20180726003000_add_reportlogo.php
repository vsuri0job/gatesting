<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_reportlogo extends CI_Migration {

        public function up(){
        	if( !$this->db->field_exists( 'report_logo', 'users' ) ){
        		$sql = 'ALTER TABLE `users` ADD `report_logo` VARCHAR(300) NOT NULL DEFAULT "" AFTER `agencies`;';
        		$this->db->query( $sql );
        	}
        }

        public function down(){
        }
}