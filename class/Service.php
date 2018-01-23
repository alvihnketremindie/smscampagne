<?php

class Service {

    protected $iniConfig;

    public function __construct($servicename) {
        global $serviceIniFile;
        $this->iniConfig = null;
        if (isset($serviceIniFile) and ! empty($serviceIniFile)) {
            if (isset($servicename) and ! empty($servicename)) {
                $iniconfig = $serviceIniFile[$servicename];
                $this->iniConfig = $iniconfig;
            }
        }
    }

    public function getDBparams() {
        $db_service_params = array('host' => $this->getParams('host'), 'user' => $this->getParams('user'), 'password' => $this->getParams('password'), 'database' => $this->getParams('database'));
        return new DB($db_service_params);
    }

    public function getNumbers( $significant) {
        global $general;
        $resultat = array();
        if ($this->iniConfig) {
            $requete = $this->getParams('reqPresent');
            // $significant = intval($general['significant']);
            $db = $this->getDBparams();
            $query_id = $db->db_query($requete);
            if ($query_id) {
                while ($row = $db->db_fetch_array($query_id)) {
                    $resultat[] = substr($row['telephone'], $significant);
                }
            }
        }
        return array_unique(array_filter($resultat));
    }

    public function getParams($params) {
        return $this->iniConfig[$params];
    }

}

?>