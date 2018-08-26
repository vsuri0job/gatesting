<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_adwordassoc extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'analytic_profile_property_adwords_associations' ) ){
			$sql = "CREATE TABLE `analytic_profile_property_adwords_associations` (
					 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					 `account_id` int(11) NOT NULL,
					 `property_id` varchar(255) NOT NULL DEFAULT '',
					 `adword_link_id` varchar(255) NOT NULL DEFAULT '',
					 `adword_link_name` varchar(255) NOT NULL DEFAULT '',
					 `profile_ids` text,
					 `adword_refs` text,
					 `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					 PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}