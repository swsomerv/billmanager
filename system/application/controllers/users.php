<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller
{
	public function index()
	{
		$this->load->view('users/users_all');
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */