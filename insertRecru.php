<?php

/*
  $libelle = utf8_decode($resultat['libelle']);
  $service = $resultat['service'];
  $type = $resultat['type'];
  $base = $resultat['base'];

  $requete = str_ireplace("{broadcast}", $base, $general['reqIdLimit']);
  $query_id = $db->db_query($requete);
  if ($query_id) {
  $row = $db->db_fetch_array($query_id);
  $inf = $row['min_id'];
  $sup = $row['max_id'];
  $diff = $sup - $inf;
  $process = $general['process'];
  $pas = floor($diff / $process);
  $array = array();
  $value1 = $inf;
  $value2 = $inf + $pas;
  if ($pas > 0) {
  while ($value2 < $sup) {
  $array [] = array($value1, $value2);
  $value1 = $value2 + 1;
  $value2 = $value1 + $pas;
  }
  $array [] = array($value1, $sup);
  } else {
  $array [] = array($inf, $sup);
  }
  foreach ($array as $couple) {
  $url = $general['pushurl'];
  $data = array("id" => $id_pushmessage,
  "sup" => $cuple[0],
  "inf" => $couple[1]);
  $params = http_build_query($data);
  $url .= "?".$params;
  $commande = "nohup curl -i '" . $url . "' 2> /dev/null &";
  #exec($commande);
  echo($url."\n");
  }

  }
 */
include_once('global.php');
$longueur = strlen($general['prefix']) + abs(intval($general['significant']));
$fichier = ROOT . "/Fichier/" . $_REQUEST['file'];

$tableau = array_unique(file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
if (!$db_production->test_connexion()) {
    $commentaire = "Probleme avec la base de donnes";
} elseif (!(isset($tableau) and ! empty($tableau))) {
    $commentaire = "Probleme avec le fichier";
} else {
    /* 	Selection	des	messages	 */
    for ($id = 0; $id < count($tableau); $id++) {
        $messageParams = explode(";", $tableau[$id]);
        $dateDebut = $messageParams[0];
        $dateFin = $messageParams[1];
        $service = $messageParams[2];
        $libelle = $messageParams[3];

        $insertRequest = "INSERT IGNORE INTO `pushMessage` (`dateDebut`, `dateFin`, `service`, `libelle`) VALUES ('" . $dateDebut . "', '" . $dateFin . "', '" . $service . "', '" . $libelle . "')";
        $db_production->db_query($insertRequest);
        debug($id . " -- " . serialize($messageParams));
    }
}
$commentaire = "Importation correcte";
$db_production->db_close();

$info = "date=" . @date('Y-m-d H:i:s') . "\t" . "fichier=" . $fichier . "\t" . "nombre=" . $i . "\t" . "type=recrutement" . "\t" . "commentaire=" . $commentaire;
// debug($info);
$log->cdr("import", $info);
?>
