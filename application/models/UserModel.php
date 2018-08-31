<?php

class UserModel extends CI_Model {

	public function checkLogin($username, $password) {
		$rst = $this->db
			->select('id, username, email, password, role_id, user_image, status, agencies')
			->from('users')
			->where('status', 1)
			->where('username', $username)
			->where('password', $password)
			->get()->row_array();
		return $rst;
	}

	public function getUserDetail($user_id) {
		$rst = $this->db
			->select('id, username, email, password, role_id, user_image, status, agencies')
			->from('users')
			->where('id', $user_id)
			->get()->row_array();
		return $rst;
	}

	public function checkUserEmail($email, $exceptId) {
		$rst = $this->db->where('email', $email)
			->where('id <> ', $exceptId)
			->count_all_results('users');
		return $rst;
	}

	public function checkUserName($uname, $exceptId) {
		$rst = $this->db->where('username', $uname)
			->where('id <> ', $exceptId)
			->count_all_results('users');
		return $rst;
	}

	public function updateUserProfile($extra = array()) {
		$userdata = array();
		$userdata['username'] = $this->input->post('username');
		$userdata['email'] = $this->input->post('email');
		$password = $this->input->post('password');
		if ($password) {
			$userdata['password'] = $password;
		}
		if ($extra) {
			$userdata = array_merge($userdata, $extra);
		}
		$this->db->where('id', com_user_data('id'))
			->update('users', $userdata);
	}

	public function getAdwordsAcc() {
		return $this->db->from('adword_account_list')
			->where('account_id', com_user_data('google_adwords_accid'))
			->get()->row_array();
	}
}