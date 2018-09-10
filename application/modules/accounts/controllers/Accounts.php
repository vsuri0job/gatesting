<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('AccountModel');		
	}

	public function valid_url($url) {
		$pattern = "/^((ht|f)tp(s?)\:\/\/|~/|/)?([w]{2}([\w\-]+\.)+([\w]{2,5}))(:[\d]{1,5})?/";
		if (!preg_match($pattern, $url)) {
			return FALSE;
		}
		return TRUE;
	}

	public function index() {		
		$inner = array();
		$shell = array();
		$user_agencies = explode(',', com_user_data('agencies'));
		$inner['accounts'] = $this->AccountModel->getAgencyAccounts($user_agencies);
		$shell['page_title'] = 'Accounts List';
		$shell['content'] = $this->load->view('accounts/index', $inner, true);
		$shell['footer_js'] = $this->load->view('accounts/index_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function addLogo($account_id) {
		$inner = array();
		$shell = array();
		$user_agencies = explode(',', com_user_data('agencies'));
		$inner['account'] = $this->AccountModel->getAccountDetail($account_id, $user_agencies);
		if ($inner['account']) {
			if (isset($_FILES['report_logo'])) {
				$config['encrypt_name'] = TRUE;
				$config['upload_path'] = 'uploads/report_logo/';
				$config['allowed_types'] = 'gif|jpg|png';
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('report_logo')) {
					$inner['file_error'] = array('error' => $this->upload->display_errors());
				} else {
					$data = array('upload_data' => $this->upload->data());
					$extra['report_logo'] = $data['upload_data']['file_name'];
					$this->AccountModel->updateAccount($inner['account'], $extra);
				}
			}
			$shell['content'] = $this->load->view('accounts/detail', $inner, true);
			$shell['footer_js'] = $this->load->view('accounts/detail_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		}
	}

	public function analytics() {
		$inner = array();
		$shell = array();
		$accounts = $this->AccountModel->getProfiles();
		if (!$accounts) {
			redirect('report/ganalyticreport');
			exit;
		}
		com_e($accounts);
		$inner['accounts'] = $accounts;
		$shell['page_title'] = 'Accounts List';
		$shell['content'] = $this->load->view('accounts/analytics', $inner, true);
		$shell['footer_js'] = $this->load->view('accounts/analytics_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function list() {
		$inner = array();
		$shell = array();
		$emsg = $this->session->flashdata('emsg');
		if ($emsg) {
			$inner['emsg'] = $emsg;
		}
		$accounts = $this->AccountModel->getProfiles();
		$inner['accounts'] = $accounts;
		$shell['page_title'] = 'Accounts Url List';
		$shell['content'] = $this->load->view('accounts/profile_list', $inner, true);
		$shell['footer_js'] = $this->load->view('accounts/profile_list_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function getAccounts() {
		$out = $inner = array();
		$aProfileId = $this->input->get('aid');
		$aProfileDetail = $this->AccountModel->getFetchedAccountDetail($aProfileId);
		$inner['accounts'] = $this->AccountModel->getAccounts();
		$inner['analytic_profile'] = $aProfileDetail;
		$out['account_html'] = $this->load->view('account_list', $inner, true);
		echo json_encode($out);
		exit();
	}

	public function linkAccount() {
		$account_id = $this->input->post('account_id');
		$analytic_id = $this->input->post('analytic_id');
		$out = array();
		$out['success'] = 0;
		$analyticDetail = $this->AccountModel->getFetchedAccountDetail($analytic_id);
		if ($account_id && $analytic_id && $analyticDetail) {
			$out['success'] = 1;
			$this->AccountModel->linkAnalyticAccount($analytic_id, $account_id);
		}
		echo json_encode($out);
		exit;
	}

	public function updateAccountAdwords($profile_id) {
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profile_id);
		$adwordProj = $this->SocialModel->getAdwordProjDetail($fetchedProfile['linked_adwords_acc_id']);
		if ($fetchedProfile && $fetchedProfile['linked_adwords_acc_id'] && $adwordProj) {
			// rankinity_project_url
			// $anl_prof_id, $adword_prj_id
			$this->AccountModel->updateGoogleAdwordsData($fetchedProfile['id'], $adwordProj);
		}
		redirect('accounts/analytics');
		exit;
	}

	public function updateAccountAnalytic($profile_id) {
		$fetchedProfile = $this->AccountModel->getFetchedAccountDetail($profile_id);
		$adwordProj = $this->SocialModel->getAdwordProjDetail($fetchedProfile['linked_adwords_acc_id']);
		// if ($fetchedProfile && $fetchedProfile[ 'linked_adwords_acc_id' ] && $adwordProj) {
		// 	// rankinity_project_url
		// 	// $anl_prof_id, $adword_prj_id
		// 	$this->AccountModel->updateGoogleAdwordsData($fetchedProfile['id'], $adwordProj);
		// }
		// redirect( 'accounts/analytics' );
		exit;
	}

	public function addProfileUrl() {
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('account_url', 'Account Url', 'required|is_unique[account_url_profiles.account_url]');
		$this->form_validation->set_rules( 'close_rate', 'Close Rate', 'required' );
		$this->form_validation->set_rules( 'avg_sale_amount', 'Avg. Sale Amt.', 'required' );
		$this->form_validation->set_rules( 'ltv_amount', 'LTV Amt.', 'required' );
		$inner = array();
		$shell = array();
		if ($this->form_validation->run() == FALSE) {
			$val_errors = "";
			if ($this->form_validation->error_array()) {
				$val_errors = implode("\n", $this->form_validation->error_array());
			}
			$inner['validation_errors'] = $val_errors;
			$shell['page_title'] = 'Add Account Url';
			$shell['content'] = $this->load->view('accounts/add_prof', $inner, true);
			$shell['footer_js'] = $this->load->view('accounts/add_prof_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {			
			$this->AccountModel->addProfile();
			redirect('accounts/list');
			exit;
		}
	}
	
	public function editProfileUrl( $profId ) {
		$profDet = $this->AccountModel->getFetchedAccountDetail($profId);
		$profDetSetting = $this->AccountModel->getFetchedAccountDetailSetting($profId, com_user_data( 'id' ));		
		if( !$profDet ){
			redirect( 'dashboard' );
			exit;
		}
		$this->form_validation->set_error_delimiters('', '');		
		$this->form_validation->set_rules( 'close_rate', 'Close Rate', 'required' );
		$this->form_validation->set_rules( 'avg_sale_amount', 'Avg. Sale Amt.', 'required' );
		$this->form_validation->set_rules( 'ltv_amount', 'LTV Amt.', 'required' );
		$inner = array();
		$shell = array();
		if ($this->form_validation->run() == FALSE) {
			$val_errors = "";
			if ($this->form_validation->error_array()) {
				$val_errors = implode("\n", $this->form_validation->error_array());
			}			
			$inner['profDet'] = $profDet;
			$inner['profDetSetting'] = $profDetSetting;
			$inner['validation_errors'] = $val_errors;
			$shell['page_title'] = 'Edit Account url:- '.$profDet[ 'account_url' ];
			$shell['content'] = $this->load->view('accounts/edit_prof', $inner, true);
			$shell['footer_js'] = $this->load->view('accounts/edit_prof_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {			
			$data = array();
			$data[ 'close_rate' ] = $this->input->post( 'close_rate' );;
			$data[ 'ltv_amount' ] = $this->input->post( 'ltv_amount' );;
			$data[ 'avg_sale_amount' ] = $this->input->post( 'avg_sale_amount' );;
			$this->AccountModel->updateProfile( $profDet[ 'id' ], $data );

			$data = array();
			$data['profile_id'] = $profDet[ 'id' ];
			$data['account_id'] = com_user_data( 'id' );			
			$seo = $this->input->post( 'seo' );
			$ppc = $this->input->post( 'ppc' );
			$wm = $this->input->post( 'wm' );
			$data['seo'] = $seo ? json_encode( $seo ) : json_encode( array() );
			$data['ppc'] = $ppc? json_encode( $ppc ): json_encode( array() );
			$data['wm'] = $wm ? json_encode( $wm ): json_encode( array() );
			$where = array();
			$where['profile_id'] = $profDet[ 'id' ];
			$where['account_id'] = com_user_data( 'id' );
			$this->AccountModel->updateProfileSetting( $where, $data );
			redirect('accounts/list');
			exit;
		}
	}

	public function deleteProfileUrl( $profId ) {
		$profDet = $this->AccountModel->getFetchedAccountDetail($profId);
		if( !$profDet ){
			redirect( 'dashboard' );
			exit;
		}		
		$this->AccountModel->removeAccountDetail($profDet[ 'id' ]);
		redirect('accounts/list');
		exit;
	}
}