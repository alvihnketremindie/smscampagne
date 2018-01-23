<?php

class Utils {

    var $request;
    var $id;
    var $sup;
    var $inf;

    public function __construct($request) {
        $this->request = $request;
        $this->id = (isset($this->request['id']) ? $this->request['id'] : 0);
        $this->service = $this->request['service'];
        #$this->inf = $this->request['inf'];
        #$this->sup = $this->request['sup'];
        #print_r($request);
    }

    public function getId() {
        return $this->id;
    }
    
	public function getService() {
        return $this->service;
    }
    
    public function getInf() {
        return $this->inf;
    }
    
    public function getSup() {
        return $this->sup;
    }
}

?>