<?php
class ServiceA {
    function __construct() {        
        //Construction...
    }
    
    /**
     * @return string
     */
    public function getMenu()
    {
        return 'myMenuA';
    }
}