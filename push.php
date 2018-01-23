<?php
$timestart = microtime(true);
$script['debut'] = date("H:i:s", $timestart);
include_once('global.php');
$utils = new Utils($_REQUEST);
$id = $utils->getId();
if (isset($id) and ! empty($id)) {
    $production = new PRODUCTION($general);
    $db_production = $production->getDBparams();
    if ($db_production->test_connexion()) {
        $push = new PUSH($id, $production);
        $message = $push->getMessage();
		$significant = intval($operateurIni[$push->getOperateur()]['significant']);
		$production->setSignificant($significant);
        if (isset($message) and ! empty($message)) {
			if(strtoupper($push->getBaseType()) == "FICHIER"){
				$push->setBaseFile();
			}
			elseif(strtoupper($push->getBaseType()) == "TABLE"){
				$vague = intval($operateurIni[$push->getOperateur()]['vaguesms']);
				$push->setBaseDB($vague);
			}
			else exit("Type de base non reconnu");
            $baseabonne = $push->getBase();
            if (isset($baseabonne) and ! empty($baseabonne)) {
				$service = $utils->getService();
                if (isset($service) and ! empty($service)) {
                    $serviceobject = new Service($service);
                    $baseinscrit = $serviceobject->getNumbers($significant);
                    $baseabonne = $production->filtreBase($baseabonne, $baseinscrit);
                }
                foreach ($baseabonne as $telephone) {
                    $push->send($telephone,$push->getSmstype());
                }
            }
            $quantite = intval(count($baseabonne));
            $push->updatePushParams($quantite);
        }
        $db_production->db_close();
    }
}
$timeend = microtime(true);
$script['fin'] = date("H:i:s", $timeend);
$time = $timeend - $timestart;
$page_load_time = number_format($time, 3);
$script['temps'] = $page_load_time = number_format($time, 3);
$log->cdr("temps", $script);
?>