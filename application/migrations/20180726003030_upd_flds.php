<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_upd_flds extends CI_Migration {

	public function up(){
		
		if( $this->db->table_exists( 'fetched_analytic_profiles' ) ){
			$sql = "ALTER TABLE `fetched_analytic_profiles` CHANGE `modified_at` `modified_at` 
			TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
			$this->db->query( $sql );
		}
		if( !$this->db->field_exists( 'fetched_analytic_prof_id', 'analytic_profile_property_view_adword_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_adword_data` ADD `fetched_analytic_prof_id` INT UNSIGNED NOT NULL DEFAULT 
			'0' AFTER `account_id`;";
			$this->db->query( $sql );
			$sql = "ALTER TABLE `analytic_profile_property_view_adword_data_detail` ADD `fetched_analytic_prof_id` INT UNSIGNED NOT NULL DEFAULT 
			'0' AFTER `account_id`;";
			$this->db->query( $sql );
		}

		if( $this->db->table_exists( 'fetched_analytic_profiles' ) 
			&& !$this->db->field_exists( 'account_url', 'fetched_analytic_profiles' ) ){
			$sql = "ALTER TABLE `fetched_analytic_profiles` ADD `account_url` TEXT NOT NULL AFTER `account_id`;";
			$this->db->query( $sql );
		}
	
		if( $this->db->table_exists( 'fetched_analytic_profile_rankinity_projects' ) ){
			$sql = "TRUNCATE TABLE `fetched_analytic_profile_rankinity_projects`;";
			$this->db->query( $sql );

			$sql = "ALTER TABLE `fetched_analytic_profile_rankinity_projects` 
					ADD FOREIGN KEY (`analytic_profile_id`) REFERENCES `fetched_analytic_profiles`(`id`) 
					ON DELETE CASCADE ON UPDATE CASCADE;";
			$this->db->query( $sql );			
		}

		if( $this->db->table_exists( 'fetched_analytic_profiles' )  &&
			$this->db->table_exists( 'fetched_analytic_profile_rankinity_project_engines' ) ){
			$sql = "TRUNCATE table `fetched_analytic_profile_rankinity_project_engines`;";
			$this->db->query( $sql );
			$sql = "ALTER TABLE `fetched_analytic_profile_rankinity_project_engines` 
					ADD FOREIGN KEY (`analytic_profile_id`) REFERENCES `fetched_analytic_profiles`(`id`) 
					ON DELETE CASCADE ON UPDATE CASCADE;";
			$this->db->query( $sql );
		}

		if( $this->db->table_exists( 'fetched_analytic_profiles' )  &&
			$this->db->table_exists( 'fetched_analytic_profile_rankinity_project_engine_ranks' ) ){
			$sql = "TRUNCATE table `fetched_analytic_profile_rankinity_project_engine_ranks`;";
			$this->db->query( $sql );
			$sql = "ALTER TABLE `fetched_analytic_profile_rankinity_project_engine_ranks` ADD 
				FOREIGN KEY (`analytic_profile_id`) REFERENCES `fetched_analytic_profiles`(`id`) 
				ON DELETE CASCADE ON UPDATE CASCADE;";
			$this->db->query( $sql );
		}

		if( $this->db->table_exists( 'fetched_analytic_profiles' )  &&
			$this->db->table_exists( 'fetched_analytic_profile_rankinity_project_engine_visibility' ) ){
			$sql = "TRUNCATE table `fetched_analytic_profile_rankinity_project_engine_visibility`;";
			$this->db->query( $sql );

			$sql = "ALTER TABLE `fetched_analytic_profile_rankinity_project_engine_visibility` ADD 
				FOREIGN KEY (`analytic_profile_id`) REFERENCES `fetched_analytic_profiles`(`id`) 
				ON DELETE CASCADE ON UPDATE CASCADE;";
			$this->db->query( $sql );
		}

		if( $this->db->table_exists( 'fetched_analytic_profiles' ) ){
			$sql = "RENAME TABLE `fetched_analytic_profiles` TO `account_url_profiles`;";
			$this->db->query( $sql );
		}

		if( $this->db->table_exists( 'fetched_analytic_profile_rankinity_projects' ) ){
			$sql = "RENAME TABLE `fetched_analytic_profile_rankinity_projects` TO `url_profile_rankinity_projects`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}