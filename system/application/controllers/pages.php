<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller
{
	public function index()
	{
		// fetch overview data
		$this->load->model('User');
		$all_to_all = $this->User->allOwesToAll();
		$overview_amount = array();
		$overview_users = array();
		
		// initialize variables for each user first
		foreach ($all_to_all as $user_to_all)
		{
			$overview_users[] = $user_to_all['user'];
			$overview_amount[$user_to_all['user']['id']] = array();
			$overview_amount[$user_to_all['user']['id']]['pay_sum'] = 0;
			$overview_amount[$user_to_all['user']['id']]['receive_sum'] = 0;
		}
		
		foreach ($all_to_all as $user_to_all)
		{
			foreach($user_to_all['owes'] as $user_to_user)
			{
				$owes = $user_to_user['owes'];
				$payer = $user_to_all['user']['id'];
				$payee = $user_to_user['user']['id'];
				
				$overview_amount[$payer][$payee] = $owes;
				$overview_amount[$payer]['pay_sum'] += $owes;
				
				$overview_amount[$user_to_user['user']['id']]['receive_sum'] += $owes;
			}
		}
		
		// fetch sidebar data
		$this->load->model('Bill');
		$recent_bills = $this->Bill->billList(6);
		
		$this->load->view('pages/pages_overview', compact('overview_amount', 'overview_users', 'recent_bills'));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */