<?php
defined('BASEPATH') OR exit('No direct script access allowed');

private class AccReport {

	public function __construct(){
		parent::__construct();
		$this->isLogin();
		$this->load->model( 'SocialModel' );
        $opt = array();
        $opt[ 'ADWORDS' ][ 'clientCustomerId' ] = '719-360-1563';
        $this->config_path = $this->resetAdwordConfig( $opt );
	}

    public static function runExample(AdWordsSession $session, $filePath){
        // Create selector.
        $selector = new Selector();
        $selector->setFields(
            [
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
            ]
        );
        // Use a predicate to filter out paused criteria (this is optional).
        // $selector->setPredicates(
        //     [
        //         new Predicate('Status', PredicateOperator::NOT_IN, ['PAUSED'])
        //     ]
        // );
        // Create report definition.
        $reportDefinition = new ReportDefinition();
        $reportDefinition->setSelector($selector);        
        $reportDefinition->setDateRangeType(
            ReportDefinitionDateRangeType::LAST_MONTH
        );
        $reportDefinition->setReportType(
            ReportDefinitionReportType::CRITERIA_PERFORMANCE_REPORT
        );
        $reportDefinition->setDownloadFormat(DownloadFormat::CSV);
        // Download report.
        $reportDownloader = new ReportDownloader($session);
        // Optional: If you need to adjust report settings just for this one
        // request, you can create and supply the settings override here. Otherwise,
        // default values from the configuration file (adsapi_php.ini) are used.
        $reportSettingsOverride = (new ReportSettingsBuilder())->includeZeroImpressions(false)->build();
        $reportDownloadResult = $reportDownloader->downloadReport(
            $reportDefinition,
            $reportSettingsOverride
        );
        $reportDownloadResult->saveToFile($filePath);
        // printf(
        //     "Report with name '%s' was downloaded to '%s'.\n",
        //     $reportDefinition->getReportName(),
        //     $filePath
        // );
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
        $file_path = APPPATH.'../uploads/reports/';
        $fname = 'criteria-report-'.time().'.csv';
        $filePath = $file_path.$fname;
        self::runExample($session, $filePath);
		header('HTTP/1.1 200 OK');
        header('Cache-Control: no-cache, must-revalidate');
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$fname");
        readfile($filePath);
        exit;
    }
}