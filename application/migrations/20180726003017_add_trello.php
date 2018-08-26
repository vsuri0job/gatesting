<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_trello extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'trello_boards' ) ){
			$sql = "CREATE TABLE `trello_boards` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `account_id` INT NOT NULL , `board_id` VARCHAR(255) NOT NULL , `board_name` TEXT NOT NULL , `board_url` VARCHAR(255) NOT NULL , `board_closed` TINYINT(1) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
			$this->db->query( $sql );

			// $sql = "CREATE TABLE `trello_boards` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `account_id` INT NOT NULL , `board_id` VARCHAR(255) NOT NULL , `board_name` TEXT NOT NULL , `board_url` VARCHAR(255) NOT NULL , `board_closed` TINYINT(1) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
			// $this->db->query( $sql );
		}		
	}

	public function down(){
	}
}