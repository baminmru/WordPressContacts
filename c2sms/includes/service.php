<?php  
class SMSService
{


	private function get($data)    {
       // $time_start = microtime(true);
		
		
		$data =array_merge($data, array('KEY'=>get_option('WP_C2SMS_KEY'),'SITE'=>$_SERVER['SERVER_NAME']));
		$jdata = json_encode($data);
			
        $params = array('http' => array(
            'method' => 'POST',
            'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
                . "Content-Length: " . strlen($jdata) . "\r\n",
            'content' => $jdata
        ));
       
		$server=get_option('WP_C2SMS_SERVER');	
		$ctx = stream_context_create();
        stream_context_set_option($ctx, $params);
		
		
        $fp = fopen( $server, FOPEN_READ, false, $ctx);
		if (!$fp) {
            return '';
        }
        $response = stream_get_contents($fp);
		
		
	   
		if ($response === false) {
            return '';
        }   
		$r=json_decode($response);
		
		//file_put_contents('C:\bami\projects\Selenkov\log\wp_debug.txt',$r, FILE_APPEND);
       
	   return $r;
	}
	
	public function GetCredit(){
	
		if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');;
			
		$data=array('Action'=>'GetBalans');
		return $this->get($data);
	}
	
	public function SendContacts($SendTo){
		if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$text=get_option('WP_C2SMS_CONTACT');
		$order   = array("\r\n", "\n", "\r");
		$replace = ' ';
		$text = str_replace($order, $replace, $text);
		$data=array('Action'=>'SendSms','SmsText'=>$text,'SendTo'=>$SendTo,'ClientIP'=>$_SERVER['REMOTE_ADDR']);
		return $this->get($data);
	}
	
	public function SendSMS($SendTo,$SmsText){
		if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$text=$SmsText;
		$order   = array("\r\n", "\n", "\r");
		$replace = ' ';
		$text = str_replace($order, $replace, $text);
		$data=array('Action'=>'SendSms','SmsText'=> $text,'SendTo'=> ($SendTo),'ClientIP'=>$_SERVER['REMOTE_ADDR']);
		return $this->get($data);
	}
	
	public function GetSMSStatus($SmsID){
		if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'GetSMSStatus','SmsID'=>$SmsID);
		return $this->get($data);
	}
	
	public function SetConfig(){
		if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$oktext=get_option('WP_C2SMS_OKTEXT');
		$locktext=get_option('WP_C2SMS_LOCKTEXT');
		$smsint=get_option('WP_C2SMS_SMSINTERVAL','3');
		$ipint=get_option('WP_C2SMS_IPINTERVAL','3');
		$data=array('Action'=>'SetConfig','SMSInterval'=>$smsint,'IPInterval'=>$ipint,'OKText'=>$oktext,'LockText'=>$locktext);
		return $this->get($data);
	}
	
	public function RefreshSMSStatus(){
	if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'RefreshSMSStatus');
		$this->get($data);
	}
	
	
	public function GetSMSList(){
	    if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'GetSmsList');
		return $this->get($data);
	}
	
	public function GetIpList(){
	    if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'GetIpList');
		return $this->get($data);
	}
	
	public function GetPhoneList(){
	    if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'GetPhoneList');
		return $this->get($data);
	}
	
	
	public function DelIP($IP){
	    if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'DelIP','IP'=>$IP);
		return $this->get($data);
	}
	
	public function AddIP($IP){
	    if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'AddIP','IP'=>$IP);
		return $this->get($data);
	}
	
	public function DelPhone($Phone){
	    if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'DelPhone','Phone'=>$Phone);
		return $this->get($data);
	}
	
		
	public function AddPhone($Phone){
	    if(get_option('WP_C2SMS_SERVER')=='')
			return __('Error:SMS API Server not defined', 'wp-c2sms');;
		if(get_option('WP_C2SMS_KEY')=='')
			return __('Error:SMS API key not defined', 'wp-c2sms');
		$data=array('Action'=>'AddPhone','Phone'=>$Phone);
		return $this->get($data);
	}
}


