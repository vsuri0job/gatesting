<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Auth\OAuth2;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\Query\v201806\ReportQueryBuilder;
use Google\AdsApi\AdWords\Reporting\v201806\DownloadFormat;
use Google\AdsApi\AdWords\Reporting\v201806\ReportDefinitionDateRangeType;
use Google\AdsApi\AdWords\Reporting\v201806\ReportDownloader;
use Google\AdsApi\AdWords\ReportSettingsBuilder;
use Google\AdsApi\AdWords\v201806\cm\ReportDefinitionReportType;
use Google\AdsApi\Common\OAuth2TokenBuilder;


private class CampReport extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->isLogin();
		$this->load->model( 'SocialModel' );
		$opt = array();
		$opt[ 'ADWORDS' ][ 'clientCustomerId' ] = '719-360-1563';
        $this->config_path = $this->resetAdwordConfig( $opt );
	}

public static function runExample(AdWordsSession $session, $reportFormat)
    {
        // Create report query to get the data for last 7 days.
        $query = (new ReportQueryBuilder())
            ->select([
                'CampaignId',
                'AdGroupId',
                'Id',
                'Criteria',
                'CriteriaType',
                'Impressions',
                'Clicks',
                'Cost'
            ])
            ->from(ReportDefinitionReportType::CRITERIA_PERFORMANCE_REPORT)
            ->where('Status')->in(['ENABLED', 'PAUSED'])
            ->duringDateRange(ReportDefinitionDateRangeType::LAST_7_DAYS)
            ->build();
        // Download report as a string.
        $reportDownloader = new ReportDownloader($session);
        // Optional: If you need to adjust report settings just for this one
        // request, you can create and supply the settings override here.
        // Otherwise, default values from the configuration
        // file (adsapi_php.ini) are used.
        $reportSettingsOverride = (new ReportSettingsBuilder())
            ->includeZeroImpressions(false)
            ->build();
        $reportDownloadResult = $reportDownloader->downloadReportWithAwql(
            sprintf('%s', $query),
            $reportFormat,
            $reportSettingsOverride
        );
        print "Report was downloaded and printed below:<br/>";
        print $reportDownloadResult->getAsString();
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
        self::runExample($session, DownloadFormat::CSV);
    }
}