<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Bill Manager</title>
		<link rel="stylesheet" href="<?=base_url()?>css/bootstrap.css" type="text/css" />
		<link rel="stylesheet" href="<?=base_url()?>css/style.css" type="text/css" />
		<link type="text/css" href="<?=base_url()?>css/jquery-ui-1.8.16.custom.css" rel="Stylesheet" />	
		<script type="text/javascript" src="<?=base_url()?>js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="<?=base_url()?>js/jquery-ui-1.8.16.custom.min.js"></script>
	</head>
	<body>
		<div class="topbar">
			<div class="fill">
				<div class="container">
					<a class="brand" href="<?=site_url(array('pages'))?>">Bill Manager</a>
					<ul class="nav">
						<li><a href="<?=base_url()?>">Overview</a></li>
						<li><a href="<?=site_url(array('bills', 'add'))?>">Add a Bill</a></li>
						<li><a href="<?=site_url(array('bills'))?>">View Bills</a></li>
						<!-- <li><a href="<?=site_url(array('users'))?>">View Users</a></li> -->
					</ul>
				</div>
			</div>
		</div>
		
		<div class="container" id="container">
			<div class="row">