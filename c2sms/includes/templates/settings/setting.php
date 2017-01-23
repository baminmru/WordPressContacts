<script type="text/javascript">
	var boxId2 = 'WP_C2SMS_CONTACT';
	var counter = 'wp_counter';
	var max = 'wp_max';
	function charLeft2() {
		checkSMSLength(boxId2, counter,  max);
	}
	
	jQuery(document).ready(function(){
	
		charLeft2();
		jQuery("#" + boxId2).bind('keyup', function() {
			charLeft2();
		});
		jQuery("#" + boxId2).bind('keydown', function() {
			charLeft2();
		});
		jQuery("#" + boxId2).bind('paste', function(e) {
			charLeft2();
		});
	});
	
	function validate(event) {
		var key = window.event ? event.keyCode : event.which;

		if (event.keyCode === 8 || event.keyCode === 46
		 || event.keyCode === 37 || event.keyCode === 39) {
			return true;
		}
		else if ( key < 48 || key > 57 ) {
			return false;
		}
		else return true;
	};
</script>

<?php do_action('WP_C2SMS_settings_page'); ?>

<div class="wrap">
	<?php include( dirname( __FILE__ ) . '/tabs.php' ); ?>
	<table class="form-table">
		<form method="post" action="options.php" name="form">
			<?php wp_nonce_field('update-options');?>
			
			<tr>
				<td><?php _e('SMS API Service', 'wp-c2sms'); ?>:</td>
				<td>
					<input type="text"  style="width: 350px;" name="WP_C2SMS_SERVER" value="<?php echo get_option('WP_C2SMS_SERVER'); ?>"/>
					<p class="description"><?php _e('Enter SMS API server address.', 'wp-c2sms'); ?></p>
				</td>
			</tr>
			
			<tr>
				<td><?php _e('Key for SMS API', 'wp-c2sms'); ?>:</td>
				<td>
					<input type="text"  style="width: 350px;" name="WP_C2SMS_KEY" value="<?php echo get_option('WP_C2SMS_KEY'); ?>"/>
					<p class="description"><?php _e('Enter KEY for SMS API.', 'wp-c2sms'); ?></p>
				</td>
			</tr>
			
			<tr>
				<td><?php _e('Contacts text', 'wp-c2sms'); ?>:</td>
				<td>
					<textarea  style="width:350px; height: 100px; direction:ltr;" id="WP_C2SMS_CONTACT" name="WP_C2SMS_CONTACT"><?php echo get_option('WP_C2SMS_CONTACT'); ?></textarea>
					<p class="description"><?php _e('Enter Contact Info.', 'wp-c2sms'); ?></p>
					<?php _e('The remaining words', 'wp-c2sms'); ?>: <span id="wp_counter" class="number"></span>/<span id="wp_max" class="number"></span><br />
				</td>
			</tr>
			
			
			<tr>
				<td><?php _e('Phone protection interval', 'wp-c2sms'); ?>:</td>
				<td>
					<input type="number"  style="width: 350px;" name="WP_C2SMS_SMSINTERVAL"  onkeypress='return validate(event);' value="<?php echo get_option('WP_C2SMS_SMSINTERVAL',3); ?>"/>
					<p class="description"><?php _e('Interval for send SMS to same Phone', 'wp-c2sms'); ?></p>
				</td>
			</tr>
			
			<tr>
				<td><?php _e('IP protection interval', 'wp-c2sms'); ?>:</td>
				<td>
					<input type="number"  style="width: 350px;" name="WP_C2SMS_IPINTERVAL"  onkeypress='return validate(event);' value="<?php echo get_option('WP_C2SMS_IPINTERVAL',3); ?>"/>
					<p class="description"><?php _e('Interval for send SMS to same IP', 'wp-c2sms'); ?></p>
				</td>
			</tr>
			
			<tr>
				<td><?php _e('Locked text message', 'wp-c2sms'); ?>:</td>
				<td>
					<input type="text"  style="width: 350px;" name="WP_C2SMS_LOCKTEXT" value="<?php echo get_option('WP_C2SMS_LOCKTEXT','Сработала защита от спама'); ?>"/>
					<p class="description"><?php _e('Message for locking status', 'wp-c2sms'); ?></p>
				</td>
			</tr>
			<tr>
				<td><?php _e('OK text message', 'wp-c2sms'); ?>:</td>
				<td>
					<input type="text"  style="width: 350px;" name="WP_C2SMS_OKTEXT" value="<?php echo get_option('WP_C2SMS_OKTEXT','Сообщение успешно доставлено'); ?>"/>
					<p class="description"><?php _e('Message for success sending', 'wp-c2sms'); ?></p>
				</td>
			</tr>
			
			<tr>
				<td>
					<p class="submit">
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="page_options" value="WP_C2SMS_SERVER,WP_C2SMS_KEY,WP_C2SMS_CONTACT,WP_C2SMS_SMSINTERVAL,WP_C2SMS_IPINTERVAL,WP_C2SMS_LOCKTEXT,WP_C2SMS_OKTEXT" />
						<input type="submit" class="button-primary" name="Submit" value="<?php _e('Update', 'wp-c2sms'); ?>" />
					</p>
				</td>
			</tr>
		</form>	
	</table>
</div>