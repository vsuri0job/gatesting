<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index(){		
		if( $this->isLogin ){
			redirect( 'dashboard' );
			exit;			
		}
		// $view = 'welcome_message';
		$inner = array();
		$shell = array();
		$shell[ 'content' ] = $this->load->view( 'welcome/login', $inner, true );
		$this->load->view( TMP_LOGIN, $shell );
	}

	public function login(){
		$username = $this->input->post( 'username' );
		$password = $this->input->post( 'password' );
		$user_detail = $this->UserModel->checkLogin($username, $password);
		if( $user_detail ){
			$this->session->set_userdata( $user_detail );
			$this->session->set_flashdata('success', 'You are successfully logged in');
			redirect( 'dashboard' );
			exit;
		}
		$this->session->set_flashdata('success', 'Please check your credentials!');
		redirect( '/' );
		exit;
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect( '/' );
		exit;
	}

	function pinfo(){
		echo phpinfo();
	}
}