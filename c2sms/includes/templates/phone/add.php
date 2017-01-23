<div class="wrap">
	<h2><?php _e('Add Phone to blacklist', 'wp-c2sms'); ?></h2>
	<form method="post" action="admin.php?page=wp-c2sms-phone">
		<?php wp_nonce_field('add-phone');?>
		<table class="form-table">
			<tr>
				<td><?php _e('Phone', 'wp-c2sms'); ?>:</td>
				<td>
					<span class="c2cms-value">  
						<input type="tel" style="width:350px; direction:ltr;" id="id_phone" name="newphone" />
					</span>
				</td>
			</tr>
			
			<tr>
				<td>
					<p class="submit">
						<input id="c2cms-cfg-submit" type="submit" class="button-primary" name="submit"  value="<?php _e('Add Phone to blacklist', 'wp-c2sms'); ?>"  />
					</p>
				</td>
			</tr>
		</table>
	</form>
</div>