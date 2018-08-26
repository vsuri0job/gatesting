<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gaflds extends CI_Migration {

	public function up(){

		if( !$this->db->field_exists( 'medium', 'analytic_profile_property_view_data' ) ){
			$sql = "ALTER TABLE `analytic_profile_property_view_data`  ADD `medium` TEXT NULL  AFTER `month_ref`,  ADD `source_medium` TEXT NULL  AFTER `medium`,  ADD `landing_page_path` TEXT NULL  AFTER `source_medium`;";
			$this->db->query( $sql );
		}
	}

	public function down(){
	}
}