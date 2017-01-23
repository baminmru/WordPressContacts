<?php
$time_start = microtime(true);
include_once('config.php');
include_once('SMSServiceRU.php');

$request = json_decode(file_get_contents("php://input"), true);
if ($config['log']==true)	file_put_contents($config['logpath'].'/_debug.txt', '>>>: '.json_encode($request)."\r\n", FILE_APPEND);
try {
    $app = new SMSService($config, $request);
} catch( Exception $e ) {
if ($config['log']==true)   file_put_contents($config['logpath'].'/_debug.txt', 'Err: '.$e->getMessage()."\r\n", FILE_APPEND);
    header('Content-Type: application/json; charset=utf-8');
	echo json_encode(array('error' => $e->getMessage()));
    exit;
}
header('Content-Type: application/json; charset=utf-8');
try {
	if ($app->action!=''){
		$res = json_encode(call_user_func(array($app, $app->action)));
		$time = (microtime(true) - $time_start)*1000;

		if ($config['log']==true)    file_put_contents($config['logpath'].'/_debug.txt', '<<<('.round($time).'ms): '.substr($res, 0, 10000)."\r\n", FILE_APPEND);
		echo $res;
	}else{
		echo json_encode(array('error' => _('Unknown action!') ));
	}
} catch( Exception $e ) {
	if ($config['log']==true)    file_put_contents($config['logpath'].'/_debug.txt', 'Err: '.$e->getMessage()."\r\n", FILE_APPEND);
   
   echo json_encode(array('error' => $e->getMessage()));
}

