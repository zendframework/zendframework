<?php

require_once "Zend/Soap/AutoDiscover.php";
require_once "Zend/Soap/Server.php";
require_once "Zend/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php";

class Zend_Soap_Wsdl_ComplexTypeB
{
    /**
     * @var string
     */
    public $bar;
    /**
     * @var string
     */
    public $foo;
}

class Zend_Soap_Service_Server2
{
    /**
     * @param  string $foo
     * @param  string $bar
     * @return Zend_Soap_Wsdl_ComplexTypeB
     */
    public function request($foo, $bar)
    {
        $b = new Zend_Soap_Wsdl_ComplexTypeB();
        $b->bar = $bar;
        $b->foo = $foo;
        return $b;
    }
}

if(isset($_GET['wsdl'])) {
    $server = new Zend_Soap_AutoDiscover(new Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new Zend_Soap_Server($uri);
}
$server->setClass('Zend_Soap_Service_Server2');
$server->handle();