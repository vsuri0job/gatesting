<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Auth\OAuth2;
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdWords\v201806\cm\OrderBy;
use Google\AdsApi\AdWords\v201806\cm\Paging;
use Google\AdsApi\AdWords\v201806\cm\Selector;
use Google\AdsApi\AdWords\v201806\cm\SortOrder;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\ReportSettingsBuilder;
use Google\AdsApi\AdWords\v201806\cm\Predicate;
use Google\AdsApi\AdWords\v201806\mcm\ManagedCustomer;
use Google\AdsApi\AdWords\v201806\mcm\CustomerService;
use Google\AdsApi\AdWords\v201806\cm\PredicateOperator;
use Google\AdsApi\AdWords\Reporting\v201806\DownloadFormat;
use Google\AdsApi\AdWords\Query\v201806\ReportQueryBuilder;
use Google\AdsApi\AdWords\Reporting\v201806\ReportDefinition;
use Google\AdsApi\AdWords\Reporting\v201806\ReportDownloader;
use Google\AdsApi\AdWords\v201806\mcm\ManagedCustomerService;
use Google\AdsApi\AdWords\v201806\cm\ReportDefinitionReportType;
use Google\AdsApi\AdWords\Reporting\v201806\ReportDefinitionDateRangeType;

class Adwords {

    /**
     * CI Instance
     * @var CI_Controller 
     */
    private $_ci;    

    private $acc_page_list = 500;

    private $client_customer_id;

    private $user_refresh_token;

    private $config_path;
    /**
     * Constructor.
     * @param array $config 
     */
    public function __construct($config = array()) {
        $this->_ci = & get_instance();
        $this->_ci->loaddata->loadConst();
	}

    public function getAdwordConfig(){
        $this->setAdwordConfig();
        return $this->config_path;
    }

	public function setAdwordConfig( $params = array() ){
        $opt = array();
        $opt[ 'file_ref' ] = $params[ 'prod' ].'_'.$params[ 'profId' ];
        $opt[ 'OAUTH2' ][ 'refreshToken' ] = $params[ 'refresh_token' ];
        $opt[ 'ADWORDS' ][ 'clientCustomerId' ] = GOOGLE_ADWORD_MANAGERACC;
        if( isset( $params[ 'clientCustomerId' ] ) ){
            $opt[ 'ADWORDS' ][ 'clientCustomerId' ] = $params[ 'clientCustomerId' ];
        }
        if( isset( $params[ 'customerIdMode' ] ) ){            
            $opt[ 'ADWORDS' ][ 'clientCustomerId' ] = $params[ 'customerIdMode' ];
        }
        $this->config_path = $this->resetAdwordConfig( $opt );
	}

	private function resetAdwordConfig( $opt = array() ){
		$config = array();
		$config[ 'SOAP' ] = array();
		$config[ 'LOGGING' ] = array();
		$config[ 'CONNECTION' ] = array();
		$config[ 'ADWORDS_REPORTING' ] = array();
		$config[ 'ADWORDS' ][ 'clientCustomerId' ] = GOOGLE_ADWORD_MANAGERACC;
		$config[ 'ADWORDS' ][ 'developerToken' ] = GOOGLE_ADWORD_DEV_TOKEN;
		$config[ 'OAUTH2' ][ 'clientId' ] = GOOGLE_CLIENT_ID;
		$config[ 'OAUTH2' ][ 'clientSecret' ] = GOOGLE_CLIENT_SECRET;
		$config[ 'OAUTH2' ][ 'refreshToken' ] = '';
		if( $opt ){
			foreach( $opt as $key => $optDet ){
                if( isset( $config[ $key ] ) ){
    				foreach( $optDet as $optK => $optV){
    					$config[ $key ][ $optK ] = $optV;
    				}
                }
			}
		}
		$data = "";
		foreach ($config as $key => $cDet) {
			$data .= '['.$key.']'.PHP_EOL;
			foreach( $cDet as $cKey => $cVal ){
				$data .= $cKey.' = "'.$cVal.'"'.PHP_EOL;
			}
		}
		$out = array();
		$out[ 'dir_path' ] = APPPATH.'custom_libraries/';
		$out[ 'file_path' ] = APPPATH.'custom_libraries/adsapi_php_'.$opt[ 'file_ref' ].'.ini';
		$adword_config = write_file( $out[ 'file_path' ], $data );
		if( !$adword_config ){
			echo 'Adwords config library is not writable';
			die();
		}
		return $out;
	}

    private function getAdwordSession( $opt = array() ){
		$adSess = $this->_ci->loaddata->updateGoogleTokens( true, $opt );
        $opt[ 'access_token' ] = $adSess[ 'access_token' ];
        $opt[ 'refresh_token' ] = $adSess[ 'refresh_token' ];
        $this->setAdwordConfig( $opt );
		$access_token =  $opt[ 'access_token' ];
		$refresh_token = $opt[ 'refresh_token' ];
		// $access_token = json_decode($access_token, true);
		$oauth2 = new OAuth2([
		    'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
		    'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
		    'redirectUri' => base_url('social/verify_google'),
		    'clientId' => GOOGLE_CLIENT_ID,
		    'clientSecret' => GOOGLE_CLIENT_SECRET,
		    'scope' => 'https://www.googleapis.com/auth/adwords'
		]);
		$oauth2->setGrantType( 'refresh_token' );
        if( is_string( $access_token ) ){
            $access_token = json_decode( $access_token, true );
        }
		$oauth2->updateToken( $access_token );
        return (new AdWordsSessionBuilder())->fromFile( $this->config_path[ 'file_path' ] )->withOAuth2Credential($oauth2)->build();
    }

    private function buildAccountHierarchy( $account, $customerIdsToAccounts,
        $customerIdsToChildLinks, $depth = null, $parentAccId = 0, &$accStack = array(), $log_user_id = 0, $profID = 0){
        if ($depth === null) {
            $this->buildAccountHierarchy(
                $account,
                $customerIdsToAccounts,
                $customerIdsToChildLinks,
                0,
                $parentAccId,
                $accStack,
                $log_user_id,
                $profID
            );
            return;
        }
        $customerId = $account->getCustomerId();
        $accStack[] = array(
        	'depth' => $depth,
            'prof_id' => $profID,
            'account_id' => $customerId,
            'log_acc_id' => $log_user_id,
        	'account_name' => $account->getName(),
        	'parent_account_id' => $parentAccId,
        );
        if (array_key_exists($customerId, $customerIdsToChildLinks)) {
            foreach ($customerIdsToChildLinks[strval($customerId)] as $childLink) {
            	$parentAccId = $childLink->getManagerCustomerId();            	
                $childAccount = $customerIdsToAccounts[strval($childLink->getClientCustomerId())];
                $this->buildAccountHierarchy(
                    $childAccount,
                    $customerIdsToAccounts,
                    $customerIdsToChildLinks,
                    $depth + 1,
                    $parentAccId,
                    $accStack,
                    $log_user_id,
                    $profID
                );
            }
        }
    }

    // public function getList(  $opt = array()  ){        
    //     $session = $this->getAdwordSession( $opt );
    //     $adWordsServices = new AdWordsServices();
    //     $customerService = $adWordsServices->get(
    //         $session, CustomerService::class
    //     );
    //     com_e( $customerService->getCustomers() );
    // }

    public function getList( $opt = array() ){
        $prof_id = $opt[ 'profId' ];
        $log_user_id = $opt[ 'log_user_id' ];        
        $session = $this->getAdwordSession( $opt );
        $adWordsServices = new AdWordsServices();
        $managedCustomerService = $adWordsServices->get(
            $session, ManagedCustomerService::class
        );
        // Create selector.
        $selector = new Selector();
        $selector->setFields(['CustomerId', 'Name']);
        $selector->setOrdering([new OrderBy('CustomerId', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, $this->acc_page_list ));
        // Maps from customer IDs to accounts and links.
        $customerIdsToAccounts = [];
        $customerIdsToChildLinks = [];
        $customerIdsToParentLinks = [];
        $totalNumEntries = 0;
        $exMsg = "";
        try{
            do {
                // Make the get request.
                $page = $managedCustomerService->get($selector);
                // Create links between manager and clients.
                if ($page->getEntries() !== null) {
                    $totalNumEntries = $page->getTotalNumEntries();
                    if ($page->getLinks() !== null) {
                        foreach ($page->getLinks() as $link) {
                            // Cast the indexes to string to avoid the issue when 32-bit PHP
                            // automatically changes the IDs that are larger than the 32-bit max
                            // integer value to negative numbers.
                            $managerCustomerId = strval($link->getManagerCustomerId());
                            $customerIdsToChildLinks[$managerCustomerId][] = $link;
                            $clientCustomerId = strval($link->getClientCustomerId());
                            $customerIdsToParentLinks[$clientCustomerId] = $link;
                        }
                    }
                    foreach ($page->getEntries() as $account) {
                        $customerIdsToAccounts[strval($account->getCustomerId())] = $account;
                    }
                }
                // Advance the paging index.
                $selector->getPaging()->setStartIndex(
                    $selector->getPaging()->getStartIndex() + $this->acc_page_list
                );
            } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);
            // Find the root account.
            $rootAccount = null;
            foreach ($customerIdsToAccounts as $account) {
                if (!array_key_exists(
                    $account->getCustomerId(),
                    $customerIdsToParentLinks
                )) {
                    $rootAccount = $account;
                    break;
                }
            }
            $accounts = array();
            if ($rootAccount !== null) {
                // Display results.
                $this->buildAccountHierarchy(
                    $rootAccount,
                    $customerIdsToAccounts,
                    $customerIdsToChildLinks,
                    null, 0, $accounts, $log_user_id, $prof_id 
                );
            }
            return $accounts;
        } catch(Exception $e){
            $exMsg = $e->getMessage();
            $exMsg = "Some internal error is coming";
            return $exMsg;
        }
    }

    public function getAdwordsData($opt = array(), $start_date, $end_date){        
        $session = $this->getAdwordSession( $opt );
        $adWordsServices = new AdWordsServices();
        $file_path = APPPATH.'../uploads/reports/';
        $fname = 'adwords-criteria-report-'.time().'.csv';
        $filePath = $file_path.$fname;
        $query = (new ReportQueryBuilder())
            ->select([
                'CampaignId',
                'Impressions',
                'Clicks',
                'Cost',
                'AverageCpc',
                'AverageCost',
                'Ctr',
                'Conversions',
                'CostPerConversion',
                'AveragePosition',
                'AllConversions'
            ])
            ->from(ReportDefinitionReportType::CRITERIA_PERFORMANCE_REPORT)
            // ->where('Status')->in(['ENABLED', 'PAUSED'])
            ->during($start_date, $end_date)
            // ->duringDateRange(ReportDefinitionDateRangeType::CUSTOM_DATE)
            ->build();
        // Create selector.
        // $selector = new Selector();
        // $selector->setFields(
        //     [
        //     ]
        // );
        // Use a predicate to filter out paused criteria (this is optional).
        // $selector->setPredicates(
        //     [
        //         new Predicate('Status', PredicateOperator::NOT_IN, ['PAUSED'])
        //     ]
        // );
        // Create report definition.
        // $reportDefinition = new ReportDefinition();
        // $reportDefinition->setSelector($selector);        
        // $reportDefinition->setDateRangeType(
        //     ReportDefinitionDateRangeType::CUSTOM_DATE
        // );
        // $reportDefinition->setStartDate('')
        // $reportDefinition->setReportType(
        //     ReportDefinitionReportType::CRITERIA_PERFORMANCE_REPORT
        // );
        // $reportDefinition->setDownloadFormat(DownloadFormat::CSV);
        // Download report.
        $reportDownloader = new ReportDownloader($session);
        // Optional: If you need to adjust report settings just for this one
        // request, you can create and supply the settings override here. Otherwise,
        // default values from the configuration file (adsapi_php.ini) are used.
        $reportSettingsOverride = (new ReportSettingsBuilder())
        ->skipReportHeader(true)
        ->includeZeroImpressions(false)->build();
        // $reportDownloadResult = $reportDownloader->downloadReport(
        //     $reportDefinition,
        //     $reportSettingsOverride
        // );
        // die(sprintf('%s', $query));
        $reportDownloadResult = $reportDownloader->downloadReportWithAwql(
            sprintf('%s', $query),
            DownloadFormat::CSV,
            $reportSettingsOverride
        );

        $reportDownloadResult->saveToFile($filePath);
        return $filePath;
    }
}