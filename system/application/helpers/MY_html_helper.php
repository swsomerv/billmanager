<?php

/**
 * Formats money
 * @param unknown_type $amount The amount to format
 * @param unknown_type $allow_neg When false, this will show $0 instead of the negative number.
 */
function money($amount, $allow_neg = true)
{
	$output = '<span class="';
	
	if ($amount > 0)
	{
		$output .= 'green">$' . number_format($amount, 2);
	}
	else if ($allow_neg && $amount != 0)
	{
		$output .= '">-$' . number_format(abs($amount), 2);
	}
	else
	{
		$output .= '">$0.00';
	}
	
	$output .= '</span>';
	
	return $output;
}

/**
 * Puts ellipsis (...) on long text
 */
function text_overflow($str, $max)
{
	if (strlen($str) <= $max)
	{
		return $str;
	}
	else
	{
		return substr($str, 0, $max).'...';
	}
}

/**
 * Same as set_value() but can check an array
 * the path is passed in as an array e.g. array('User', '0', 'name')
 */
function set_value_array($path, $default = '')
{
	if (empty($_POST) || empty($path))
		return $default;
	
	$value = $_POST;
	
	foreach ($path as $_path)
	{
		if (isset($value[$_path]))
			$value = $value[$_path];
		else
			return $default;
	}
	
	return $value;
}

/**
* Same as set_checkbox() but can check an array
* the path is passed in as an array e.g. array('User', '0', 'name')
*/
function set_checkbox_array($path, $default = false)
{
	if (empty($_POST) || empty($path))
		return $default;
	
	$value = $_POST;

	foreach ($path as $_path)
	{
		if (!isset($value[$_path]))
		{
			return false;
		}
		
		$value = $value[$_path];
	}

	return true;
}