<?php
class SMSService
{
    private $db;
    private $config;
    private $SessionData;
    private $request;
    public  $action;
	private $cfg_ipinterval;
	private $cfg_ipportinterval;
	private $cfg_smsinterval;
	private $cfg_locktext;
	private $cfg_oktext;

    public function __construct($config, $request)
    {
	
        // check action
        $this->request = $request;
        $this->action = isset($this->request['Action']) ? $this->request['Action'] : '';
		
		
		
        if (!method_exists($this, $this->action)) {
            throw new Exception( 'Неизвестная операция: "' . $this->action . '"');
        }
		$this->config = $config;
	
        // connect to DB
        $this->db = new mysqli($config['db']['server'], $config['db']['username'], $config['db']['password'],
                               $config['db']['database']);
        if ($this->db->connect_error) {
            throw new Exception("Ошибка подключения к БД".' (' . $this->db->connect_errno . ') ' . $this->db->connect_error);
        }
        $this->db->set_charset("utf8");

		$key=isset($this->request['KEY']) ? $this->request['KEY'] : '';
		
		$ip=isset($this->request['SITE']) ? $this->request['SITE'] : '';
		
		
		
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
			if($this->config['log']==true)
				file_put_contents($this->config['logpath'].'/_debug.txt',">>>: key not found\r\n"			, FILE_APPEND);
			return;
		}
		
		try{
			$ss="SELECT * FROM clientconfig WHERE clientkey = '".$this->SessionData['clientkeyid']."' limit 0,1";
	
			$res = $this->db->query($ss);
			if($res){
				while ($row = $res->fetch_assoc()){
					$this->cfg_ipinterval=$row['ipinterval'];
					$this->cfg_ipportinterval=$row['ipportinterval'];
					$this->cfg_smsinterval=$row['smsinterval'];
					$this->cfg_locktext=$row['locktext'];
					$this->cfg_oktext=$row['oktext'];
			
				}
				$res->close();
			}else{
			}
		}catch (Exception $e) {
			
		if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',">>>: exception".$e->getMessage()." \r\n"			, FILE_APPEND);
			
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
						//$status='сообщение успешно доставлено';
						$status=$this->cfg_oktext;
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
			case 'queued':
						$status='в очереди';
						break;
			
		    }
			return $status;
	}
	
	public function SendSMS(){
		$SendTo = isset($this->request['SendTo']) ? $this->request['SendTo'] : '';
        $SmsText = isset($this->request['SmsText']) ? $this->request['SmsText'] : '';
		$ClientIP = isset($this->request['ClientIP']) ? $this->request['ClientIP'] : '';
        $smsid = $this->NewID();
		$gateid="";
		
		 if (empty($SendTo))
            return "Ошибка: Получатель не определен";        
			
		if (empty($SmsText))
            return "Ошибка: Текст СМС не установлен!";   
			
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";   
			
		if (empty($ClientIP ))
            return "Ошибка: IP клиента не установлен!";   
			
		$bad    = array("+", "(", "-",")"," ");
		$good   = array("",  "",  "", "", "");
		$SendTo=str_replace($bad, $good, $SendTo);
			
		//if($this->config['log']==true)
		//	file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: SendTo={$SendTo} \r\n"			, FILE_APPEND);
			
		// проверить не блокирован ли IP
		$cnt=0;
		$res = $this->db->query("SELECT count(*) cnt
                                 FROM lockedip 
                                 WHERE clientkey = '".$this->SessionData['clientkeyid']."' and  ip='".$ClientIP."'");
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			//return "Ошибка: IP адрес в черном списке!"; 
			return "Ошибка: ".$this->cfg_locktext; 
		}	
		
		//if($this->config['log']==true)
		//	file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: IP checked \r\n"			, FILE_APPEND);
		
		
		
		// проверить не блокирован ли Телефон
		$cnt=0;
		$res = $this->db->query("SELECT count(*) cnt
                                 FROM lockedphone 
                                 WHERE  clientkey = '".$this->SessionData['clientkeyid']."' and  phone='".$SendTo."'");
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			//return "Ошибка: Телефон в черном списке!"; 
			return "Ошибка: ".$this->cfg_locktext; 
		}	
			
	
		//if($this->config['log']==true)
		//	file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: phone checked \r\n"			, FILE_APPEND);
		
		
		// проверить не спамит ли этот IP
		$cnt=0;
		$q="SELECT count(*) cnt   FROM sms    WHERE  clientkeyid = '".$this->SessionData['clientkeyid']."' and clientip='".$ClientIP."' and createtime >=DATE_ADD(Now(), INTERVAL -".$this->cfg_ipinterval." MINUTE) ";
		
		//if($this->config['log']==true)
		//	file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: Query:{$q} \r\n"			, FILE_APPEND);
		$res = $this->db->query($q);
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			//return "Ошибка: Защита от спама по IP адресу! Интервал повторной отсылки ".$this->cfg_ipinterval." мин."; 
			return "Ошибка: ".$this->cfg_locktext; 
		}

		//if($this->config['log']==true)
		//	file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: ip spam checked \r\n"			, FILE_APPEND);
	
		
		// проверить не посылали на этот же телефон в течение последних  N минут	
		$cnt=0;
		$res = $this->db->query("SELECT count(*) cnt
                                 FROM sms 
                                 WHERE  clientkeyid = '".$this->SessionData['clientkeyid']."' and smsto='".$SendTo."' and createtime >=DATE_ADD(Now(), INTERVAL -".$this->cfg_smsinterval." MINUTE) ");
        while ($row = $res->fetch_assoc()){
			$cnt=$row['cnt'];
        }
        $res->close();
		
		if($cnt >0){
			//return "Ошибка: Защита от спама по номеру телефона! Интервал повторной отсылки ".$this->cfg_smsinterval." мин."; 
			return "Ошибка: ".$this->cfg_locktext; 
		}
		
		
		//if($this->config['log']==true)
		//	file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: phone spam checked \r\n"			, FILE_APPEND);
	

	
		$r=$this->config['smsgate']['server']."?user=".$this->config['smsgate']['user']."&pass=".$this->config['smsgate']['password']."&sender=".$this->config['smsgate']['sender']."&number=".urlencode($SendTo)."&text=".urlencode($SmsText);
		
		if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: request: {$r} \r\n"			, FILE_APPEND);

		$esmeid = file_get_contents($r);
		if (empty($esmeid))
            return "Ошибка cистемы отсылки сообщений.";   

		if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: esmeid={$esmeid} \r\n"			, FILE_APPEND);
			
        $q = "INSERT INTO sms (`smsid`, `clientkeyid`, `smsto`, `smstext`,`sender`,`esmeid`,`clientip`,`clientport`,`createtime`) VALUES 
		('{$smsid}' , '{$this->SessionData['clientkeyid']}', '{$SendTo}','{$SmsText}','-','{$esmeid}','{$ClientIP}','0',now())";

        if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: {$q} \r\n"			, FILE_APPEND);

        $stmt = $this->db->prepare($q);

        $res = $stmt->execute();

        $error = $stmt->error."\r\n 	"; //. implode("\r\n 	",$stmt->error_list);
        $stmt->close();
		
        if ($res){
            return $smsid;

        }else
            return "Ошибка: при сохранении записи об смс:".$error;
	
	}
	
	public function SetConfig(){
		$SMSInterval = isset($this->request['SMSInterval']) ? $this->request['SMSInterval'] : '';
		$IPInterval = isset($this->request['IPInterval']) ? $this->request['IPInterval'] : '';
        $OKText = isset($this->request['OKText']) ? $this->request['OKText'] : '';
		$LockText = isset($this->request['LockText']) ? $this->request['LockText'] : '';
		
   	    $q = "UPDATE `clientconfig` SET `smsinterval`=".$SMSInterval.",`locktext`='".$LockText."',`oktext`='".$OKText."',`ipinterval`=".$IPInterval.",`ipportinterval`=0 WHERE clientkey='".$this->SessionData['clientkeyid']."'";

        if($this->config['log']==true)
			file_put_contents($this->config['logpath'].'/_debug.txt',			">>>: {$q} \r\n"			, FILE_APPEND);

        $stmt = $this->db->prepare($q);

        $res = $stmt->execute();

        $error = $stmt->error."\r\n 	";
        $stmt->close();
		
		if ($res){
            return 'OK';
        }else
            return "Ошибка: при сохранении записи оконфигурации:".$error;
	}
	
	public function  GetStatus($smsid){
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
      
		$gateid="";
		$status="";
		
		 if (empty($smsid))
            return "Ошибка: Идентификатор СМС не задан!";        
		if ($smsid=="")
            return "Ошибка: Идентификатор СМС не задан!";  
			
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
                throw new Exception("Ошибка: не найдена запись об СМС");
            }
            $stmt->close();
			if(empty($status) || $status='в очереди' || $status='сообщение отправлено оператору' || $status='сообщение принято и будет отправлено оператору'){
				$status = file_get_contents($this->config['smsgate']['server']."?user=".$this->config['smsgate']['user']."&pass=".$this->config['smsgate']['password']."&smsid=".$gateid);
				if (empty($status))
					return "Ошибка cистемы отсылки сообщений.";   
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
				return "Ошибка при получении статуса СМС".$error;
		}else{
			return "Ошибка: Неверный формат идентификатора СМС!";
		}
	
	}
	
	public function GetSMSStatus(){
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
        $smsid = isset($this->request['SmsID']) ? $this->request['SmsID'] : '';
		return $this->GetStatus($smsid);
	}
	
	public function SendSMS2(){
		$smsid=$this->SendSMS();
		return $this->GetStatus($smsid);
	}
	
	
	public function RefreshSMSStatus(){
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
		$cnt=0;
		$esmeid="";
		$smsid="";
        $res = $this->db->query("SELECT esmeid,smsid
                                 FROM sms 
                                 WHERE  clientkeyid = '".$this->SessionData['clientkeyid']."' and (smsstatus is null or smsstatus='в очереди'  or smsstatus='сообщение отправлено оператору' or smsstatus='сообщение принято и будет отправлено оператору' or smsstatus='состояние сообщения не определено') order by sms.createtime desc  limit 0,50");
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
			return "Ошибка: недопустимый ключ СМС API!";  
		$balans="";
		
		$balans = file_get_contents($this->config['smsgate']['server']."?user=".$this->config['smsgate']['user']."&pass=".$this->config['smsgate']['password']."&action=balance");
		
		return $balans;
	}
	
	public function GetAllSmsList()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
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
			return "Ошибка: недопустимый ключ СМС API!";  
        $result = array();
        $whereclause = isset($this->request['WhereClause']) ? $this->request['WhereClause'] : '1=1';
        $res = $this->db->query("SELECT smsto,smstext,createtime,smsstatus,clientkey.domain
                                 FROM sms left join clientkey on sms.clientkeyid=clientkey.clientkeyid
                                 WHERE sms.clientkeyid = '".$this->SessionData['clientkeyid']."' AND " . $whereclause. " order by createtime desc LIMIT 0,1000"  );
        while ($row = $res->fetch_assoc()){
            $result[] = $row;
        }
        $res->close();
        return $result;
    }
	
  
  public function GetPhoneList()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
        $result = array();
      
      $res = $this->db->query("SELECT phone,clientkey.domain  FROM lockedphone left join clientkey on lockedphone.clientkey=clientkey.clientkeyid where lockedphone.clientkey  = '".$this->SessionData['clientkeyid']."' order by phone"  );
        while ($row = $res->fetch_assoc()){
            $result[] = $row;
        }
        $res->close();
        return $result;
    }
	
	
	public function DelPhone()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
        $result = array();
		$phone = isset($this->request['Phone']) ? $this->request['Phone'] : '';
		$q="delete  FROM lockedphone where phone='".$phone."' and clientkey  = '".$this->SessionData['clientkeyid']."'";
		$stmt = $this->db->prepare($q);
		$res = $stmt->execute();
		$error = $stmt->error."\r\n 	"; //. implode("\r\n 	",$stmt->error_list);
		$stmt->close();
        return 'OK';
    }
	
	public function AddPhone()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
        $result = array();
		$phone = isset($this->request['Phone']) ? $this->request['Phone'] : '';
		$q="insert into lockedphone( phone,clientkey) values('".$phone."','".$this->SessionData['clientkeyid']."')";
		$stmt = $this->db->prepare($q);
		$res = $stmt->execute();
		$error = $stmt->error."\r\n 	"; //. implode("\r\n 	",$stmt->error_list);
		$stmt->close();
        return 'OK';
    }
	
	public function DelIP()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
        $result = array();
		$ip = isset($this->request['IP']) ? $this->request['IP'] : '';
		$q="delete  FROM lockedip  where ip='".$ip."' and clientkey  = '".$this->SessionData['clientkeyid']."'";
		$stmt = $this->db->prepare($q);
		$res = $stmt->execute();
		$error = $stmt->error."\r\n 	"; //. implode("\r\n 	",$stmt->error_list);
		$stmt->close();
        return 'OK';
    }
	
	public function AddIP()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
        $result = array();
		$ip = isset($this->request['IP']) ? $this->request['IP'] : '';
		$q="insert into lockedip( ip,clientkey) values('".$ip."','".$this->SessionData['clientkeyid']."')";
		$stmt = $this->db->prepare($q);
		$res = $stmt->execute();
		$error = $stmt->error."\r\n 	"; //. implode("\r\n 	",$stmt->error_list);
		$stmt->close();
        return 'OK';
    }


	
	public function GetIpList()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
        $result = array();
        $res = $this->db->query("SELECT ip,clientkey.domain  FROM lockedip left join clientkey on lockedip.clientkey=clientkey.clientkeyid where lockedip.clientkey  = '".$this->SessionData['clientkeyid']."' order by ip"  );
        while ($row = $res->fetch_assoc()){
            $result[] = $row;
        }
        $res->close();
        return $result;
    }
  
  
  
    public function GetConfig()
    {
		if (empty($this->SessionData['clientkeyid']))	
			return "Ошибка: недопустимый ключ СМС API!";  
        $result = array();
        
        $res = $this->db->query("SELECT *  FROM clientconfig where clientkey  = '".$this->SessionData['clientkeyid']."'"  );
        while ($row = $res->fetch_assoc()){
            $result[] = $row;
        }
        $res->close();
        return $result;
    }
	
   


}
?>
