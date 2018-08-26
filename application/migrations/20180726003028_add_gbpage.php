<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gbpage extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'google_business_pages' ) ){
			$sql = "CREATE TABLE `google_business_pages` 
				( 	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
					`account_id` INT UNSIGNED NOT NULL DEFAULT '0' , 
					`account_page_name` VARCHAR(500) NOT NULL DEFAULT '' , 
					`account_page_name_url` VARCHAR(500) NOT NULL DEFAULT '' , 
					`account_page_name_id` VARCHAR(500) NOT NULL DEFAULT '' , 
					PRIMARY KEY (`id`)) ENGINE = InnoDB;";
			$this->db->query( $sql );
		}

		if( !$this->db->table_exists( 'google_business_page_locations' ) ){
			$sql = "CREATE TABLE `google_business_page_locations` 
				( 	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
					`account_id` INT UNSIGNED NOT NULL DEFAULT '0' , 
					`account_page_name_ref` VARCHAR(500) NOT NULL DEFAULT '' , 					
					`account_page_location_id` VARCHAR(500) NOT NULL DEFAULT '' , 
					`account_page_location_place` VARCHAR(500) NOT NULL DEFAULT '' , 
					PRIMARY KEY (`id`)) ENGINE = InnoDB;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}