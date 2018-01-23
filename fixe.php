<?php
$timestart=microtime(true);
include_once('global.php');
/*	CONNEXION	A	LA	BASE	DE	DONNES	DE	PRODUCTION	*/
$db_production_params = array('host' => $general['mysqlhost'],'user' => $general['mysqluser'],'password' => $general['mysqlpassword'],'database' => $general['mysqldatabase']);
$db_production = new DB($db_production_params);
if($db_production->test_connexion()){
	/*	SELECTION	DU	CONTENU	*/
	if(isset($_REQUEST['service']) and !empty($_REQUEST['service'])){
		$service = $_REQUEST['service'];
		$type = $_REQUEST['type'];
		$syntaxe = "SELECT * FROM `pushMessage` WHERE `statut` = 'EN-ATTENTE' AND `type` = '".$type."' AND `service` = '".$service."' AND NOW() BETWEEN `dateDebut` AND `dateFin` LIMIT 1";
		$requete = $db_production->db_query($syntaxe);
		if($resultat = $db_production->db_fetch_array($requete)){
			$id_pushmessage = $resultat['id_pushmessage'];
			$libelle = utf8_decode($resultat['libelle']);
			$serviceNAME = $resultat['service'];
			$type = $resultat['type'];
			$dernier_id = $resultat['dernier_id'];
			/*	CONNEXION	A	LA	BASE	DE	DONNES	DU	SERVICE	*/
			$service =  $serviceIniFile[$serviceNAME];
			$db_service_params = array('host' => $service['mysqlhost'],'user' => $service['mysqluser'],'password' => $service['mysqlpassword'],'database' => $service['mysqldatabase']);
			$db_service = new DB($db_service_params);
			if($db_service->test_connexion()){
				/*	PRISE	DU	CONTENU	*/
				$updateOnGoing = "UPDATE `pushMessage` SET `statut` = 'EN-COURS' WHERE `id_pushmessage` = ".$id_pushmessage." AND `statut` = 'EN-ATTENTE'";
				$db_production->db_query($updateOnGoing);
				$push = new PUSH($service['sender'],$libelle, $type);
				/*	REQUTETE	DE	SELECTION	DES	ABONNES	*/
				$tableauBroadcast = preg_split("/([[:punct:]]|[[:space:]])/i",$general['baseTest']);
				$baseAbonne = array_unique(array_filter($tableauBroadcast));
				$db_service->db_close();
				/*	BULK	SMS	*/
				if ((isset($libelle) and !empty($libelle)) and (isset($baseAbonne) and !empty($baseAbonne))){
					$id = 0;
					foreach ($baseAbonne as $telephone){
						$push->send($telephone,$general,$serviceNAME);
						$id++;
					}
				}
				$push->fileLog();
				$info = "Parcous semblant correct";
			}
			else{
				$info = "Probleme de connexion a la base de donnees du service ".$serviceNAME;
			}
		}
	}
	else{
		$info = "Pas de message disponible pour le service moment";
	}
	$db_production->db_close();
}
else{
	$info = "Probleme de connexion a la base de donnees de production";
}
/*	TEMPS	D'EXECUTION	DU	SCRIPT	*/
$timeend=microtime(true);
$time=$timeend-$timestart;
$page_load_time=number_format($time, 3); 
$script = "[push]|debut=".date("H:i:s", $timestart)."|fin=".date("H:i:s", $timeend)."|temps=".$page_load_time . " sec|commentaire=".$info;
$log->cdr("temps", $script);
?>