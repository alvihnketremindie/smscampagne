<?php
$timestart=microtime(true);
$nombre = 0;
include_once('global.php');
if(isset($_REQUEST['base']) && !empty($_REQUEST['base']))
{
	$nom_fichier_base = $_REQUEST['base'];
	$db_production_params = array('host' => $general['mysqlhost'],'user' => $general['mysqluser'],'password' => $general['mysqlpassword'],'database' => $general['mysqldatabase']);
	$db_production = new DB($db_production_params);
	$chemin_fichier = ROOT."/Bases/".$nom_fichier_base;
	$fichier = @file_get_contents($chemin_fichier);
	$commentaire = "Probleme avec la base de donnee ou le fichier";
	if(isset($fichier) && !empty($fichier)){
		$base = formater ($fichier,$general['significant']);
		if($db_production->test_connexion()){
			/*	Insertion dans la blacklist */
			if(preg_match("/blacklist|dnd/i", $nom_fichier_base)){
				$table = 'blacklist';
				$nombre = inserer($db_production,$table,$base);
			}
			else{
				if(isset($_REQUEST['service']) and !empty($_REQUEST['service'])){
					/*
					$serviceTAB = explode("-",$nom_fichier_base);
					#$serviceTAB = preg_split("/_/i",$nom_fichier_base);
					$serviceNAME = $serviceTAB[0];
					*/
					$serviceNAME = $_REQUEST['service'];
					$service =  $serviceIniFile[$serviceNAME];
					if(isset($service) && !empty($service)){
						$table = $service['broadcast'];
						if(isset($_REQUEST['action']) && !empty($_REQUEST['action']) && preg_match("/remplace|replace|changer|nouvelle/i", $_REQUEST['action']) && $table != 'broadcast'){
							sauvegarder($db_production,$table,$general['significant']);
							vider ($db_production,$table);
						}
					}
					else $table = "broadcast";
				}
				else $table = "broadcast";
				// insererByLOad($db_production,$table,$chemin_fichier,$general['significant']);
				$nombre = inserer($db_production,$table,$base);
			}
			/*	Insertion	des	abonnes	*/
			$commentaire = "Importation correcte dans la table ".$table;
		}
	}
	else
	{
		$commentaire = "Le fichier ".$chemin_fichier." n'a pas ete trouve";
	}
	$db_production->db_close();
}
else{
	$commentaire = "Nom de la base NON fourni";
	$table = "echec";
}
$info = "[$table]|fichier=".$chemin_fichier."|nombre=".$nombre."|commentaire=".$commentaire;
$log->cdr("import", $info);
// if(!in_array($table,array('blacklist','broadcast','echec'))){}
/*	TEMPS	D'EXECUTION	DU	SCRIPT	*/
$timeend=microtime(true);
$time=$timeend-$timestart;
$page_load_time=number_format($time, 3);
$script = " [import] "."debut=".date("H:i:s", $timestart)."|fin=".date("H:i:s", $timeend)."|temps=".$page_load_time . " sec|commentaire=".$commentaire;
$log->cdr("temps", $script);

function insererByLOad($db,$table,$fichier,$significant)
{
	creerTable ($db,$table);
	creerTable ($db,$table."tmp");
	$syntaxe = "LOAD DATA LOCAL INFILE '".$fichier."' IGNORE INTO TABLE ".$table."tmp"." FIELDS TERMINATED BY '\\n' LINES TERMINATED BY '\\r\\n' (telephone)";
	$db->db_query($syntaxe);
	copier($db, $table."tmp",$table,$significant);
	sauvegarder($db,$table,$significant);
	vider ($db,$table."tmp");
}

function insererBlacklist($db,$fichier,$significant)
{
	creerTable ($db,"blacklist");
	creerTable ($db,"blacklisttmp");
	$syntaxe = "LOAD DATA LOCAL INFILE '".$fichier."' IGNORE INTO TABLE blacklisttmp LINES TERMINATED BY '\n' (telephone)";
	$db->db_query($syntaxe);
	copier($db, "blacklisttmp","blacklist",$significant);
	vider ($db,"blacklisttmp");
	
}
function copier($db,$table1,$table2,$significant)
{
	$syntaxe2 = "INSERT IGNORE INTO `".$table2."`(`telephone`) SELECT RIGHT(TRIM(`telephone`),".abs(intval($significant)).") FROM `".$table1."`";
	$db->db_query($syntaxe2);
}

function sauvegarder($db,$table,$significant)
{
	$syntaxe = "INSERT IGNORE INTO `broadcast`(`telephone`) SELECT RIGHT(TRIM(`telephone`),".abs(intval($significant)).") FROM `".$table."`";
	$db->db_query($syntaxe);
}

function inserer($db,$table,$tableau)
{
	creerTable ($db,$table);
	$id = 0;
	foreach ($tableau as $value)
	{
		$insertPhone = "INSERT IGNORE INTO `$table`(`telephone`) VALUES ('$value')";
		$db->db_query($insertPhone);
		$id++;
	}
	return $id;
}

function vider ($db,$table)
{
	$reqVidage = "TRUNCATE `$table`";
	$db->db_query($reqVidage);
}

function formater ($contents,$significant)
{
	$base = array();
	if(isset($contents) && !empty($contents))
	{
		$tableau = preg_split("/([[:punct:]]|[[:space:]])/i",$contents."\n");
		foreach ($tableau as $value)
		{
			$value = preg_replace("/[^0-9]/","",$value);
			$value = substr($value, $significant);
			if(strlen($value) == abs(intval($significant)))
			{
				$base[] = $value;
			}
		}
	}
	return array_unique(array_filter($base));
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