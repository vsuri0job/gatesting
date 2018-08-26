<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_propviews extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'analytic_profile_property_views' ) ){
			$sql = "CREATE TABLE `analytic_profile_property_views` (
					 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					 `property_id` varchar(255) NOT NULL,
					 `view_id` varchar(255) NOT NULL,
					 `view_name` text NOT NULL,
					 PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}