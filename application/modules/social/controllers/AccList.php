<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Auth\OAuth2;
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201806\cm\OrderBy;
use Google\AdsApi\AdWords\v201806\cm\Paging;
use Google\AdsApi\AdWords\v201806\cm\Selector;
use Google\AdsApi\AdWords\v201806\cm\SortOrder;
use Google\AdsApi\AdWords\v201806\mcm\ManagedCustomer;
use Google\AdsApi\AdWords\v201806\mcm\ManagedCustomerService;
use Google\AdsApi\Common\OAuth2TokenBuilder;

 class AccList extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->isLogin();
		$this->load->model( 'SocialModel' );
        $this->config_path = $this->adwords->getAdwordConfig();        
	}

 	const PAGE_LIMIT = 500;
    public static function runExample(
        AdWordsServices $adWordsServices,
        AdWordsSession $session
    ) {
        $managedCustomerService = $adWordsServices->get(
            $session,
            ManagedCustomerService::class
        );
        // Create selector.
        $selector = new Selector();
        $selector->setFields(['CustomerId', 'Name']);
        $selector->setOrdering([new OrderBy('CustomerId', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));
        // Maps from customer IDs to accounts and links.
        $customerIdsToAccounts = [];
        $customerIdsToChildLinks = [];
        $customerIdsToParentLinks = [];
        $totalNumEntries = 0;
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
                $selector->getPaging()->getStartIndex() + self::PAGE_LIMIT
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
            self::printAccountHierarchy(
                $rootAccount,
                $customerIdsToAccounts,
                $customerIdsToChildLinks,
                null,
                0,
                $accounts
            );
        }
        return $accounts;
    }
    /**
     * Prints the specified account's hierarchy using recursion.
     *
     * @param ManagedCustomer $account the account to print
     * @param array $customerIdsToAccounts a map from customer IDs to accounts
     * @param array $customerIdsToChildLinks a map from customer IDs to child
     *     links
     * @param int|null $depth the current depth we are printing from in the
     *     account hierarchy; i.e., how far we've recursed
     */
    private static function printAccountHierarchy(
        $account,
        $customerIdsToAccounts,
        $customerIdsToChildLinks,
        $depth = null,
        $parentAccId = 0,
        &$accStack = array()
    ) {    	
        if ($depth === null) {
            self::printAccountHierarchy(
                $account,
                $customerIdsToAccounts,
                $customerIdsToChildLinks,
                0,
                $parentAccId,
                $accStack
            );
            return;
        }
        $customerId = $account->getCustomerId();
        $accStack[] = array(
        	'depth' => $depth,
        	'account_id' => $customerId,
        	'account_name' => $account->getName(),
        	'parent_account_id' => $parentAccId
        );
        if (array_key_exists($customerId, $customerIdsToChildLinks)) {
            foreach ($customerIdsToChildLinks[strval($customerId)] as $childLink) {
            	$parentAccId = $childLink->getManagerCustomerId();            	
                $childAccount = $customerIdsToAccounts[strval($childLink->getClientCustomerId())];
                self::printAccountHierarchy(
                    $childAccount,
                    $customerIdsToAccounts,
                    $customerIdsToChildLinks,
                    $depth + 1,
                    $parentAccId,
                    $accStack
                );
            }
        }
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
        $session = (new AdWordsSessionBuilder())->fromFile( $this->config_path[ 'file_path' ] )->withOAuth2Credential($oauth2)->build();
        $accounts = self::runExample( new AdWordsServices(), $session);
        com_e( $accounts );
    }
}