<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_adword_tbl extends CI_Migration {

	public function up(){
		if( !$this->db->table_exists( 'analytic_profile_property_view_adword_data' ) ){
			$sql = "CREATE TABLE `analytic_profile_property_view_adword_data` (
					 `id` int(11) NOT NULL AUTO_INCREMENT,
					 `account_id` int(11) NOT NULL,
					 `view_id` varchar(255) NOT NULL,
					 `month_ref` varchar(255) NOT NULL,
					 `clicks` varchar(255) DEFAULT '0',
					 `impressions` varchar(255) DEFAULT '0',
					 `ctr` varchar(255) NOT NULL DEFAULT '0',
					 `avg_cpc` varchar(255) NOT NULL DEFAULT '0',
					 `cost` varchar(255) NOT NULL DEFAULT '0',
					 `conversion` varchar(255) NOT NULL DEFAULT '0',
					 `cost_per_conversion` varchar(255) NOT NULL DEFAULT '0',
					 `avg_position` varchar(255) NOT NULL DEFAULT '0',
					 `phone_calls` varchar(255) NOT NULL DEFAULT '0',
					 PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}