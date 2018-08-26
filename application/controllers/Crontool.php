<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crontool extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library( 'loaddata' );					
	}

    public function index($to = 'World')
    {
            echo "Hello {$to}!".PHP_EOL;
    }

    public function UpdateAccountSocialData(){    	
    	$linkedProfile = $this->db->from( 'fetched_analytic_profiles' )
    							->where( 'linked_account_id <> ', 0 )
    							->get()->result_array();
		foreach( $linkedProfile as $apDet ){
			$acc_id =  $apDet[ 'account_id' ];
			$prof_id = $apDet[ 'id' ];
			$anly_view_id =  $apDet[ 'view_id' ];
			$anly_prof_id =  $apDet[ 'profile_id' ];
			$anly_prop_id =  $apDet[ 'property_id' ];			
			$client = $this->loaddata->updateGoogleTokens(true);
			$client = $this->getGoogleClient();
			com_e( $client );
			$service = new Google_Service_Oauth2($client);
			$analytics = new Google_Service_Analytics($client);
			// $propDetail = $this->SocialModel->getPropertyDetail($anly_prop_id);
			// $linkedAccount = $this->ReportModel->searchPropertyDomainAccount($propDetail);
			// $linked_account_id = $linkedAccount['linkAccountId'];
			// $data = array();
			// $data['view_id'] = $viewId;
			// $data['profile_id'] = $profileId;
			// $data['property_id'] = $propertyId;
			// $data['account_id'] = com_user_data('id');
			// $data['linked_account_id'] = $linked_account_id;		
			// $aProfileId = $this->ReportModel->updateFetchedAccountDetail($data);
			// $rankFetch = $this->getRankinityProject($propDetail, $aProfileId);
		}
    }
}