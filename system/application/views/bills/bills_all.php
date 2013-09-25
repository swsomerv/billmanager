<?php $this->load->view('header'); ?>
<div class="span16">
	<ul class="breadcrumb">
		<li><a href="<?=site_url(array('bills'))?>">View Bills</a></li>
	</ul>
</div>
<div class="span4">
	<h2>Overview</h2>
	<dl>
		<dt>Number of bills</dt>
		<dd><?=count($bills)?></dd>
		<dt>Total this week</dt>
		<dd><?=money($this_week)?></dd>
		<dt>Total last week</dt>
		<dd><?=money($last_week)?></dd>
		<dt>Total this month</dt>
		<dd><?=money($this_month)?></dd>
		<dt>Total last month</dt>
		<dd><?=money($last_month)?></dd>
	</dl>
</div>
<div class="span12">
	<h2>Bills</h2>
		
	<table class="zebra-striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Total</th>
				<th>Payer</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<? if(empty($bills)) : ?>
				<tr>
					<td colspan="4">No bills at this time</td>
				</tr>
			<? endif; ?>
			<? foreach($bills as $bill) : ?>
				<tr>
					<td><a href="<?=site_url(array('bills', 'view', $bill['id']))?>"><?=$bill['title']?></a></td>
					<td><?=money($bill['total'])?></td>
					<td><?=$bill['name']?></td>
					<td><?=date('F jS, Y', $bill['date'])?></td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</div>
<?php $this->load->view('footer'); ?>