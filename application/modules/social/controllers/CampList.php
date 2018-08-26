<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Auth\OAuth2;
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201806\cm\CampaignService;
use Google\AdsApi\AdWords\v201806\cm\OrderBy;
use Google\AdsApi\AdWords\v201806\cm\Paging;
use Google\AdsApi\AdWords\v201806\cm\Selector;
use Google\AdsApi\AdWords\v201806\cm\SortOrder;
use Google\AdsApi\Common\OAuth2TokenBuilder;


private class CampList extends MY_Controller {

	const PAGE_LIMIT = 500;
	
	public function __construct(){
		parent::__construct();
		$this->isLogin();
		$this->load->model( 'SocialModel' );
        $opt = array();
        $opt[ 'ADWORDS' ][ 'clientCustomerId' ] = '719-360-1563';
        $this->config_path = $this->resetAdwordConfig();
	}

    public static function runExample(
        AdWordsServices $adWordsServices,
        AdWordsSession $session
    ) {
        $campaignService = $adWordsServices->get($session, CampaignService::class);
        // Create selector.
        $selector = new Selector();
        $selector->setFields(['Id', 'Name']);
        $selector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));
        $totalNumEntries = 0;
        do {
            // Make the get request.
            $page = $campaignService->get($selector);
            // Display results.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $campaign) {
                    printf(
                        "Campaign with ID %d and name '%s' was found.\n",
                        $campaign->getId(),
                        $campaign->getName()
                    );
                }
            }
            // Advance the paging index.
            $selector->getPaging()->setStartIndex(
                $selector->getPaging()->getStartIndex() + self::PAGE_LIMIT
            );
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);
        printf("Number of results found: %d\n", $totalNumEntries);
    }



    public function index()
    {
        // Generate a refreshable OAuth2 credential for authentication.
        // $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()->build();
        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
		// $init_homedrive = getenv('HOME');
		// putenv("HOME=". $this->config_path[ 'dir_path' ]);
		$this->updateGoogleTokens();
		$access_token =  com_user_data( 'google_access_token' );
		$refresh_token =  com_user_data( 'google_refresh_token' );
		$access_token = json_decode($access_token, true);		
		$oauth2 = new OAuth2([
		    'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
		    'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
		    'redirectUri' => base_url('social/verify_tmpgoogle'),
		    'clientId' => GOOGLE_CLIENT_ID,
		    'clientSecret' => GOOGLE_CLIENT_SECRET,
		    'scope' => 'https://www.googleapis.com/auth/adwords'
		]);
		$oauth2->setGrantType( 'refresh_token' );
		$oauth2->updateToken( $access_token );
        // $str = read_file( $this->config_path );
        // com_e( $this->config_path[ 'file_path' ] );
        $session = (new AdWordsSessionBuilder())->fromFile( $this->config_path[ 'file_path' ] )->withOAuth2Credential($oauth2)->build();
        self::runExample(new AdWordsServices(), $session);
    }
}