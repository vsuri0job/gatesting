<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testing extends MY_Controller {

	public function index() {
		com_e('Index');
	}

	public function api_test() {
		$api_key = 'AIzaSyCX4IqApFXkbFm22e5f0-xYMC5P1WffqM8';
		$client = new Google_Client();
		$client->setApplicationName("Client_Library_Examples");
		$client->setDeveloperKey($api_key);

		// $service = new Google_Service_Books($client);
		$service = new Google_Service_Analytics($client);
		// com_e($service->data_ga);
		com_e($service->data_ga->get('132440718',
			'2017-01-01', '2018-07-26', 'ga:sessions'));
		$optParams = array('filter' => 'free-ebooks');
		$results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);

		foreach ($results as $item) {
			echo $item['volumeInfo']['title'], "<br /> \n";
		}
	}

	public function glogin() {
		$client_id = '5094604723-p5r009kn58j9ucgehqe09djmktnr9ttr.apps.googleusercontent.com';
		$client_secret = '2EwY1gHPNnIXX5u-ODhcoiiW';
		$redirect_uri = base_url('testing/gloginverify');

		//Create Client Request to access Google API
		$client = new Google_Client();
		$client->setApplicationName("Analytics Report");
		$client->setAccessType("offline");
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->addScope("email");
		$client->addScope("profile");
		$client->addScope("https://www.googleapis.com/auth/analytics");

		//Send Client Request
		$objOAuthService = new Google_Service_Oauth2($client);

		$authUrl = $client->createAuthUrl();

		header('Location: ' . $authUrl);
	}

	public function gloginverify() {
		// Fill CLIENT ID, CLIENT SECRET ID, REDIRECT URI from Google Developer Console
		$client_id = '5094604723-p5r009kn58j9ucgehqe09djmktnr9ttr.apps.googleusercontent.com';
		$client_secret = '2EwY1gHPNnIXX5u-ODhcoiiW';
		$redirect_uri = base_url('testing/gloginverify');

		//Create Client Request to access Google API
		$client = new Google_Client();
		$client->setApplicationName("Analytics Report");
		$client->setAccessType("offline");
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->addScope("email");
		$client->addScope("profile");
		$client->addScope("https://www.googleapis.com/auth/analytics");
		//Send Client Request
		$service = new Google_Service_Oauth2($client);

		com_e($client->authenticate($_GET['code']), 1, 1);
		$_SESSION['access_token'] = $client->getAccessToken();

		// User information retrieval starts..............................

		$user = $service->userinfo->get(); //get user info
		com_e($_SESSION, 0);
		com_e($user, 0);

		$analytics = new Google_Service_Analytics($client);
		/*
			    $accounts = $analytics->management_accounts->listManagementAccounts();
			    com_e($accounts);
		*/
		// Get the first view (profile) id for the authorized user.
		$profile = $this->getFirstProfileId($analytics);

		// Get the results from the Core Reporting API and print the results.
		$results = $this->getResults($analytics, $profile);
		$this->printResults($results);
	}

	function getFirstProfileId($analytics) {
		// Get the user's first view (profile) ID.

		// Get the list of accounts for the authorized user.
		$accounts = $analytics->management_accounts->listManagementAccounts();

		if (count($accounts->getItems()) > 0) {
			$items = $accounts->getItems();
			$firstAccountId = $items[0]->getId();

			// Get the list of properties for the authorized user.
			$properties = $analytics->management_webproperties
				->listManagementWebproperties($firstAccountId);

			if (count($properties->getItems()) > 0) {
				$items = $properties->getItems();
				$firstPropertyId = $items[0]->getId();

				// Get the list of views (profiles) for the authorized user.
				$profiles = $analytics->management_profiles
					->listManagementProfiles($firstAccountId, $firstPropertyId);

				if (count($profiles->getItems()) > 0) {
					$items = $profiles->getItems();

					// Return the first view (profile) ID.
					return $items[0]->getId();

				} else {
					throw new Exception('No views (profiles) found for this user.');
				}
			} else {
				throw new Exception('No properties found for this user.');
			}
		} else {
			throw new Exception('No accounts found for this user.');
		}
	}

	function getResults($analytics, $profileId) {
		// Calls the Core Reporting API and queries for the number of sessions
		// for the last seven days.
		return $analytics->data_ga->get(
			'ga:' . $profileId,
			'7daysAgo',
			'today',
			'ga:sessions');
	}

	function printResults($results) {
		// Parses the response from the Core Reporting API and prints
		// the profile name and total sessions.
		if (count($results->getRows()) > 0) {

			// Get the profile name.
			$profileName = $results->getProfileInfo()->getProfileName();

			// Get the entry for the first entry in the first row.
			$rows = $results->getRows();
			$sessions = $rows[0][0];

			// Print the results.
			print "<p>First view (profile) found: $profileName</p>";
			print "<p>Total sessions: $sessions</p>";
		} else {
			print "<p>No results found.</p>";
		}
	}

	function pinfo() {
		echo phpinfo();
	}
}
