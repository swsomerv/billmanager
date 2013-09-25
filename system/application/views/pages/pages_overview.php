<?php $this->load->view('header'); ?>
<div class="span11">
	<h2>Overview</h2>
	<p>Each row corresponds to how much the person (name in the first column of the row) owes to each person.<br /><br />
	Each column corresponds to how much the person (name in the first row of the column) is owed by each person.</p>
	<table class="zebra-striped">
		<thead>
			<tr>
				<th></th>
				<? foreach ($overview_users as $user) : ?>
				<th><?=$user['name']?></th>
				<? endforeach; ?>
				<th>Total to Pay</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($overview_users as $payer) : ?>
			<tr>
				<td><strong><?=$payer['name']?></strong></td>
				<? foreach ($overview_users as $payee) : ?>
					<td><?=($payee['id'] != $payer['id']) ? money($overview_amount[$payer['id']][$payee['id']], false) : ''?></td>
				<? endforeach; ?>
				<td><?=money($overview_amount[$payer['id']]['pay_sum'], false)?></td>
			</tr>
			<? endforeach; ?>
			<tr>
				<td><strong>Total to Receive</strong></td>
				<? foreach ($overview_users as $users) : ?>
					<td><?=money($overview_amount[$users['id']]['receive_sum'], false)?></td>
				<? endforeach; ?>
				<td></td>
			</tr>
	</table>
</div>
<div class="span5">
	<h2>Recently Added Bills</h2>
	<dl>
		<? foreach ($recent_bills as $bill) : ?>
		<dt><a href="<?=site_url(array('bills', 'view', $bill['id']))?>"><?=text_overflow($bill['title'], 30)?></a></dt>
		<dd>With <?=$bill['item_count']?> item<?=($bill['item_count'] == 1) ? '' : 's'?> for a total of <?=money($bill['total'])?></dd>
		<dd>Created by <a href="<?=site_url(array('users', 'view', $bill['user_id']))?>"><?=$bill['name']?></a> on <?=date('M jS, g:ia', $bill['created'])?></dd>
		<? endforeach; ?>
	</dl>

</div>
<?php $this->load->view('footer'); ?>