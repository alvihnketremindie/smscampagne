<?php
$timestart=microtime(true);
include_once('global.php');
$db_production_params = array('host' => $general['mysqlhost'],'user' => $general['mysqluser'],'password' => $general['mysqlpassword'],'database' => $general['mysqldatabase']);
$db_production = new DB($db_production_params);
if($db_production->test_connexion()){
	// foreach ($serviceIniFile as $serviceNAME => $serviceValues){
	if (isset($_REQUEST['service']) and !empty($_REQUEST['service'] )){
		$serviceNAME =  $_REQUEST['service'];
		$service =  $serviceIniFile[$serviceNAME];
		if(isset($service) && !empty($service)){
			/*	Insertion dans la base de travail	*/
			// $travail = $serviceNAME;
			$travail = $service['broadcast'];
			$significant = $general['significant'];
			// creerTable ($db_production,$travail);
			// copier($db_production,$service['broadcast'],$travail,$significant);
			/*	Retrait de la blacklist	*/
			// retraitBlacklist($db_production,$travail,$significant);
			$db_service_params = array('host' => $service['mysqlhost'],'user' => $service['mysqluser'],'password' => $service['mysqlpassword'],'database' => $service['mysqldatabase']);
			$db_service = new DB($db_service_params);
			if($db_service->test_connexion()){
				/* REQUTETE DE SELECTION DES INSCRITS */
				$syntaxeBlacklist = str_ireplace("{significant}", abs(intval($general['significant'])), $general['reqBlacklist']);
				$tableauBlacklist = tableauBase($db_production, $syntaxeBlacklist);
				/* REQUTETE DE SELECTION DES INSCRITS */
				$syntaxeInscrit = str_ireplace("{significant}", abs(intval($general['significant'])), $service['reqInscrit']);
				$tableauInscrit = tableauBase($db_service, $syntaxeInscrit);
				/* REQUTETE DE SELECTION DES DESINSCRITS */
				$syntaxeDesinscrit = str_ireplace("{significant}", abs(intval($general['significant'])), $service['reqDesinscrit']);
				$tableauDesinscrit = tableauBase($db_service, $syntaxeDesinscrit);
				/*	BASE INSCRITS	*/
				$baseInscrit = array_unique(array_filter(array_merge((array)$tableauBlacklist,(array)$tableauInscrit,(array)$tableauDesinscrit)));
				$db_service->db_close();
				foreach ($baseInscrit as $telephone){
					retraitTelephone($db_production,$travail,$significant,$telephone);
				}
			}
		}
	}
$db_production->db_close();
}

// if(!in_array($table,array('blacklist','broadcast','echec'))){}
/*	TEMPS	D'EXECUTION	DU	SCRIPT	*/
$timeend=microtime(true);
$time=$timeend-$timestart;
$page_load_time=number_format($time, 3);
$script = " [import] "."debut=".date("H:i:s", $timestart)."|fin=".date("H:i:s", $timeend)."|temps=".$page_load_time . " sec|commentaire=".$commentaire;
$log->cdr("temps", $script);


function retraitTelephone($db,$table,$significant,$telephone)
{
	$syntaxe = "UPDATE ".$table." SET statut = 'NO' WHERE RIGHT(`telephone`,".abs(intval($significant)).") = '".$telephone."'";
	$db->db_query($syntaxe);
}

function retraitBlacklist($db,$table,$significant)
{
	$syntaxe = "UPDATE ".$table." SET statut = 'NO' WHERE RIGHT(TRIM(`telephone`),".abs(intval($significant)).") IN (SELECT RIGHT(TRIM(`telephone`),".abs(intval($significant)).") FROM blacklist)";
	$db->db_query($syntaxe);
}
function copier($db,$table1,$table2,$significant)
{
	$syntaxe2 = "INSERT IGNORE INTO `".$table2."`(`telephone`) SELECT RIGHT(TRIM(`telephone`),".abs(intval($significant)).") FROM `".$table1."`";
	$db->db_query($syntaxe2);
}

function sauvegarder($db,$table,$significant)
{
	creerTable ($db,$table."Save");
	$syntaxe = "INSERT IGNORE INTO `".$table."Save"."`(`telephone`) SELECT RIGHT(TRIM(`telephone`),".abs(intval($significant)).") FROM `".$table."`";
	$db->db_query($syntaxe);
}

function creerTable ($db,$table)
{
	$reqCreaTable = "CREATE TABLE IF NOT EXISTS `$table` (
`Id` int(11) unsigned NOT NULL AUTO_INCREMENT,`telephone` varchar(20) NOT NULL,`statut` enum('YES','NO') NOT NULL DEFAULT 'YES',
PRIMARY KEY (`Id`),UNIQUE KEY `telephone` (`telephone`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
	$db->db_query($reqCreaTable);
}
?>