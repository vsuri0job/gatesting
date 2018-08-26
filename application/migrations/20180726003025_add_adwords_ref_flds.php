<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_adwords_ref_flds extends CI_Migration {

	public function up(){

		if( !$this->db->field_exists( 'adword_acc_id', 'analytic_profile_property_view_adword_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_adword_data` 
					ADD `adword_acc_id` VARCHAR(255) NOT NULL DEFAULT '' AFTER `account_id`;";
			$this->db->query( $sql );
		}

		if( !$this->db->table_exists( 'analytic_profile_property_view_adword_data_detail' ) ){
			$sql = "CREATE TABLE `analytic_profile_property_view_adword_data_detail` (
								 `id` int(11) NOT NULL AUTO_INCREMENT,
								 `account_id` int(11) NOT NULL,
								 `adword_acc_id` varchar(255) NOT NULL DEFAULT '',
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