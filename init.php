<?php

$timestart = microtime(true);
include_once('global.php');
$link = mysqli_connect($general['mysqlhost'], $general['mysqluser'], $general['mysqlpassword']);
if ($link) {
    $query = "CREATE DATABASE IF NOT EXISTS " . $general['mysqldatabase'];
    if (mysqli_query($link, $query)) {
        $commentaire = "Success";
        $commande = "mysql -h " . $general['mysqlhost'] . " -u " . $general['mysqluser'] . " -p" . $general['mysqlpassword'] . " " . $general['mysqldatabase'] . " < " . ROOT . '/' . $general['mysqldatabase'] . ".sql";
        exec($commande);
    } else {
        $commentaire = "Failure";
    }
    /* $commande = "mkdir ".LOG_PATH."/sms_envoi";
      exec($commande);
      $commande = "mkdir ".LOG_PATH."/push";
      exec($commande);
      $commande = "mkdir ".LOG_PATH."/temps";
      exec($commande);
      $commande = "mkdir ".LOG_PATH."/import";
      exec($commande);
     * *
     */
    $commande = "chmod -R 777 " . LOG_PATH;
    exec($commande);
} else {
    $commentaire = "Connexion impossible|Code=" . mysqli_connect_error() . "|Message=" . mysqli_connect_errno();
}
/*      TEMPS   D'EXECUTION     DU      SCRIPT  */
$timeend = microtime(true);
$time = $timeend - $timestart;
$page_load_time = number_format($time, 3);
$script = "[init]|debut=" . date("H:i:s", $timestart) . "|fin=" . date("H:i:s", $timeend) . "|temps=" . $page_load_time . " sec|commentaire=" . $commentaire;
$log->cdr("temps", $script);
?>