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
			$this->AccountModel->addAgencies($data);
			redirect( 'agencies' );
			exit;
		}
	}

	public function list_user() {
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
			$this->AccountModel->addAgencies($data);
			redirect( 'agencies' );
			exit;
		}
	}

	public function add_user() {
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
			$this->AccountModel->addAgencies($data);
			redirect( 'agencies' );
			exit;
		}
	}
}