<?php

class PUSH {

    protected $id_push;
    protected $Message;
    protected $idcourant;
    protected $iddebut;
    protected $idfin;
    protected $basetype;
    protected $smstype;
    protected $service;
    protected $tablebroadcast;
    protected $base;
    protected $status;
    protected $production;

    public function __construct($id, $production) {
        $this->status = "en-cours";
        $this->production = $production;
        $syntaxe = "SELECT * FROM pushmessage WHERE statut = 'EN-ATTENTE' AND id_pushmessage = " . $id . " LIMIT 1";
        $this->getPushParams($syntaxe);
    }

    public function getSender() {
        return $this->sender;
    }

    public function getMessage() {
        return $this->Message;
    }

    public function getOperateur() {
        return $this->operateur;
    }
	
	public function getBaseType() {
        return $this->basetype;
    }

    public function getSmstype() {
        return $this->smstype;
    }

    public function setBaseDB($vague) {
        $this->base = $this->production->getBroadcast($this->numbers, $this->idcourant, $this->iddebut, $this->idfin, $vague);
    }

    public function setBaseFile() {
         $this->base = $this->production->getBaseFromFile($this->numbers);
    }

    public function getBase() {
        return $this->base;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setMessage($Message) {
        $this->Message = $Message;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getPushParams($syntaxe) {
        $db = $this->production->getDbParams();
        $requete = $db->db_query($syntaxe);
        $resultat = $db->db_fetch_array($requete);
        if ($resultat) {
            $this->id_push = $resultat['id_pushmessage'];
            $this->Message = utf8_decode($resultat['libelle']);
            $this->idcourant = $resultat['idcourant'];
            $this->iddebut = intval($resultat['iddebut']);
            $this->idfin = intval($resultat['idfin']);
            $this->sender = $resultat['sender'];
            $this->service = $resultat['service'];
            $this->smstype = $resultat['smstype'];
            $this->operateur = $resultat['operateur'];
            $this->basetype = $resultat['basetype'];
            $this->numbers = $resultat['numbers'];
            $updateQuery = "UPDATE pushmessage SET statut = 'EN-COURS' WHERE id_pushmessage = " . $this->id_push . " AND statut = 'EN-ATTENTE'";
            $db->db_query($updateQuery);
        }
        $db->db_close();
    }

    public function updatePushParams($quantite) {
        $db = $this->production->getDbParams();
		if ($quantite > 0){
			if(strtolower($this->basetype) == "table") {
				$updateQuery = "UPDATE pushmessage SET statut = 'EN-ATTENTE', idcourant = " . $this->production->getDernierid() . ", quantite = quantite  + " . $quantite . " WHERE id_pushmessage = " . $this->id_push . " AND (statut = 'EN-ATTENTE' OR statut = 'EN-COURS')";
				$this->status = "continue";
			} elseif(strtolower($this->basetype) == "fichier") {
				 $updateQuery = "UPDATE pushmessage SET statut = 'TERMINER', quantite = quantite  + " . $quantite . " WHERE id_pushmessage = " . $this->id_push . " AND (statut = 'EN-ATTENTE' OR statut = 'EN-COURS')";
				$this->status = "terminer";
			} else {
				$updateQuery = "UPDATE pushmessage SET statut = 'ECHEC' WHERE id_pushmessage = " . $this->id_push . " AND (statut = 'EN-ATTENTE' OR statut = 'EN-COURS')";
				$this->status = "echec";
			}
		} else {
			if(strtolower($this->basetype) == "table" and intval($this->production->getDernierid()) < intval($this->idfin)){
				$updateQuery = "UPDATE pushmessage SET statut = 'EN-ATTENTE', idcourant = " . $this->production->getDernierid() . ", quantite = quantite  + " . $quantite . " WHERE id_pushmessage = " . $this->id_push . " AND (statut = 'EN-ATTENTE' OR statut = 'EN-COURS')";
				$this->status = "continue no number";
			} else {
				$updateQuery = "UPDATE pushmessage SET statut = 'TERMINER' WHERE id_pushmessage = " . $this->id_push . " AND (statut = 'EN-ATTENTE' OR statut = 'EN-COURS')";
				$this->status = "terminer";
			}
        }
        $db->db_query($updateQuery);
        $db->db_close();
    }

    public function fileLog() {
        global $log;
        $text = str_replace(array('<br>', '<br />', "\n", "\r", "\t"), array('', '', '', '', ''), $this->getMessage());
        $logIngo['service'] = $this->service;
        $logIngo['operateur'] = $this->operateur;
        $logIngo['sender'] = $this->getSender();
        $logIngo['message'] = $text;
        $logIngo['nombre'] = count($this->getBase());
        $logIngo['statut'] = $this->getStatus();
        $log->cdr("push", $logIngo);
    }
	
    public function send($telephone) {
        $message = str_ireplace("{phone}", $telephone, $this->getMessage());
        $url = $this->production->getParams('url','sms');
        $smsparams = array(
            "sender" => $this->getSender(),
            "receiver" => $telephone,
            "text" => $message,
            "operateur" => $this->operateur,
            "type" => $this->smstype,
            "dlr" => "no");
        $url_params = http_build_query($smsparams);
        $url = $url . "?" . $url_params;
        $commande = "curl -i  '" . $url . "' 2> /dev/null &";
        echo($url . "\n");
        #exec($commande);
    }
}

?>