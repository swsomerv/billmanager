<?php $this->load->view('header'); ?>
<div class="span16">
	<h1>The bill "<?=$bill_name?>" has been created</h1>
	<a href="<?=base_url()?>bills/add" class="btn primary">Add Another Bill</a>
	<a href="<?=base_url()?>bills/view/<?=$bill_id?>" class="btn info">View Bill</a>
</div>
<?php $this->load->view('footer'); ?>