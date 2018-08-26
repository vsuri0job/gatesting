<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_adwordacc extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'adword_account_list' ) ){
			$sql = "CREATE TABLE `adword_account_list` 
					( 	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
						`account_id` VARCHAR(255) NOT NULL DEFAULT '' , 
						`account_name` VARCHAR(255) NOT NULL DEFAULT '' , 
						`parent_account_id` VARCHAR(255) NOT NULL DEFAULT '' , 
						`depth` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}