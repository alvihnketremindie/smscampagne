<?php

class PRODUCTION {

    protected $iniConfig;
    protected $dernierid;
	protected $significant;

    public function __construct($general) {
        if (isset($general) and ! empty($general)) {
            $this->iniConfig = $general;
        } else {
            exit(utf8_decode("le fichier general.ini n'a pu être trouvé"));
        }
    }

    public function getDBparams() {
        $db_service_params = array('host' => $this->getParams('db','host'), 'user' => $this->getParams('db','user'), 'password' => $this->getParams('db','password'), 'database' => $this->getParams('db','database'));
        return new DB($db_service_params);
    }

    public function getBase($string) {
        $tableau = preg_split("/([[:punct:]]|[[:space:]])/i", $string);
        $significant = intval($this->significant);
        foreach ($tableau as $value) {
            $value = substr(preg_replace("/[^0-9]/", "", $value), $significant);
            if (strlen($value) == abs(intval($significant))) {
                $base[] = $value;
            }
        }
        return array_unique(array_filter($base));
    }

    public function getBaseFromFile($nomBase) {
        $chemin_fichier = BASEABO_PATH . "/" . $nomBase . ".txt";
        $fichier = @file_get_contents($chemin_fichier);
        $base = array();
        if (isset($fichier) && !empty($fichier)) {
            $base = $this->getBase($fichier);
        }
        return $base;
    }

    public function getBlacklist() {
        $requete = $this->getParams('requete','blacklist');
        return $this->getRequeteResultat($requete);
    }

    public function getBroadcast($tablebroadcast, $dernier_id, $id_debut, $id_fin, $vague) {
        $search = array("{broadcast}", "{dernier_id}", "{vague}", "{infId}", "{supId}");
        $replace = array($tablebroadcast, $dernier_id, $vague, $id_debut, $id_fin);
        $requete = str_ireplace($search, $replace, $this->getParams('requete','baseabonne'));
        $this->dernierid = $this->requeteDernierId($requete);
		$this->dernierid = (isset($this->dernierid) ? $this->dernierid : $id_fin);
		$baseabonne = $this->getRequeteResultat($requete);
        if (isset($baseabonne) && !empty($baseabonne)) {
            $baseblacklist = $this->getBlacklist();
            if (isset($baseblacklist) && !empty($baseblacklist)) {
                $baseabonne = $this->filtreBase($baseabonne, $baseblacklist);
            }
        }
        return $baseabonne;
    }
	
    public function getDernierid() {
        return $this->dernierid;
    }

    public function getParams($level,$params) {
		$element = $this->iniConfig[$level][$params];
        return $element;
    }

    public function getRequeteResultat($requete) {
        $db = $this->getDBparams();
        $significant = intval($this->significant);
        $query_id = $db->db_query($requete);
        $resultat = array();
        if ($query_id) {
            while ($row = $db->db_fetch_array($query_id)) {
                $telephone = substr($row['telephone'], $significant);
                if (strlen($telephone) == intval(abs($significant))) {
                    $resultat[] = $telephone;
                }
            }
        }
        $db->db_close();;
        return $resultat;
    }

    public function filtreBase($baseabonne, $basefiltre) {
        $filtreBase =  array_unique(array_filter(array_diff($baseabonne, $basefiltre)));
		return $filtreBase;
    }
    
    public function requeteDernierId($requete) {
        $db = $this->getDBparams();
        $requete = "SELECT MAX(Id) AS dernier_id FROM (".$requete.") AS tmp_broad LIMIT 1";
        $query_id = $db->db_query($requete);
        $resultat = null;
        if ($query_id) {
            if ($row = $db->db_fetch_array($query_id)) {
                $resultat = $row['dernier_id'];
            }
        }
        $db->db_close();
        return $resultat;
    }
	
	public function setSignificant($significant){
		$this->significant = $significant;
	}

}

?>
