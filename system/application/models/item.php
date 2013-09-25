<?php
class Item extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Creates an item and all of the appropriate item <-> user assocations
	 * Accepts an array of the format
	 * 'name' => <name>
	 * 'cost' => <cost>
	 * 'user' => array( <user_id>, <user_id>...)
	 */
	function create($bill_id, $item, $discount)
	{
		$discount = (100 - $discount) / 100;
		
		$result = mysql_query("INSERT INTO `items`(`name`, `bill_id`, `cost`)
										VALUES('".$item['name']."', '".$bill_id."', '".($item['cost'] * $discount)."')");

		$item_id = mysql_insert_id();
		
		$query = "INSERT INTO `items_users`(`item_id`, `user_id`, `amount`) VALUES";
		
		$users = count($item['user']);
		
		foreach ($item['user'] as $user_id => $val)
		{
			$query .= '('.$item_id.', '.$user_id.', '.(($item['cost'] * $discount)/$users).'),';
		}
		
		mysql_query(substr($query, 0, -1));
		
		return $item_id;
	}
}