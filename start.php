<?php

include_once('global.php');
/* 	CONNEXION	A	LA	BASE	DE	DONNES	DE	PRODUCTION	 */
$db_production_params = array('host' => $general['db']['host'], 'user' => $general['db']['user'], 'password' => $general['db']['password'], 'database' => $general['db']['database']);
$db_production = new DB($db_production_params);
if ($db_production->test_connexion()) {
    /* 	SELECTION	DU	CONTENU	 */
    $syntaxe = "SELECT * FROM pushmessage WHERE statut = 'EN-ATTENTE' AND '" . date("Y-m-d H:i:s") . "' BETWEEN datedebut AND datefin";
    $requete = $db_production->db_query($syntaxe);
    while ($resultat = $db_production->db_fetch_array($requete)) {
        $id_pushmessage = $resultat['id_pushmessage'];
        $service = $resultat['service'];
        $url = $general['url']['push'];
        $data = array("id" => $id_pushmessage, "service" => $service);
        $params = http_build_query($data);
        $url .= "?" . $params;
        $commande = "nohup curl -i '" . $url . "' 2> /dev/null &";
        exec($commande);
        echo($url . "\n");
    }
    $db_production->db_close();
}
?>