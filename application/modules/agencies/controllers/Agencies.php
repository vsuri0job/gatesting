<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agencies extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('AccountModel');
	}

	public function index() {
		$inner = array();
		$shell = array();		
		$inner['agencies'] = $this->AccountModel->getAgencies();
		$shell['page_title'] = 'Agencies List';
		$shell['content'] = $this->load->view('agency_list', $inner, true);
		$shell['footer_js'] = $this->load->view('agency_list_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);
	}

	public function add() {
		$this->form_validation->set_rules( 'agency_name', 'Name', 'required|alpha_numeric_spaces|is_unique[agencies.name]' );
		if( $this->form_validation->run() == false){
			$shell = $inner = array();
			$shell['page_title'] = 'Agency';
			$shell['content'] = $this->load->view('agency_add', $inner, true);
			$shell['footer_js'] = $this->load->view('agency_add_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {
			$agency_name = $this->input->post( 'agency_name' );
			$data = array();
			$data[ 'name' ] = $agency_name;
			$data[ 'added_by' ] = com_user_date('id');
			$this->AccountModel->addAgencies($data);
			redirect( 'agencies' );
			exit;
		}
	}

	public function list_user( $agency_id ) {
		$agency = $this->AccountModel->getAgencyData( $agency_id );
		if( !$agency ){
			redirect( 'agencies' );
			exit;
		}
		$agency_users = $this->AccountModel->getAgencyUsers( $agency[ 'id' ] );

		$shell = $inner = array();		
		$inner['agency'] = $agency;		
		$inner['agency_users'] = $agency_users;		
		$shell['page_title'] = 'Agency';
		$shell['content'] = $this->load->view('agency_ulist', $inner, true);
		$shell['footer_js'] = $this->load->view('agency_ulist_js', $inner, true);
		$this->load->view(TMP_DEFAULT, $shell);	
	}

	public function add_user($agency_id) {
		$agency = $this->AccountModel->getAgencyData( $agency_id );
		if( !$agency ){
			redirect( 'agencies' );
			exit;
		}
		$this->form_validation->set_rules( 'company_name', 'Name', 'trim|required|min_length[5]|max_length[100]');
		$this->form_validation->set_rules( 'email', 'Email', 'trim|required|valid_email|max_length[100]|is_unique[agency_users.email]');
		$this->form_validation->set_rules( 'password', 'Password', 'trim|required|max_length[100]');
		if( $this->form_validation->run() == false){
			$shell = $inner = array();
			$inner['agency'] = $agency;
			$shell['page_title'] = 'Agency';
			$shell['content'] = $this->load->view('agency_add_user', $inner, true);
			$shell['footer_js'] = $this->load->view('agency_add_user_js', $inner, true);
			$this->load->view(TMP_DEFAULT, $shell);
		} else {
			$email = $this->input->post( 'email' );
			$password = $this->input->post( 'password' );
			$company_name = $this->input->post( 'company_name' );
			$data = array();
			$data[ 'logo' ] = "";
			$data[ 'email' ] = $email;
			$data[ 'password' ] = $password;
			$data[ 'agency_id' ] = $agency[ 'id' ];
			$data[ 'company_name' ] = $company_name;			
			if (isset($_FILES['logo'])) {
				$config['encrypt_name'] = TRUE;
				$config['upload_path'] = 'uploads/report_logo/';				
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('logo')) {
					$inner['file_error'] = array('error' => $this->upload->display_errors());
					// return false;
				} else {
					$logoData = array('upload_data' => $this->upload->data());
					$extra['logo'] = $logoData['upload_data']['file_name'];					
				}
			}
			$this->AccountModel->addAgencyUser($data);
			redirect( 'agencies' );
			exit;
		}
	}
}