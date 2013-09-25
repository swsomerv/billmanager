<?php
class Bill extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Create a bill
	 * @param unknown_type $user_id User ID of the bill owner
	 * @param unknown_type $title Name of the bill
	 * @param unknown_type $date The date the purchase was made (unix time stamp)
	 * @return ID of the bill
	 */
	function create($user_id, $title, $date)
	{
		$result = mysql_query("INSERT INTO `bills`(`created`, `creator_id`, `title`, `date`)
										VALUES('".time()."', '".$user_id."', '".$title."', '".$date."')");
		
		return mysql_insert_id();
	}
	
	/**
	 * True if the bill exists
	 */
	function exists($id)
	{
		return ($this->db->select('id')->where('bills.id', $id)->count_all_results('bills') > 0);
	}
	
	/**
	 * Total of the bill
	 * @param unknown_type $id The bill ID
	 * @return Total
	 */
	function total($id)
	{
		$this->db->select('SUM(items_users.amount) as total');
		$this->db->from('bills');
		
		$this->db->where('bills.id', $id);
		
		$this->db->join('items', 'bills.id=items.bill_id');
		$this->db->join('items_users', 'items.id=items_users.item_id');
		
		$result = $this->db->get()->row();
		
		return $result->total;
	}
	

	/**
	 * Totals the amount $user_id owes for the bill
	 * @param unknown_type $id The bill ID
	 * @param unknown_type $user_id The user ID
	 * @return Total
	 */
	function userTotal($id, $user_id)
	{
		$this->db->select('SUM(items_users.amount) AS total, COUNT(items_users.user_id) AS users');
		$this->db->from('bills');
		
		$this->db->where('bills.id', $id);
		$this->db->where('items_users.user_id', $user_id);
		
		$this->db->join('items', 'bills.id=items.bill_id');
		$this->db->join('items_users', 'items.id=items_users.item_id');
		
		$result = $this->db->get()->row();
		
		return $result->total / $result->users;
	}

	/**
	 * Count how many people are on the bill
	 * @param unknown_type $id The bill ID
	 * @return Total users
	 */
	function countUsers($id)
	{
		$this->db->select('COUNT(items_users.user_id) AS count');
		$this->db->from('bills');
		
		$this->db->where('bills.id', $id);
		$this->db->join('items', 'bills.id=items.bill_id');
		$this->db->join('items_users', 'items.id=items_users.item_id');
		
		$result = $this->db->get()->row();
		
		return $result->count;
	}
	

	/**
	 * Count how many items are on the bill
	 * @param unknown_type $id The bill ID
	 * @return Total items
	 */
	function countItems($id)
	{
		$this->db->select('COUNT(items.id) AS count');
		$this->db->from('bills');
		
		$this->db->where('bills.id', $id);
		$this->db->join('items', 'bills.id=items.bill_id');
		
		$result = $this->db->get()->row();
		
		return $result->count;
	}
	
	/**
	 * Fetches the last $limit of bills (e.g. 10 most recent bills)
	 * @param unknown_type $limit
	 */
	function billList($limit = -1)
	{
		$this->db->select('bills.id, bills.title, bills.created ,bills.date, users.name, users.id AS user_id');
		$this->db->from('bills');
		$this->db->order_by('created', 'desc');
		
		if ($limit != -1)
			$this->db->limit($limit);
		
		$this->db->join('users', 'bills.creator_id=users.id');
		
		$result = $this->db->get()->result_array();
		$i = 0;
		
		foreach ($result as $key => $bill)
		{
			$result[$key]['item_count'] = $this->countItems($bill['id']);
			$result[$key]['total'] = $this->total($bill['id']);
			
			$i++;
		}
		
		return $result;
	}
	
	/**
	* Bill info (bill name, creator name, date created, total, # of items)
	*/
	function info($id)
	{
		$this->db->select('bills.id,SUM(items.cost) as total, COUNT(items.id) as num_items, 
									users.name, users.id as creator_id, bills.title, bills.date');
		$this->db->from('bills');
	
		$this->db->where('bills.id', $id);
	
		$this->db->join('items', 'bills.id=items.bill_id');
		$this->db->join('users', 'bills.creator_id=users.id');
	
		return $this->db->get()->result_array();
	}
	
	/**
	 * Returns all of the items on the bill.
	 * Including the cost and who shares the cost.
	 */
	function getItems($id)
	{
		$this->db->select('items.name, items.cost, items.id, items_users.user_id');
		
		$this->db->from('bills');
		
		$this->db->where('bills.id', $id);
		
		$this->db->join('items', 'bills.id=items.bill_id');
		$this->db->join('users', 'bills.creator_id=users.id');
		$this->db->join('items_users', 'items.id=items_users.item_id');
		
		$result = $this->db->get()->result_array();
		
		$items = array();
		
		foreach ($result as $_result)
		{
			$items[$_result['id']]['name'] = $_result['name'];
			$items[$_result['id']]['cost'] = $_result['cost'];
			
			if (array_key_exists('users', $items[$_result['id']]))
				$items[$_result['id']]['users'][$_result['user_id']] = 1;
			else
				$items[$_result['id']]['users'] = array($_result['user_id'] => 1);
		}
		
		return $items;
	}
	
	/**
	* Sums the total of the bills within the given time range (unix timestamps).
	*/
	function sumBills($start = 0, $end = 0)
	{
		$this->db->select('SUM(items.cost) as total');
		
		$this->db->from('bills');
		
		if ($start != 0)
			$this->db->where('bills.date >=', $start);
		
		if ($end != 0)
			$this->db->where('bills.date <=', $end);
		
		$this->db->join('items', 'bills.id=items.bill_id');
		
		$result = $this->db->get()->row();
		
		return $result->total;
	}
	
	/**
	 * Used for updating who owes who
	 */
	function rebuildItemsUsersAssoc($bill_id, $items)
	{
		// delete existing ones
		mysql_query("DELETE items_users.* FROM items_users JOIN items ON items.id=items_users.item_id WHERE items.bill_id=".$bill_id);
		
		foreach ($items as $item_id => $users)
		{
			$this->db->select('cost');
			$this->db->from('items');
			
			$this->db->where('id', $item_id);
			
			$cost = $this->db->get()->row()->cost;
			
			$query = "INSERT INTO `items_users`(`item_id`, `user_id`, `amount`) VALUES";
			
			foreach ($users as $user_id => $val)
				$query .= "('".$item_id."', '".$user_id."', '".($cost/count($users))."'),";
			
			mysql_query(substr($query, 0, -1));
		}
	}
}