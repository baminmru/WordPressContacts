<script type="text/javascript">
	var boxId2 = 'wp_get_message';
	var counter = 'wp_counter';
	var max = 'wp_max';
	function charLeft2() {
		checkSMSLength(boxId2, counter, max);
	}
	
	jQuery(document).ready(function(){
		jQuery("#c2cms-cfg-submit").attr('disabled','disabled');
		jQuery("#wp_get_number").inputmask(
		"+7(999)999-99-99",
			{
			"onincomplete": function(){ jQuery("#c2cms-cfg-submit").attr('disabled','disabled'); }, 
			"oncomplete"  : function(){ jQuery("#c2cms-cfg-submit").removeAttr('disabled');      } 
			}
		);
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
</script>
 
<div class="wrap">
	<form method="post" action="">
		<table class="form-table">
			<?php wp_nonce_field('update-options');?>
			<tr>
				<th><h3><?php _e('Send SMS', 'wp-c2sms'); ?></h4></th>
			</tr>

			<tr>
				<td><?php _e('Send to', 'wp-c2sms'); ?>:</td>
				<td>
					<span class="c2cms-value">  
						<input type="tel" style="width:350px; direction:ltr;" id="wp_get_number" name="wp_get_number" />
						<span style="font-size: 10px"><?php echo sprintf(__('For example: <code>%s</code>', 'wp-c2sms'), '+7(999)888-77-66'); ?></span>
					</span>
				</td>
			</tr>
			
			<tr>
				<td><?php _e('SMS', 'wp-c2sms'); ?>:</td>
				<td>
					<textarea name="wp_get_message" id="wp_get_message" style="width:350px; height: 100px; direction:ltr;"></textarea><br />
					<?php _e('The remaining words', 'wp-c2sms'); ?>: <span id="wp_counter" class="number"></span>/<span id="wp_max" class="number"></span><br />
				</td>
			</tr>
			
			<tr>
				<td>
					<p class="submit">
						<input id="c2cms-cfg-submit" type="submit" class="button-primary" name="SendSMS" value="<?php _e('Send SMS', 'wp-c2sms'); ?>" />
					</p>
				</td>
			</tr>
		</table>
	</form>
</div>