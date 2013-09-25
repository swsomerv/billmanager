<?php
class User extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	* Calculates how much everyone owes everyone else
	* @param unknown_type $user_id User ID of the payer
	* @return Array containing the amount owed and user info e.g. array(0 => array('user' => array('id' => ...), 'owes' => array('0' => array('user' => array('id' => 1, ...), 'owes' => 42)), ...))
	*/
	function allOwesToAll()
	{
		$this->db->order_by("id", "asc");
		$users = $this->db->get('users');
		$owes_array = array();
		$i = 0;
		
		foreach ($users->result_array() as $user)
		{
			$owes_array[$i]['owes'] = $this->userOwesToAll($user['id']);
			$owes_array[$i]['user'] = $user;
			
			$i++;
		}
		
		return $owes_array;
	}
	
	/**
	* Calculates how each users owes to $user_id
	* @param unknown_type $user_id User ID of the payer
	* @param $bill_id Optionally, filter by a bill id
	* @return Array containing the amount owed and user info e.g. array(0 => array('user' => array('id' => 1, ...), 'owes' => 42), ...)
	*/
	function allOwesToUser($user_id, $bill_id = -1)
	{
		$this->db->order_by("id", "asc");
		$this->db->where('users.id !=', $user_id);
		$users = $this->db->get('users');
		$owes_array = array();
		$i = 0;
	
		foreach ($users->result_array() as $user)
		{
			$owes_array[$i] = array();
			
			$owes_array[$i]['owes'] = $this->userOwesToUserRaw($user['id'], $user_id, $bill_id);
			$owes_array[$i]['user'] = $user;
				
			$i++;
		}
	
		return $owes_array;
	}
	
	/**
	 * Calculates how much $user_id owes to each user
	 * @param unknown_type $user_id User ID of the payer
	 * @return Array containing the amount owed and user info e.g. array(0 => array('user' => array('id' => 1, ...), 'owes' => 42), ...)
	 */
	function userOwesToAll($user_id)
	{
		$this->db->order_by("id", "asc");
		$users = $this->db->get('users');
		$owes_array = array();
		$i = 0;
		
		foreach ($users->result_array() as $user)
		{
			$owes_array[$i] = array();
			
			$owes_array[$i]['owes'] = $this->userOwesToUser($user_id, $user['id']);
			$owes_array[$i]['user'] = $user;
			
			$i++;
		}
		
		return $owes_array;
	}
	
	/**
	 * Calculates how much $user_id owes to $owes_id by offsetting what $owes_id owes to $user_id
	 * @param unknown_type $user_id User ID of the payer
	 * @param unknown_type $owes_id User ID of the payee
	 * @return Total owed
	 */
	function userOwesToUser($user_id, $owes_id)
	{		
		if ($user_id == $owes_id)
		{
			return 0;
		}
		
		return $this->userOwesToUserRaw($user_id, $owes_id) - $this->userOwesToUserRaw($owes_id, $user_id);
	}
	
	/**
	 * How much $user_id owes to everyone
	 * @param unknown_type $user_id User ID of the payer
	 * @return Total owed
	 */
	function userOwesTotal($user_id)
	{
		$this->db->order_by("id", "asc");
		$users = $this->db->get('users');
		$total = 0;
		
		foreach ($users->result_array() as $user)
		{
			$total = $this->userOwesToUser($user_id, $user['id']);
		}
		
		return $total;
	}
	
	/**
	 * Calculates how much $user_id owes $owes_id
	 * @param unknown_type $user_id
	 * @param unknown_type $owes_id
	 */
	function userOwesToUserRaw($user_id, $owes_id, $bill_id = -1)
	{
		$this->db->select('SUM(items_users.amount) as "total"');
		$this->db->from('bills');
		
		$this->db->where('bills.creator_id', $owes_id);
		$this->db->where('items_users.user_id', $user_id);
		
		if ($bill_id != -1)
		{
			$this->db->where('bills.id', $bill_id);
		}
		
		$this->db->join('items', 'bills.id=items.bill_id');
		$this->db->join('items_users', 'items.id=items_users.item_id');
		
		$result = $this->db->get()->row();
		
		return $result->total;
	}
	
	/**
	 * Fetches all the users and their IDs as keys.
	 */
	function userList()
	{
		$this->db->select(array('id', 'name'));
		$this->db->order_by('name asc');
		$query = $this->db->get('users');
		
		$result = $query->result_array();
		
		foreach ($result as $person)
			$arr[$person['id']] = $person['name'];
		
		return $arr;
	}
	
	/**
	 * True if the user exists, false otherwise
	 */
	function exists($id)
	{
		$this->db->where('users.id', $id);
		$query = $this->db->get('users');
		
		if ($query->num_rows() == 0)
			return false;
		else
			return true;
	}
}