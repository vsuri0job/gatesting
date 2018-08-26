<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_analyticproperty extends CI_Migration {

    public function up(){

    	if( !$this->db->table_exists( 'analytic_profile_properties' ) ){
    		$sql = "CREATE TABLE `analytic_profile_properties` (
                     `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                     `profile_id` int(11) NOT NULL,
                     `property_id` varchar(255) NOT NULL,
                     `property_name` text NOT NULL,
                     `property_website_url` text NOT NULL,
                     PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$this->db->query( $sql );
    	}
	}

    public function down(){
    }
}