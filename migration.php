<?php
/**
 * Created by PhpStorm.
 * User: YGC
 * Date: 05/05/17
 * Time: 19:31
 */
include('autoload.php');
include('config.inc.php');

$init2['DATABASE']['host']     = "localhost";
$init2['DATABASE']['user']     = "root";
$init2['DATABASE']['dbConnection']=false;
$init2['DATABASE']['password'] = "timezone!=fanny";
$init2['DATABASE']['database'] = "jeuneafrique_stk";

$init['DATABASE']['database'] = "presse_moov_togo";
$rfi_airtel_bf = new JEUNEAFRIQUE($init['DATABASE'], $_REQUEST, $init);
/*
$rfi=new JEUNEAFRIQUE($init2['DATABASE'], $_REQUEST, $init2);
$rs=$rfi->fetchAll("select telephone,level,subtype from subscription where active!='NO' and level in ('jaeco','japo') ");

foreach($rs as $ligne){
    if($ligne['subtype']=="WEEK" or $ligne['subtype']=="DAY"){
        $sous="HEBDO";
    }else $sous="MOIS";
    //if($ligne['code_service']==1)$level="jaeco";else $level="japo";
    $ls=$rfi_airtel_bf->fetchAll("select * from presse_moov_togo.abonnes where numero='".$ligne['telephone']."' and code_service=1");
    if(count($ls)>0){
        $rfi_airtel_bf->execQuery("update abonnes set statut='YES' and date_limite='2017-07-13' where numero='".$ligne['telephone']."' and code_service =1 ");
        echo "update abonnes set statut='YES' and date_limite='2017-07-13' where numero='".$ligne['telephone']."' and code_service =1  \n";
    }else{
        $rfi_airtel_bf->execQuery("insert into abonnes set numero='".$ligne['telephone']."' ,code_service='1',statut='YES',souscription='$sous',renew='NO',relance='$sous',date_abonn=now(),date_limite='2017-07-13' ");
        echo "insert into abonnes set numero='".$ligne['telephone']."' ,code_service='1',statut='YES',souscription='$sous',renew='NO',relance='$sous',date_abonn=now(),date_limite='2017-07-13'  \n";
    }
    $rfi->execQuery("update subscription set active='NO',number=100 where telephone='".$ligne['telephone']."' and level='".$ligne['level']."'");
    echo "update subscription set active='NO',number=100 where telephone='".$ligne['telephone']."' and level='".$ligne['level']."' \n";
}
$rs=$rfi->fetchAll("select * from billinglist_new where status='YES' and code_service in (22,180)");
foreach($rs as $ligne){
    $rfi_airtel_bf->execQuery("insert into billinglist (date,telephone,amount,code_service,type,rubrique,plateforme,status,response_code) values ('".$ligne['date']."','".$ligne['telephone']."','".$ligne['amount']."','1','".$ligne['type']."','".$ligne['rubrique']."','".$ligne['plateforme']."','".$ligne['status']."','".$ligne['response_code']."')");
    echo "insert into billinglist (date,telephone,amount,code_service,type,rubrique,plateforme,status,response_code) values ('".$ligne['date']."','".$ligne['telephone']."','".$ligne['amount']."','1','".$ligne['type']."','".$ligne['rubrique']."','".$ligne['plateforme']."','".$ligne['status']."','".$ligne['response_code']."')\n";
}
$rfi->execQuery("update services set statut='no' where code_service in (22,180)");
$rfi->execQuery("update groupements set statut='no' where id_groupement in (132,133,29)");
*/


$rs=$rfi->fetchAll("select * from billinglist_new where status='YES' and code_service");
foreach($rs as $ligne){
    $rfi_airtel_bf->execQuery("insert into billinglist (date,telephone,amount,code_service,type,rubrique,plateforme,status,response_code) values ('".$ligne['date']."','".$ligne['telephone']."','".$ligne['amount']."','1','".$ligne['type']."','".$ligne['rubrique']."','".$ligne['plateforme']."','".$ligne['status']."','old')");
    echo "insert into billinglist (date,telephone,amount,code_service,type,rubrique,plateforme,status,response_code) values ('".$ligne['date']."','".$ligne['telephone']."','".$ligne['amount']."','1','".$ligne['type']."','".$ligne['rubrique']."','".$ligne['plateforme']."','".$ligne['status']."','old')\n";
}