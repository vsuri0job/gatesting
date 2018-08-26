<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_projecttable extends CI_Migration {

    public function up(){

    	if( !$this->db->table_exists( 'analytic_profiles' ) ){
	    	$sql = 'CREATE TABLE `analytic_profiles` (
					 `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
					 `account_id` int(11) NOT NULL,
					 `profile_id` int(11) NOT NULL,
					 `profile_name` varchar(255) NOT NULL,
					 PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1';
			$this->db->query( $sql );
    	}
    }

    public function down(){
    }
}