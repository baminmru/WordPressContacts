<?php

include_once('config.php');
include_once('SMSService.php');

class SMSServiceTest 
{
    public $config;
    public $SessionID;
	
	public function print_array($arr){
		if(is_array($arr)){
			foreach ($arr as $key => $value) {
				echo "<br/>Key: ".$key;
				if(is_array($value)){
					echo	'  Value: {<br/>';
					echo print_r($value);
					echo '<br/>}<br/>';
				}else		
					echo	'  Value: '.$value;
					}
		} else {
			echo	$arr;
		}
	}

    public function setup()
    {
        global $config;
        $this->config = $config;
    }

    protected function GetBalans() {
        $request = array('Action'=>'GetBalans','KEY'=>'ab3a3e1fde6ca33659afa82a7e89a392','SITE'=>'wpsms.baminote2.local');
        $my = new SMSService($this->config, $request);
       return $my->GetBalans();
    }
	
	
	 protected function GetSMSStatus($SmsID) {
        $request = array('Action'=>'GetSMSStatus','KEY'=>'ab3a3e1fde6ca33659afa82a7e89a392','SmsID'=>$SmsID,'SITE'=>'wpsms.baminote2.local');
        $my = new SMSService($this->config, $request);
       return $my->GetSMSStatus();
    }

	
	 protected function SendSms($Sender,$SendTo,$SmsText) {
        $request = array('Action'=>'SendSms','KEY'=>'ab3a3e1fde6ca33659afa82a7e89a392','SmsText'=>'Test SMS INFO','Sender'=>$Sender,'SendTo'=>$SendTo,'SITE'=>'wpsms.baminote2.local');
        $my = new SMSService($this->config, $request);
        return $my->SendSms();
    }
	
	 protected function GetSmsList() {
        $request = array('Action'=>'GetSmsList','KEY'=>'ab3a3e1fde6ca33659afa82a7e89a392','SITE'=>'wpsms.baminote2.local');
        $my = new SMSService($this->config, $request);
        return $my->GetSmsList();
    }
	
	protected function GetAllSmsList() {
        $request = array('Action'=>'GetAllSmsList','KEY'=>'ab3a3e1fde6ca33659afa82a7e89a392','SITE'=>'wpsms.baminote2.local');
        $my = new SMSService($this->config, $request);
        return $my->GetAllSmsList();
    }
	
	protected function RefreshSMSStatus() {
        $request = array('Action'=>'RefreshSMSStatus','KEY'=>'ab3a3e1fde6ca33659afa82a7e89a392','SITE'=>'wpsms.baminote2.local');
        $my = new SMSService($this->config, $request);
        return $my->RefreshSMSStatus();
    }
	
  
// ------------------------- tests ---------------------------------
    public function testBalance() {
        $balans=$this->GetBalans();
        echo '<br/>Balance: <br> = '; 
		echo $balans;
    }

   
    public function testGetSmsList()
    {
        $list = $this->GetSmsList();
        //$this->print_array($list);
        echo '<br/>GetSmsList:';
		echo ' <br>Result:';
		echo $this->print_array($list);
   
    }
	
	 public function testGetAllSmsList()
    {
       
        $list = $this->GetAllSmsList();
        //$this->print_array($list);
        echo '<br/>GetSmsList:';
		 echo ' <br>Result:';
		 echo $this->print_array($list);
   
    }
	
	
	 public function testSend()
    {
       
        $smsid= $this->SendSms('bami','79213746485','hello mr. twister address:1231435 phone 4567890','Bami' );
     
        echo '<br/>ID</br>='.$smsid;
		
		 $status= $this->GetSMSStatus($smsid);
	
		 echo ' <br>Status='. $status;
		 
    }
	

   

	public function Tests(){
	
		echo 'Refresh:'.$this->RefreshSMSStatus().' SMS';
		echo '<hr/>'; 
		$this->testBalance() ;
		echo '<hr/>'; 
		$this->testSend() ;
		/*echo '<hr/>'; 
		$this->testSend();
		echo '<hr/>'; 
		$this->testSend() ;
		echo '<hr/>'; 
		$this->testSend() ;
		echo '<hr/>'; 
		$this->testSend() ;
		echo '<hr/>'; 
		$this->testSend() ;
		echo '<hr/>'; 
		$this->testSend() ;
		echo '<hr/>'; 
		$this->testSend() ;*/
		echo '<hr/>'; 
		$this->testGetAllSmsList() ;
	}

}

$tester = new SMSServiceTest();
$tester->setup();
$tester->Tests();