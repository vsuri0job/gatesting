<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_analyticfetchtbl extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'fetched_analytic_profiles' ) ){
			$sql = "CREATE TABLE `fetched_analytic_profiles` ( 
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, 
				`account_id` INT NOT NULL , 
				`profile_id` VARCHAR(50) NOT NULL , 
				`property_id` VARCHAR(50) NOT NULL , 
				`view_id` VARCHAR(50) NOT NULL , 
				`modified_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
				PRIMARY KEY (`id`)) ENGINE = InnoDB;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}