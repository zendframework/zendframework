<?php
class My_ServiceA {
    function __construct() {
        //Construction...
    }

    /**
     * @return string
     */
    public function getMenu( )
    {
        return 'Service: myMenuA';
    }
}