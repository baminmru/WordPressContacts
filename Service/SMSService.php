<?php
class SMSService
{
    private $db;
    private $config;
    private $SessionData;
    private $request;
    public  $action;

    public function __construct($config, $request)
    {
	
        // check action
        $this->request = $request;
        $this->action = isset($this->request['Action']) ? $this->request['Action'] : '';
		
		
		
        if (!method_exists($this, $this->action)) {
            throw new Exception( _('Unknown action:').' "' . $this->action . '"');
        }
		$this->config = $config;
	
        // connect to DB
        $this->db = new mysqli($config['db']['server'], $config['db']['username'], $config['db']['password'],
                               $config['db']['database']);
        if ($this->db->connect_error) {
            throw new Exception(_('Connect Error').' (' . $this->db->connect_errno . ') ' . $this->db->connect_error);
        }
        $this->db->set_charset("utf8");

		$key=isset($this->request['KEY']) ? $this->request['KEY'] : '';
		
		$ip=isset($this->request['KEY']) ? $this->request['SITE'] : '';
		
		if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',">>>: request addr: {$ip} \r\n"			, FILE_APPEND);
	
		//echo $ip;
		// лезем в БД и ищем ключ
		$stmt = $this->db->prepare("select clientkeyid,keycreattime,keyexparetime,keypaused from clientkey where md5(concat(clientkeyid,domain,keyvalue))=? and domain=? and (keypaused is null or keypaused=0)" );
       if ($stmt){
			$stmt->bind_param('ss', $key, $ip);
			$stmt->execute();
			$stmt->bind_result($c1, $c2, $c3, $c4);
			if ($stmt->fetch()) { // есть такая
				$this->SessionData['clientkeyid']    = $c1;
				//$this->SessionData['keycreattime']   = $c2;
				//$this->SessionData['keyexparetime']   = $c3;
				//$this->SessionData['keypaused'] = $c4;
			} 
			$stmt->close();
		}else{
			//throw new Exception('Query error!');
		}
		
        
    }

    public function __destruct()
    {
        $this->db->close();
    }

	 // Новый UUID
    private function NewID()
    {
        $result = '';
        $stmt = $this->db->prepare('SELECT UUID()');
        $stmt->execute();
        $stmt->bind_result($uuid);
        if ($stmt->fetch()) {
            $result = $uuid;
        }
        $stmt->close();
        return $result;
    }
	private function DecodeStatus($status){
		switch( $status) {
			case 'ENROUTE':
						$status='сообщение отправлено оператору';
						break;
			case 'DELIVRD':
						$status='сообщение успешно доставлено';
						break;
			case 'EXPIRED':
						$status='время жизни сообщения истекло';
						break;
			case 'DELETED':
						$status='сообщение удалено';
						break;
			case 'UNDELIV':
						$status='не удалось доставить сообщение';
						break;
			case 'ACCEPTD':
						$status='сообщение принято и будет отправлено оператору';
						break;
			case 'UNKNOWN':
						$status='состояние сообщения не определено';
						break;
			case 'REJECTD':
						$status='сообщение отклонено';
						break;
			
		    }
			return $status;
	}
	
	public function SendSMS(){
		$SendTo = isset($this->request['SendTo']) ? $this->request['SendTo'] : '';
        $SmsText = isset($this->request['SmsText']) ? $this->request['SmsText'] : '';
        $smsid = $this->NewID();
		$gateid="";
		
		 if (empty($SendTo))
            return _('Error: SendTo not set!');        
			
		if (empty($SmsText))
            return _('Error: SmsText not set!');   
			
		if (empty($this->SessionData['clientkeyid']))	
			return _('Error: Invalid API KEY!');   
			
		$bad    = array("+", "(", "-",")"," ");
		$good   = array("",  "",  "", "", "");
		$SendTo=str_replace($bad, $good, $SendTo);
			
			
			
		// проверить не блокирован ли IP
		$cnt=0;
		$res = $this->db->query("SELECT count(*) cnt
                                 FROM lockedip 
                                 WHERE  ip='".$_SERVER['REMOTE_ADDR']."'");
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			return _('Error: IP in blacklist!'); 
		}	
		
		
		// проверить не блокирован ли Телефон
		$cnt=0;
		$res = $this->db->query("SELECT count(*) cnt
                                 FROM lockedphone 
                                 WHERE   phone='".$SendTo."'");
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			return _('Error: Phone in blacklist!'); 
		}	
			
		// проверить не спамит ли эта клиентская сессия
		$cnt=0;
		$res = $this->db->query("SELECT count(*) cnt
                                 FROM sms 
                                 WHERE  clientkeyid = '".$this->SessionData['clientkeyid']."' and clientip='".$_SERVER['REMOTE_ADDR']."' and clientport='".$_SERVER['REMOTE_PORT']."' and createtime >=DATE_ADD(Now(), INTERVAL -".$this->config['smsgate']['ipportspam']." MINUTE) ");
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			return _('Error: IP+Port Spam protection! Interval ').$this->config['smsgate']['ipportspam']._(' min.'); 
		}
		
		// проверить не спамит ли этот IP
		$cnt=0;
		$res = $this->db->query("SELECT count(*) cnt
                                 FROM sms 
                                 WHERE  clientkeyid = '".$this->SessionData['clientkeyid']."' and clientip='".$_SERVER['REMOTE_ADDR']."' and createtime >=DATE_ADD(Now(), INTERVAL -".$this->config['smsgate']['ipspam']." MINUTE) ");
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			return _('Error: IP Spam protection! Interval ').$this->config['smsgate']['ipspam']._(' min.'); 
		}

		// проверить не посылали на этот же телефон в течение последних  N минут	
		$cnt=0;
		$res = $this->db->query("SELECT count(*) cnt
                                 FROM sms 
                                 WHERE  clientkeyid = '".$this->SessionData['clientkeyid']."' and smsto='".$SendTo."' and createtime >=DATE_ADD(Now(), INTERVAL -".$this->config['smsgate']['phonespam']." MINUTE) ");
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			return _('Error: Phone Spam protection! Interval ').$this->config['smsgate']['phonespam']._(' min.'); 
		}

	
		$r=$this->config['smsgate']['server']."?user=".$this->config['smsgate']['user']."&pass=".$this->config['smsgate']['password']."&sender=".$this->config['smsgate']['sender']."&number=".urlencode($SendTo)."&text=".urlencode($SmsText);
		if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: request: {$r} \r\n"			, FILE_APPEND);

		$esmeid = file_get_contents($r);
		if (empty($esmeid))
            return _('Error: SMS gate error !');   

		if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: esmeid={$esmeid} \r\n"			, FILE_APPEND);
			
        $q = "INSERT INTO sms (`smsid`, `clientkeyid`, `smsto`, `smstext`,`sender`,`esmeid`,`clientip`,`clientport`,`createtime`) VALUES 
		('{$smsid}' , '{$this->SessionData['clientkeyid']}', '{$SendTo}','{$SmsText}','-','{$esmeid}','{$_SERVER['REMOTE_ADDR']}','{$_SERVER['REMOTE_PORT']}',now())";

        if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: {$q} \r\n"			, FILE_APPEND);

        $stmt = $this->db->prepare($q);

        $res = $stmt->execute();

        $error = $stmt->error."\r\n 	"; //. implode("\r\n 	",$stmt->error_list);
        $stmt->close();
		
        if ($res){
            return $smsid;
			
			
        }else
            return _('Error: while store SendSms row: ').$error;
	
	}
	
	
	public function  GetStatus($smsid){
		if (empty($this->SessionData['clientkeyid']))	
			return 'Error: Invalid API KEY!';  
      
		$gateid="";
		$status="";
		
		 if (empty($smsid))
            return _('Error: SmsID not set!');        
		if ($smsid=="")
            return _('Error: SmsID not set!');  
			
		if( preg_match("/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i", $smsid)){
		
            $stmt = $this->db->prepare('SELECT esmeid,smsstatus
                                        FROM sms
                                        WHERE smsid=? and clientkeyid=?');
            $stmt->bind_param('ss', $smsid ,$this->SessionData['clientkeyid']);
            $stmt->execute();
            $stmt->bind_result($c1, $c2);
            if ($stmt->fetch()) { // есть такая сессия
                $gateid   = $c1;
                $status   = $c2;
  			
            } else {
                $stmt->close();
                throw new Exception(_('Sms record not found!'));
            }
            $stmt->close();
			if(empty($status) || $status='queued' || $status='сообщение отправлено оператору' || $status='сообщение принято и будет отправлено оператору'){
				$status = file_get_contents($this->config['smsgate']['server']."?user=".$this->config['smsgate']['user']."&pass=".$this->config['smsgate']['password']."&smsid=".$gateid);
				if (empty($status))
					return _('Error: SMS gate error !');   
			}
			
			$status=$this->DecodeStatus($status);
			$q = "update sms set smsstatus='".$status."' where smsid='".$smsid."' and clientkeyid='".$this->SessionData['clientkeyid']."'";
			if($this->config['log']==true)
				file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: {$q} \r\n"			, FILE_APPEND);

			$stmt = $this->db->prepare($q);

			$res = $stmt->execute();

			$error = $stmt->error."\r\n 	"; //. implode("\r\n 	",$stmt->error_list);
			$stmt->close();
			
			if ($res){
				return $status;
				
				
			}else
				return _('Error: while get Sms status row: ').$error;
		}else{
			return _('Error: Wrong SmsID format');
		}
	
	}
	
	public function GetSMSStatus(){
		if (empty($this->SessionData['clientkeyid']))	
			return _('Error: Invalid API KEY!');  
        $smsid = isset($this->request['SmsID']) ? $this->request['SmsID'] : '';
		return $this->GetStatus($smsid);
	
	}
	
	public function SendSMS2(){
		$smsid=$this->SendSMS();
		return $this->GetStatus($smsid);
	}
	
	
	public function RefreshSMSStatus(){
		if (empty($this->SessionData['clientkeyid']))	
			return _('Error: Invalid API KEY!');  
		$cnt=0;
		$esmeid="";
		$smsid="";
        $res = $this->db->query("SELECT esmeid,smsid
                                 FROM sms 
                                 WHERE  clientkeyid = '".$this->SessionData['clientkeyid']."' and (smsstatus is null or smsstatus='queued'  or smsstatus='сообщение отправлено оператору' or smsstatus='сообщение принято и будет отправлено оператору') order by sms.createtime desc  limit 0,50");
        while ($row = $res->fetch_assoc()){
		
			$esmeid=$row['esmeid'];
			$smsid=$row['smsid'];
			$status = file_get_contents($this->config['smsgate']['server']."?user=".$this->config['smsgate']['user']."&pass=".$this->config['smsgate']['password']."&smsid=".$esmeid);
			$status=$this->DecodeStatus($status);
			
			$q = "update sms set smsstatus='".$status."' where smsid='".$smsid."' and clientkeyid='".$this->SessionData['clientkeyid']."' ";
			//if($this->config['log']==true)
			//	file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: {$q} \r\n"			, FILE_APPEND);
			$stmt = $this->db->prepare($q);
			$res2 = $stmt->execute();
			$stmt->close();
			$cnt++;
        }
        $res->close();
        return $cnt;
	
	}
	
	public function GetBalans(){
		if (empty($this->SessionData['clientkeyid']))	
			return _('Error: Invalid API KEY!');  
		$balans="";
		
		$balans = file_get_contents($this->config['smsgate']['server']."?user=".$this->config['smsgate']['user']."&pass=".$this->config['smsgate']['password']."&action=balance");
		
		return $balans;
	}
	
	public function GetAllSmsList()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return _('Error: Invalid API KEY!');  
        $result = array();
        $whereclause = isset($this->request['WhereClause']) ? $this->request['WhereClause'] : '1=1';
        $res = $this->db->query("SELECT smsid,smsto,smstext,createtime,smsstatus,clientip,clientport, clientkey.domain
                                 FROM sms left join clientkey on sms.clientkeyid=clientkey.clientkeyid
                                 WHERE  " . $whereclause. " order by sms.createtime desc ");
        while ($row = $res->fetch_assoc()){
            $result[] = $row;
        }
        $res->close();
        return $result;
    }
	
	public function GetSmsList()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return _('Error: Invalid API KEY!');  
        $result = array();
        $whereclause = isset($this->request['WhereClause']) ? $this->request['WhereClause'] : '1=1';
        $res = $this->db->query("SELECT smsid,smsto,smstext,createtime,smsstatus,clientip,clientport
                                 FROM sms
                                 WHERE clientkeyid = '".$this->SessionData['clientkeyid']."' AND " . $whereclause. " order by createtime desc LIMIT 0,1000"  );
        while ($row = $res->fetch_assoc()){
            $result[] = $row;
        }
        $res->close();
        return $result;
    }
	
  
   


}
?>
