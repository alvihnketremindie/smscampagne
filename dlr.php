<?php
require_once "global.php";
$date = @$_REQUEST['date'];
$dlrid = @$_REQUEST['dlrid'];
$sender = @$_REQUEST['sender'];
$telephone = @$_REQUEST['telephone'];
$smscid = @$_REQUEST['smscid'];
$status = @$_REQUEST['status'];
$reponse = @$_REQUEST['reponse'];
$service = @$_REQUEST['service'];
$user = @$_REQUEST['user'];
$message = @$_REQUEST['message'];
$serviceArray = explode(":",$dlrid);
$service = @$serviceArray[0];
$info = "service=$service|dlrid=$dlrid|sender=$sender|telephone=$telephone|reponse=$reponse|status=$status|message=$message|user=$user|smscid=$smscid";
$log->cdr("dlr-$status", $info);
?>