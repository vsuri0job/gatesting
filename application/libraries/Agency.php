<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Agency {

    /**
     * CI Instance
     * @var CI_Controller 
     */
    private $_ci;    

    /**
     * Constructor.
     * @param array $config 
     */
    public function __construct($config = array()) {
        $this->_ci = & get_instance();        
    }

	public function getAgencyById( $id ){
		return $this->_ci->db->select( 'id, name, status' )
						->from( 'agencies' )
						->where( 'id', $id )
						->get()->row_array();
	}

	public function getAgenciesByArray( $ids = array() ){
		return $this->_ci->db->select( 'id, name, status' )
						->from( 'agencies' )
						->where_in( 'id', $ids )
						->get()->result_array();
	}

	public function getAgencyActiveAccountCount( $agency_id ){
		return $this->_ci->db->where_in( 'agency_id', $agency_id )
						->count_all_results( 'accounts' );
	}
}