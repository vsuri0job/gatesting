<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_sdata_ref extends CI_Migration {

        public function up(){
        	if( !$this->db->field_exists( 'log_acc_id', 'adword_account_list' ) ){
        		$sql = "ALTER TABLE `adword_account_list`  
        				ADD `log_acc_id` INT(11) UNSIGNED NOT NULL DEFAULT '0'  AFTER `id`,  
        				ADD `prof_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'  AFTER `log_acc_id`;";
        		$this->db->query( $sql );
        	}

        	if( !$this->db->field_exists( 'url_profile_id', 'google_business_pages' ) ){
        		$sql = "ALTER TABLE `google_business_pages` ADD `url_profile_id` INT(10) 
        			UNSIGNED NOT NULL DEFAULT '0' AFTER `account_id`;";
        		$this->db->query( $sql );
        	}

        	if( !$this->db->field_exists( 'url_profile_id', 'google_business_page_locations' ) ){
        		$sql = "ALTER TABLE `google_business_page_locations` ADD `url_profile_id` 
        			INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `account_id`;";
        		$this->db->query( $sql );
        	}
        }

        public function down(){
        }
}