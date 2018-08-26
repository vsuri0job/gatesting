<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rankinity extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'rankinity_projects' ) ){
			$sql = "CREATE TABLE `rankinity_projects` ( 
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
					`rankinity_project_id` VARCHAR(255) NULL DEFAULT '' , 
					`rankinity_project_name` VARCHAR(255) NOT NULL DEFAULT '' , 
					`rankinity_project_url` VARCHAR(255) NOT NULL DEFAULT '' , 
					`rankinity_project_screenshot` VARCHAR(255) NOT NULL DEFAULT '' , 
					`modified_at` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP , 
					PRIMARY KEY (`id`)) ENGINE = InnoDB;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'analytic_profile_id', 'fetched_analytic_profile_rankinity_project_engines' ) ){
			$sql = "ALTER TABLE `fetched_analytic_profile_rankinity_project_engines` 
					ADD `analytic_profile_id` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'analytic_profile_id', 'fetched_analytic_profile_rankinity_project_engine_ranks' ) ){
			$sql = "ALTER TABLE `fetched_analytic_profile_rankinity_project_engine_ranks` 
					ADD `analytic_profile_id` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'analytic_profile_id', 'fetched_analytic_profile_rankinity_project_engine_visibility' ) ){
			$sql = "ALTER TABLE `fetched_analytic_profile_rankinity_project_engine_visibility` 
					ADD `analytic_profile_id` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}