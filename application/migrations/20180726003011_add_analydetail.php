<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_analydetail extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'analytic_profile_property_view_data_detail' ) ){
			$sql = "CREATE TABLE `analytic_profile_property_view_data_detail` (
					 `id` int(11) NOT NULL AUTO_INCREMENT,
					 `account_id` int(11) NOT NULL,
					 `view_id` varchar(255) NOT NULL,
					 `month_ref` varchar(255) NOT NULL,
					 `medium` text,
					 `source_medium` text,
					 `landing_page_path` text,
					 `sessions` varchar(255) NOT NULL,
					 `users` varchar(255) NOT NULL,
					 `new_users` VARCHAR(255) NOT NULL DEFAULT '0',
					 `per_new_sessions` VARCHAR(255) NOT NULL DEFAULT '0',
					 `page_view_per_sessions` varchar(255) NOT NULL,
					 `page_views` varchar(255) NOT NULL,
					 `avg_session_duration` varchar(255) NOT NULL,
					 `bounce_rate` varchar(255) NOT NULL,
					 `avg_page_download_time` varchar(255) NOT NULL,
					 `goal_conversion_rate` varchar(255) NOT NULL,
					 `goal_completion_all` varchar(255) NOT NULL,
					 PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}
