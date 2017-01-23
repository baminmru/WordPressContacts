
<script type="text/javascript">

	 function filterPhone(phone){
            var pattern = /^(\+)?\d{1}(\(|\s)?\d{3}(\))?(-|\s)?\d{3}(-|\s)?\d{2}(-|\s)?\d{2}$/;
            return pattern.test(phone);
        }

       

        function checkForm(){
            var ph = document.getElementById("c2cms-mobile").value;
             if(filterPhone(ph) ){  
                //alert("OK");
                return true;
            } else {
               //alert("Error!");
                return false;
            }
        }
		
		function wp_c2sms_submit() {
				if( checkForm() ){
			
					jQuery("#c2cms-result").hide();
					subscriber = new Array();
					subscriber['mobile'] = jQuery("#c2cms-mobile").val();
					
					
					jQuery("#wpc2sms-contacts").ajaxStart(function(){
						jQuery("#c2cms-submit").attr('disabled', 'disabled');
						jQuery("#c2cms-submit").text("<?php _e('Loading...', 'wp-c2sms'); ?>");
					});
					
					jQuery("#wpc2sms-contacts").ajaxComplete(function(){
						jQuery("#c2cms-submit").removeAttr('disabled');
						jQuery("#c2cms-submit").text("<?php _e('Send Contacts', 'wp-c2sms'); ?>");
					});
					
					jQuery.get("<?php echo WP_C2SMS_DIR_PLUGIN; ?>includes/ajax/wp-c2sms-contact.php", { mobile:subscriber['mobile']}, function(data, status){
						var response = jQuery.parseJSON(data);
						
						if(response.status == 'error') {
							jQuery("#c2cms-result").fadeIn();
							jQuery("#c2cms-result").html('<span class="c2cms-message-error">' + response.response + '</div>');
						}
						
						if(response.status == 'success') {
							jQuery("#c2cms-result").fadeIn();
							jQuery("#c2cms-result").html('<span class="c2cms-message-success">' + response.response + '</div>');
						}
					});
				}else{
					jQuery("#c2cms-result").text("<?php _e('Phone number incomplete...', 'wp-c2sms').'(+7 999 111 22 33)'; ?>");
				}
			}
		
	
	
	   
	
</script>
<div id="wpc2sms-contacts">
	<div id="c2cms-result"></div>
	<p><?php echo $description; ?></p>
	<div class="c2cms-contact-form">
		<label><?php _e('Your mobile', 'wp-c2sms'); ?>:</label>
		<input id="c2cms-mobile" type="tel" placeholder='+7 999 111 22 33' class="c2cms-input"/>
	</div>
	
	<button class="c2cms-button" id="c2cms-submit" onclick="return wp_c2sms_submit()" ><?php _e('Send Contacts', 'wp-c2sms'); ?> </button>
</div>
