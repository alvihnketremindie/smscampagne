<?php

header('Content-type:text/plain;charset=ISO-8859-1');
set_time_limit(0);
ini_set('realpath_cache_ttl', '120');
ini_set('realpath_cache_size', '50M');
ini_set('memory_limit', '-1');
#ini_set('mysql.connect_timeout', 3000);
#ini_set('default_socket_timeout', 3000);

define('ROOT', dirname(__FILE__));
define('INI_PATH', ROOT.'/ini');
define('GENERAL_INI', INI_PATH . '/general.ini');
define('OPERATEUR_INI', INI_PATH . '/operateur.ini');
define('INI_FILE', INI_PATH . '/service.ini');
define('LOG_PATH', ROOT.'/log');
define('BASEABO_PATH', ROOT.'/base');

$general = parse_ini_file(GENERAL_INI, true);
$operateurIni = parse_ini_file(OPERATEUR_INI, true);
$serviceIniFile = parse_ini_file(INI_FILE, true);
$log = new LOG(LOG_PATH);

date_default_timezone_set($general['info']['fuseau']);

function __autoload($class_name) {
    require_once "class/" . $class_name . ".php";
}

function debug($info) {
    $debug = FALSE;
    #$debug = TRUE;
    if ($debug) {
        print $info . "\r\n";
    }
}

?>