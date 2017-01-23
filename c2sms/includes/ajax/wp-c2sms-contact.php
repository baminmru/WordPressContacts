<?php
	include_once("../../../../../wp-load.php");

	$mobile	= trim($_REQUEST['mobile']);

	if( !$mobile) {
		echo json_encode(array('status' => 'error', 'response' => __('Please complete all fields', 'wp-c2sms')));
		return;
	}
	
	if(preg_match(WP_C2SMS_MOBILE_REGEX, $mobile) == false) {
		echo json_encode(array('status' => 'error', 'response' => __('Please enter a valid mobile number', 'wp-c2sms')));
		return;
	}

	global  $sms, $date;

	$sms_to = $mobile;
	$sms_id=$sms->SendContacts($sms_to);

	if(strpos($sms_id, 'Error')===0 || strpos($sms_id, 'Ошибка')===0){
		echo json_encode(array('status' => 'error', 'response' => $sms_id, 'action' => 'send sms'));
		return;
	}else{
		$i=0;
		$sms_status='в очереди';
		while($i<5 and $sms_status=='в очереди'){
			$sms_status=$sms->GetSMSStatus($sms_id);
			$i=$i+1;
			if($sms_status=='в очереди') sleep(1);
		}
		echo json_encode(array('status' => 'success', 'response' => __('Sending result: ', 'wp-c2sms')." <strong>". $sms_status."</strong>", 'action' => 'send sms'));
		return;
	}
	
	
	
	
