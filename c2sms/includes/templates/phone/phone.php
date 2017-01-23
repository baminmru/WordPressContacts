<div class="wrap">
	<h2><?php _e('Phone blacklist', 'wp-c2sms'); ?></h2>
	<div class="c2cms-button-group">
		<a href="admin.php?page=wp-c2sms-phone&action=create" class="button"><span class="dashicons dashicons-plus-alt"></span> <?php _e('Add', 'wp-c2sms'); ?></a>
	</div>
	
	<form id="outbox-filter" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $list_table->display(); ?>
	</form>
</div>