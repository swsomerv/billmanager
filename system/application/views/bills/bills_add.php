<?php $this->load->helper('form'); ?>
<?php $this->load->view('header'); ?>
<div class="span16">
	<ul class="breadcrumb">
		<li><a href="<?=site_url(array('bills'))?>">View Bills</a> <span class="divider">/</span></li>
		<li><a href="<?=site_url(array('bills', 'add'))?>">Add a Bill</a></li>
	</ul>
</div>
<div class="span16">
	<?=form_open('bills/add', array('class' => 'form-stacked'))?>
		<?=form_fieldset('Bill Details')?>
		<script type="text/javascript">
			$(function() {
				$( "#bill_date" ).datepicker();
			});
		</script>
		<div class="form-stacked input_splitter" style="border-top:0px;">
			<div class="clearfix <?=form_error('bill_name') ? 'error' : ''?>">	
				<?=form_label('Bill Name', 'bill_name')?>
				<?=form_input(
					array(
						'name' => 'bill_name',
						'id' => 'bill_name',
						'class' => 'span6',
						'value' => set_value('bill_name')
					)
				)?>
				<?=form_error('bill_name', '<span class="help-inline">', '</span>')?>
			</div>
			<div class="clearfix <?=form_error('bill_date') ? 'error' : ''?>">
				<?=form_label('Date', 'bill_date')?>
				<?=form_input(
					array(
						'name' => 'bill_date',
						'id' => 'bill_date',
						'class' => 'span2',
						'value' => set_value('bill_date')
					)
				)?>
				<?=form_error('bill_date', '<span class="help-inline">', '</span>')?>
			</div>
			<div class="clearfix <?=form_error('bill_discount') ? 'error' : ''?>">
				<?=form_label('Discount', 'bill_discount')?>
				<?=form_input(
					array(
						'name' => 'bill_discount',
						'id' => 'bill_discount',
						'class' => 'span2',
						'value' => set_value('bill_discount', 0)
					)
				)?>%
				<?=form_error('bill_discount', '<span class="help-inline">', '</span>')?>
			</div>
			<div class="clearfix <?=form_error('bill_creator') ? 'error' : ''?>">
				<?=form_label('Bill Payer', 'bill_creator')?>
				<?=form_dropdown('bill_creator', $people, set_value('bill_creator'))?>
				<?=form_error('bill_creator', '<span class="help-inline">', '</span>')?>
			</div>
		</div>

		<?=form_fieldset_close()?>
		<?=form_fieldset('Bill Items')?>
			<?=form_error('items[]', '<div class="alert-message error">', '</div>')?>
			<?php if (!$enough_items) : ?>
				<div class="alert-message error">You must enter at least one item.</div>
			<?php endif; ?>
			<? for($i = 0; $i < 20; $i++) : ?>
			
				<div class="row input_splitter">
					<div class="span7">		
						<?=form_label('Item #'.($i+1), 'item'.$i)?>
						<?=form_input(
							array(
								'name' => 'items['.$i.'][name]',
								'id' => 'item'.$i,
								'class' => 'span6',
								'value' => set_value_array(array('items', $i, 'name'))
							)
						)?>
					</div>
					
					<div class="span3">
						<?=form_label('Cost', 'cost'.$i)?>
						<?=form_input(
								array(
									'name' => 'items['.$i.'][cost]',
									'id' => 'cost'.$i,
									'class' => 'span2',
									'value' => set_value_array(array('items', $i, 'cost'))
								)
						)?>
					</div>
					<div class="span10">
						<div class="row">
							<?php foreach($people as $id => $person) : ?>
							<div class="span2">
								<label>
									<?=form_checkbox(
											array(
												'name' => 'items['.$i.'][user]['.$id.']',
												'id' => 'user', 
												'value' => '1',
												'tabindex' => -1,
												'checked' => set_checkbox_array(array('items', $i, 'user', $id), true)
											)
										)?> 
									<span><?=$person?></span>
								</label>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			
			<?php endfor; ?>
			<div class="actions">
			<?=form_submit(array('value' => 'Save Bill', 'class' => 'btn primary'))?>
			</div>
		<?=form_fieldset_close()?>
	<?=form_close()?>
</div>
<?php $this->load->view('footer'); ?>