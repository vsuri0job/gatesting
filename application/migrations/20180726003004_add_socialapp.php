<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_socialapp extends CI_Migration {

    public function up(){        

    	if( !$this->db->table_exists( 'social_app_data' ) ){
    		$sql = 'CREATE TABLE `social_app_data` 
    			( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,  
    			`social_name` VARCHAR(255) NOT NULL ,  
    			`client_id` VARCHAR(255) NOT NULL ,  
    			`client_secret` VARCHAR(255) NOT NULL ,  
    			PRIMARY KEY  (`id`)) ENGINE = InnoDB;';
			$this->db->query( $sql );

            $sql = "INSERT INTO `social_app_data` (`id`, `social_name`, `client_id`, `client_secret`) VALUES ('1', 'google', '5094604723-p5r009kn58j9ucgehqe09djmktnr9ttr.apps.googleusercontent.com', '2EwY1gHPNnIXX5u-ODhcoiiW');";
            $this->db->query( $sql );
    	}
	}

    public function down(){
    }
}