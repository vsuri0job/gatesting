<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rankinityprojflds extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'fetched_analytic_profile_rankinity_projects' ) ){
			$sql = "CREATE TABLE `fetched_analytic_profile_rankinity_projects` (
					  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `analytic_profile_id` int(10) unsigned NOT NULL,
					  `rankinity_project_id` varchar(100) NOT NULL DEFAULT '',
					  `rankinity_project_name` varchar(100) NOT NULL DEFAULT '',
					  `rankinity_project_url` varchar(100) NOT NULL DEFAULT '',
					  `rankinity_project_screenshot` text NULL,
					  `modified_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
					);";
			$this->db->query( $sql );

		}

		if( !$this->db->table_exists( 'fetched_analytic_profile_rankinity_project_engines' ) ){
			$sql = "CREATE TABLE `fetched_analytic_profile_rankinity_project_engines` (
					  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `project_id` varchar(200) NOT NULL DEFAULT '',
					  `engine_id` varchar(200) NOT NULL DEFAULT '',
					  `engine_name` varchar(200) NOT NULL DEFAULT '',
					  `engine_domain` varchar(200) NOT NULL DEFAULT '',
					  `engine_service` varchar(200) NOT NULL DEFAULT '',
					  `engine_device` varchar(200) NOT NULL DEFAULT '',
					  `engine_language` varchar(200) NOT NULL DEFAULT '',
					  `engine_location` varchar(200) NOT NULL DEFAULT '',
					  `engine_title` varchar(200) NOT NULL DEFAULT '',
					  `modified_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
					);";
			$this->db->query( $sql );
		}

		if( !$this->db->table_exists( 'fetched_analytic_profile_rankinity_project_engine_visibility' ) ){
			$sql = "CREATE TABLE `fetched_analytic_profile_rankinity_project_engine_visibility` (
					  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `visibility_id` varchar(200) NOT NULL,
					  `project_id` varchar(200) NOT NULL,
					  `project_name` varchar(200) NOT NULL,
					  `search_engine_id` varchar(200) NOT NULL,
					  `position` varchar(200) NOT NULL,
					  `position_updated_at` varchar(200) NOT NULL,
					  `position_boost` varchar(200) NOT NULL,
					  `position_best` varchar(200) NOT NULL,
					  `position_lowest` varchar(200) NOT NULL,
					  `position_top3` varchar(200) NOT NULL,
					  `position_top10` varchar(200) NOT NULL,
					  `position_top100` varchar(200) NOT NULL,
					  `position_overtop100` varchar(200) NOT NULL,
					  `position_top4_10` varchar(200) NOT NULL,
					  `position_top11_20` varchar(200) NOT NULL,
					  `position_top21_50` varchar(200) NOT NULL,
					  `position_top51_100` varchar(200) NOT NULL,
					  `position_up` varchar(200) NOT NULL,
					  `position_down` varchar(200) NOT NULL,
					  `position_unchanged` varchar(200) NOT NULL,
					  `position_history` text COLLATE 'latin1_swedish_ci' NULL,
					  `modified_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
					);";
			$this->db->query( $sql );
		}

		if( !$this->db->table_exists( 'fetched_analytic_profile_rankinity_project_engine_ranks' ) ){
			$sql = "CREATE TABLE `fetched_analytic_profile_rankinity_project_engine_ranks` (
					  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `project_id` varchar(200) NOT NULL,
					  `search_engine_id` varchar(200) NOT NULL,
					  `keyword_id` varchar(200) NOT NULL,
					  `keyword_name` varchar(200) NOT NULL,
					  `keyword_weight` varchar(200) NOT NULL,
					  `group_ids` text NULL,
					  `groups` text NULL,
					  `position` varchar(200) NOT NULL,
					  `position_boost` varchar(200) NOT NULL,
					  `position_best` varchar(200) NOT NULL,
					  `position_lowest` varchar(200) NOT NULL,
					  `position_history` text NULL,
					  `modified_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
					);";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}