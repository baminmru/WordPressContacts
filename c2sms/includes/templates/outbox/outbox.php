<div class="wrap">
	<h2><?php _e('Outbox SMS', 'wp-c2sms'); ?></h2>
	<div class="c2cms-button-group">
		<a href="admin.php?page=wp-c2sms-outbox&action=export" class="button"><span class="dashicons dashicons-redo"></span> <?php _e('Export', 'wp-c2sms'); ?></a>
	</div>
	
	<form id="outbox-filter" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $list_table->display(); ?>
	</form>
</div>