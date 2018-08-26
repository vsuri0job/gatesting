<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
	}

	public function validate_email($str, $id) {
		$user_detail = $this->UserModel->checkUserEmail($str, $id);
		if ($user_detail) {
			$this->form_validation->set_message('validate_email', 'Email already occupied');
			return FALSE;
		}
		return TRUE;
	}

	public function validate_username($str, $id) {
		$user_detail = $this->UserModel->checkUserName($str, $id);
		if ($user_detail) {
			$this->form_validation->set_message('validate_username', 'Username already occupied');
			return FALSE;
		}
		return TRUE;
	}

	public function index() {
		$inner = array();
		$shell = array();
		$shell['page_title'] = 'Dashboard';
		$shell['content'] = $this->load->view('dashboard/index', $inner, true);
		$this->load->view(TMP_DASHBOARD, $shell);
	}

	public function seoReoprt($value = '') {
		# code...
		$inner = array();
		$shell = array();
		$shell['page_title'] = 'SEO';
		$shell['content'] = $this->load->view('dashboard/seo_report', $inner, true);
		$this->load->view(TMP_DASHBOARD, $shell);
	}

	public function profile() {
		$this->breadcrumb->addElement('Profile', 'dashboard/profile');
		$inner = array();
		$shell = array();
		$this->form_validation->set_rules('email', 'Email', 'required|callback_validate_email[' . com_user_data('id') . ']');
		$this->form_validation->set_rules('username', 'Username', 'required|callback_validate_username[' . com_user_data('id') . ']');
		if ($this->form_validation->run()) {
			$extra = array();
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
				}
			}
			$this->UserModel->updateUserProfile($extra);
			com_update_session();
		}
		$socialTabActive = false;
		if ($this->input->get('sac')) {
			// gadword
			$socialTabActive = $this->input->get('sac');
		}
		$inner['socialTabActive'] = $socialTabActive;
		$inner['linked_adword_account'] = "";
		$inner['adwords_accounts'] = "";
		$shell['page_title'] = 'Profile';
		$shell['content'] = $this->load->view('dashboard/profile', $inner, true);
		$shell['footer_js'] = $this->load->view('dashboard/profile_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function getPInfo() {
		echo phpinfo();
	}
}