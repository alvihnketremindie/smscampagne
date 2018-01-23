<?php
require_once "global.php";
/*	conf	*/
$type = strtolower($_REQUEST['type']);
$operateur = $_REQUEST['operateur'];
$prefix = $operateurIni[$operateur]['prefixsms'];
$significant = $operateurIni[$operateur]['significant'];

$urlParams = $operateurIni[$operateur]['url'];
$urlArray = explode("|",$urlParams);
$i = rand(0, count($urlArray)-1);
$url = $urlArray[$i];

/*	params number	*/
$sender = $_REQUEST['sender'];
$receiver = $prefix.substr($_REQUEST['receiver'], $significant);
$text = $_REQUEST['text'];
$url .= "&from=".urlencode($sender)."&to=".urlencode($receiver)."&text=".urlencode($text);
if($type == "flash"){
	$url .= "mclass=0&alt-dcs=1";
}

/* log	params	*/
/*
$sms['from']  = $sender;
$sms['to']  = $receiver;
$sms['text']  = $text;

$sms['smsc'] = $_REQUEST['smsc'];
$info = $sms;
$info['service'] = $_REQUEST['service'];
$info['type'] = $_REQUEST['type'];
$info['dlr'] = $_REQUEST['dlr'];

$dlr_url = $general['dlrurl'];
*/


$transactionid = $type.":".$receiver.":".@date('YmdHis');
$url .= "&transactionid=".$transactionid;
if($_REQUEST['dlr'] == "yes")
{
	$dlr_url = $general['url']['dlr'];
	$url .= "&dlr-mask=31&dlr-url=".urlencode($dlr_url."?telephone=%p&sender=%P&date=%t&smscid=%i&status=%d&reponse=%A&message=%b&temps=%t&user=%n");
}

echo $url."\n";

/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$reply = trim(curl_exec($ch));
curl_close ($ch);
/*
$message = preg_replace("#<br>|\n|\r|\t|<br />|{CR}#i",". .",$text);
$info =  "service=$service|sender=$sender|telephone=$telephone|message=$message|smsc=".$kannel[1]."|transactionid=".$transactionid."|resp=".$reply;
$status = explode(":",$reply);

$log->cdr("sms-".$status[0], $info);
*/
?>