<?php

$timestart = microtime(true);
$script['debut'] = date("H:i:s", $timestart);
include_once('global.php');
$utils = new Utils($_REQUEST);
$service = $utils->getService();
$id_pushmessage = $utils->getType();
if (isset($service) and ! empty($service)) {
    if ($type == 'RECRUTEMENT') {
        $production = new PRODUCTION($general, $serviceIniFile, $service);
        $db_production = $production->getDBparams($general);
        if ($db_production->test_connexion()) {
            $pushparams = $production->getPushParams($type,$service);
            $baseAbonne = $production->getBroadcast($pushparams['dernierid']);
            $push = new PUSH($production->getParams($serviceIniFile[$service], 'sender'), $pushparams['push'], $type, $baseAbonne);
            if (isset($push->getMessage()) and ! empty($push->getMessage())) {
                if (isset($push->getBase()) and ! empty($push->getBase())) {
                    foreach ($push->getBase() as $telephone) {
                        $push->send($telephone, $production->getParams($serviceIniFile[$service], 'smsurl'), $service);
                    }
                }
                $production->setPushParams($pushparams['id_pushmessage'], count($push->getBase()));
            }
            $db_production->db_close();
        }
    }
}
/* 	TEMPS	D'EXECUTION	DU	SCRIPT	 */
$timeend = microtime(true);
$script['fin'] = date("H:i:s", $timeend);
$time = $timeend - $timestart;
$page_load_time = number_format($time, 3);
$script['temps'] = $page_load_time = number_format($time, 3);
#$script = "[push] debut=" . date("H:i:s", $timestart) . "|fin=" . date("H:i:s", $timeend) . "|temps=" . $page_load_time . " sec|commentaire=" . $info;


$log->cdr("temps", $script);
?>

