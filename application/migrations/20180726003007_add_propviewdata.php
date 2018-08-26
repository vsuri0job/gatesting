<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_propviewdata extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'analytic_profile_property_view_data' ) ){
			$sql = "CREATE TABLE `analytic_profile_property_view_data` (
					 `id` int(11) NOT NULL AUTO_INCREMENT,
					 `view_id` varchar(255) NOT NULL,
					 `month_ref` varchar(255) NOT NULL,
					 `sessions` varchar(255) NOT NULL,
					 `users` varchar(255) NOT NULL,
					 `page_view_per_sessions` varchar(255) NOT NULL,
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