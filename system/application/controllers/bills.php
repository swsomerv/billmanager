<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bills extends CI_Controller
{	
	/*
	 * Pages
	 */
	
	public function index()
	{
		$this->load->model('Bill');
		$bills = $this->Bill->billList();
		
		$this_week = $this->Bill->sumBills(mktime(0, 0, 0, date('n'), date('j') - date('w')));
		$last_week = $this->Bill->sumBills(mktime(0, 0, 0, date('n'), (date('j') - date('w')) - 7), 
														mktime(0, 0, 0, date('n'), date('j') - date('w')));
		
		$this_month = $this->Bill->sumBills(mktime(0, 0, 0, date('n'), 0));
		$last_month = $this->Bill->sumBills(mktime(0, 0, 0, date('n') - 1, 0), 
														mktime(0, 0, 0, date('n'), 0));
		
		$this->load->view('bills/bills_all', compact('bills', 'this_week', 'last_week', 'this_month', 'last_month'));
	}
	
	public function add()
	{	
		$this->load->library('form_validation');
		$this->load->model('User');
		
		$people = $this->User->userList();
		
		$enough_items = true;
		
		if($this->input->post())
		{
			$this->form_validation->set_rules($this->validation);
			
			if ($this->form_validation->run())
			{
				$post = $this->input->post();
				
				if ($this->_item_count($post['items']) > 0)
				{
					$this->load->model('Bill');
					$this->load->model('Item');
					
					$date_array = explode('/', $post['bill_date']);
					$time = mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2]);
					
					$bill_name = $post['bill_name'];
					$bill_id = $this->Bill->create($post['bill_creator'], $bill_name, $time);
					
					foreach ($post['items'] as $item)
					{
						if (!empty($item['name']) && !empty($item['cost']))
						{
							$this->Item->create($bill_id, $item, $post['bill_discount']);
						}
					}
					
					unset($this->form_validation->_field_data);
					unset($_POST);
					
					$this->load->view('bills/bills_add_success', compact('bill_id', 'bill_name'));
					return;
				}
				else
				{
					$enough_items = false;
				}
			}
		}
		
		$this->load->view('bills/bills_add', compact('people', 'enough_items'));
	}

	public function view($id)
	{
		$this->load->model('Bill');
		if (!is_numeric($id) || !$this->Bill->exists($id))
		{
			$this->load->view('bills/bills_view_not_exist');
			return;
		}
		
		$error = false;
		$success = false;
		
		if($this->input->post())
		{
			$post = $this->input->post();
			
			if ($post['action'] == 'update')
			{
				if (count($post['items']) == $this->Bill->countItems($id))
				{
					$this->Bill->rebuildItemsUsersAssoc($id, $post['items']);
					$success = 'Items updated.';
				}
				else
				{
					$error = 'Unable to modify items, make sure each item belongs to at least one person.';
				}
			}
			elseif($post['action'] == 'add')
			{
				$this->load->model('Item');
				$this->Item->create($id, $post, 0);
				$success = 'Item added.';
			}
		}
		
		$info = array_shift($this->Bill->info($id));
		$items = $this->Bill->getItems($id);
		
		$this->load->model('User');
		$owed = $this->User->allOwesToUser($info['creator_id'], $id);
		$people = $this->User->userList();
		
		$this->load->view('bills/bills_view', compact('info', 'owed', 'people', 'items', 'error', 'success'));
	}
	
	/*
	 * Validation Functions
	 */
	public $validation = array(
		array(
			'field' => 'bill_name',
			'label' => 'Bill Name',
			'rules' => 'required|max_length(255)'
		),
		array(
			'field' => 'bill_date',
			'label' => 'Bill Date',
			'rules' => 'required|callback__date_check'
		),
		array(
					'field' => 'bill_discount',
					'label' => 'Bill Discount',
					'rules' => 'required|callback__bill_discount'
		),
		array(
			'field' => 'bill_creator',
			'label' => 'Bill Creator',
			'rules' => 'required|callback__creator_check'
		),
		array(
			'field' => 'items[]',
			'label' => 'Items',
			'rules' => 'required|callback__items_check'
		)
	);
	
	
	function _date_check($str)
	{
		// Must match the format 11/11/2011 (dd/mm/yyyy)
		if (!preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $str))
		{
			$this->form_validation->set_message('_date_check', 'Invalid date. Date must be in the format dd/mm/yyyy.');
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function _items_check($item)
	{	
		if (empty($item['name']) ^ empty($item['cost']))
		{
			$this->form_validation->set_message('_items_check', 'Give each item a cost and name or neither.');
			return false;
		}
		
		if (!(empty($item['name']) && empty($item['cost'])) && !is_numeric($item['cost']))
		{
			$this->form_validation->set_message('_items_check', 'One or more items has an invalid cost. Ensure all items have a valid monetary amount (e.g. 11.25).');
			return false;
		}
		
		if (!array_key_exists('user', $item))
		{
			$this->form_validation->set_message('_items_check', 'All items must be assigned to at least one person.');
			return false;
		}
		
		return true;
	}
	
	function _creator_check($id)
	{
		$this->load->model('User');
		if (!$this->User->exists($id))
		{
			$this->form_validation->set_message('_creator_check', 'Bill payer does not exist.');
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function _item_count($items)
	{
		$i = 0;
		
		foreach ($items as $item)
		{
			if (!empty($item['name']))
				$i++;
		}
		
		return $i;
	}
	
	function _bill_discount($discount)
	{
		if (!(is_numeric($discount) && $discount >= 0 && $discount <= 100))
		{
			$this->form_validation->set_message('_bill_discount', 'Enter a valid discount between 0 and 100 (inclusive).');
			return false;
		}
		
		return true;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */