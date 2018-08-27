<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_acc_social_token extends CI_Migration {

	public function up(){

		if( !$this->db->table_exists( 'account_url_profiles_social_token' ) ){
			$sql = "CREATE TABLE `account_url_profiles_social_token` (
				 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				 `profile_id` int(10) UNSIGNED NOT NULL,
				 `trello_access_token` text,
				 `analytic_refresh_token` text,
				 `analytic_access_token` text,
				 `analytic_token_expiration_time` datetime DEFAULT '0000-00-00 00:00:00', 
				 `adword_refresh_token` text,
				 `adword_access_token` text,
				 `adword_token_expiration_time` datetime DEFAULT '0000-00-00 00:00:00', 
				 `gmb_refresh_token` text,
				 `gmb_access_token` text,
				 `gmb_token_expiration_time` datetime DEFAULT '0000-00-00 00:00:00', 
				 `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
				 PRIMARY KEY (`id`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->query( $sql );
		}

		if( !$this->db->field_exists( 'url_profile_id', 'analytic_profiles' ) ){
			$sql = "ALTER TABLE `analytic_profiles` ADD `url_profile_id` 
					INT UNSIGNED NOT NULL DEFAULT '0' AFTER `account_id`;";
			$this->db->query( $sql );

			$sql = "ALTER TABLE `analytic_profile_properties` ADD `url_profile_id` 
					INT UNSIGNED NOT NULL DEFAULT '0' AFTER `account_id`;";
			$this->db->query( $sql );

			$sql = "ALTER TABLE `analytic_profile_property_adwords_associations` ADD `url_profile_id` 
					INT UNSIGNED NOT NULL DEFAULT '0' AFTER `account_id`;";
			$this->db->query( $sql );

			$sql = "ALTER TABLE `analytic_profile_property_views` ADD `url_profile_id` 
					INT UNSIGNED NOT NULL DEFAULT '0' AFTER `account_id`;";
			$this->db->query( $sql );

			$sql = "ALTER TABLE `analytic_profile_property_view_data` ADD `url_profile_id` 
					INT UNSIGNED NOT NULL DEFAULT '0' AFTER `account_id`;";
			$this->db->query( $sql );

			$sql = "ALTER TABLE `analytic_profile_property_view_data_detail` ADD `url_profile_id` 
					INT UNSIGNED NOT NULL DEFAULT '0' AFTER `account_id`;";
			$this->db->query( $sql );
		}

		$sql = "TRUNCATE TABLE `analytic_profiles`;";
		$this->db->query( $sql );

		$sql = "TRUNCATE TABLE `analytic_profile_properties`;";
		$this->db->query( $sql );

		$sql = "TRUNCATE TABLE `analytic_profile_property_adwords_associations`;";
		$this->db->query( $sql );

		$sql = "TRUNCATE TABLE `analytic_profile_property_views`;";
		$this->db->query( $sql );

		$sql = "TRUNCATE TABLE `analytic_profile_property_view_data`;";
		$this->db->query( $sql );

		$sql = "TRUNCATE TABLE `analytic_profile_property_view_data_detail`;";
		$this->db->query( $sql );

		$sql = "ALTER TABLE `analytic_profiles` ADD  FOREIGN KEY (`url_profile_id`) 
				REFERENCES `account_url_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$this->db->query( $sql );

		$sql = "ALTER TABLE `analytic_profile_properties` ADD  FOREIGN KEY (`url_profile_id`) 
				REFERENCES `account_url_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$this->db->query( $sql );

		$sql = "ALTER TABLE `analytic_profile_property_adwords_associations` ADD  FOREIGN KEY (`url_profile_id`) 
				REFERENCES `account_url_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$this->db->query( $sql );

		$sql = "ALTER TABLE `analytic_profile_property_views` ADD  FOREIGN KEY (`url_profile_id`) 
				REFERENCES `account_url_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$this->db->query( $sql );

		$sql = "ALTER TABLE `analytic_profile_property_view_data` ADD  FOREIGN KEY (`url_profile_id`) 
				REFERENCES `account_url_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$this->db->query( $sql );

		$sql = "ALTER TABLE `analytic_profile_property_view_data_detail` ADD  FOREIGN KEY (`url_profile_id`) 
				REFERENCES `account_url_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$this->db->query( $sql );

		$sql = "ALTER TABLE `account_url_profiles_social_token` ADD  FOREIGN KEY (`profile_id`) 
				REFERENCES `account_url_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$this->db->query( $sql );
	}

	public function down(){
	}
}