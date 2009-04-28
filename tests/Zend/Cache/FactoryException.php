<?php
require_once 'PHPUnit/Extensions/ExceptionTestCase.php';
require_once 'Zend/Cache.php';
 
class Zend_Cache_FactoryException extends PHPUnit_Extensions_ExceptionTestCase
{
    function setUp(){
        $this->setExpectedException('Zend_Cache_Exception');
    }
    
    public function testBadFrontend()
    {
        Zend_Cache::factory('badFrontend', 'File');
    }
    
    public function testBadBackend()
    {
        Zend_Cache::factory('Output', 'badBackend');
    }
    
    public function testFrontendBadParam()
    {
        Zend_Cache::factory('badFrontend', 'File', array('badParam'=>true));
    }
    
    public function testBackendBadParam()
    {
        Zend_Cache::factory('Output', 'badBackend', array(), array('badParam'=>true));
    }
    
    public function testThrowMethod()
    {
        Zend_Cache::throwException('test');
    }
}
