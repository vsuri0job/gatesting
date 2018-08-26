<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_trellokey extends CI_Migration {

    public function up(){
		$trello = $this->db->select( 'social_name' )
				->from( 'social_app_data' )
				->where( 'social_name', 'trello' )
				->get()->num_rows();
    	if( !$trello ){
			$data = array();
			$data[ 'social_name' ] = 'trello';
			$data[ 'client_id' ] = '464d5cf6358ff139d1b80d9bbe5ac862';
			$data[ 'client_secret' ] = 'fb3ff944bf99f0bee73ae19f241b19fb549963f6227dfae6d6898fe2ab07861d';
            $this->db->insert( 'social_app_data', $data );
    	}
	}

    public function down(){
    }
}