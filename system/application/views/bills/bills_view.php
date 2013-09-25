<?php $this->load->helper('form'); ?>
<?php $this->load->view('header'); ?>
<div class="span16">
	<ul class="breadcrumb">
		<li><a href="<?=site_url(array('bills'))?>">View Bills</a> <span class="divider">/</span></li>
		<li><a href="<?=site_url(array('bills', 'view', $info['id']))?>"><?=$info['title']?></a></li>
	</ul>
</div>
<div class="span4">
	<h2>Overview</h2>
	<dl>
		<dt>Bill Total</dt>
		<dd><?=money($info['total'])?></dd>
		<dt>Number of items</dt>
		<dd><?=$info['num_items']?></dd>
		<dt>Payer</dt>
		<dd><?=$info['name']?></dd>
		<dt>Date of purchase</dt>
		<dd><?=date('F jS, Y', $info['date'])?></dd>
	</dl>
	<br />
	<strong>Money owed</strong> to <?=$info['name']?> by each person from only this bill:
	
	<dl>
		<? foreach($owed as $_owed) : ?>
			<dt><?=$_owed['user']['name']?></dt>
			<dd><?=money($_owed['owes'])?></dd>
		<? endforeach; ?>
	</dl>
</div>
<div class="span12">
	<? if($error) : ?>
		<div class="alert-message error"><?=$error?></div>
	<? endif; ?>
	<? if($success) : ?>
		<div class="alert-message success"><?=$success?></div>
	<? endif; ?>

	<h2><?=$info['title']?></h2>
	
	<table class="zebra-striped">
		<thead>
			<tr>
				<th>Item Name</th>
				<th>Item Cost</th>
				<? foreach($people as $person) : ?>
				<th><?=$person?></th>
				<? endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?=form_open('bills/view/'.$info['id'], array('class' => 'form-stacked'), array('action' => 'update'))?>
			<? foreach($items as $item_id => $item) : ?>
				<tr>
					<td><?=$item['name']?></td>
					<td><?=money($item['cost'])?></td>
					<? foreach($people as $user_id => $person) : ?>
							<td>
								<?=form_checkbox(
									array(
										'name' => 'items['.$item_id.']['.$user_id.']',
										'value' => '1',
										'checked' => array_key_exists($user_id, $item['users'])
									)
								)?>
							</td>
					<? endforeach; ?>
				</tr>
			<? endforeach; ?>
				<tr>
					<td style="text-align:right;" colspan="<?=(2+count($people))?>"><?=form_submit(array('value' => 'Save Bill', 'class' => 'btn primary'))?></td>
				</tr>
			<?=form_close()?>
		</tbody>
	</table>
	
	<h2>Add an Item</h2>
	<table class="zebra-striped">
		<thead>
			<tr>
				<th>Item Name</th>
				<th>Item Cost</th>
				<? foreach($people as $person) : ?>
				<th><?=$person?></th>
				<? endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?=form_open('bills/view/'.$info['id'], array('class' => 'form-stacked'), array('action' => 'add'))?>
				<tr>
					<td><?=form_input(
								array(
									'name' => 'name',
									'class' => 'span2'
								)
						)?></td>
					<td><?=form_input(
								array(
									'name' => 'cost',
									'class' => 'span2'
								)
						)?></td>
					<? foreach($people as $user_id => $person) : ?>
							<td>
								<?=form_checkbox(
									array(
										'name' => 'user['.$user_id.']',
										'value' => '1',
										'checked' => true
									)
								)?>
							</td>
					<? endforeach; ?>
				</tr>
				<tr>
					<td style="text-align:right;" colspan="<?=(2+count($people))?>"><?=form_submit(array('value' => 'Add', 'class' => 'btn primary'))?></td>
				</tr>
			<?=form_close()?>
		</tbody>
	</table>
</div>
<?php $this->load->view('footer'); ?>