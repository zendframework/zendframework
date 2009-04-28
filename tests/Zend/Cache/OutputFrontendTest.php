<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
 /**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Frontend/Output.php';
require_once 'Zend/Cache/Backend/Test.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class Zend_Cache_OutputFrontendTest extends PHPUnit_Framework_TestCase {
    
    private $_instance;
    
    public function setUp()
    {
        if (!$this->_instance) {
            $this->_instance = new Zend_Cache_Frontend_Output(array());
            $this->_backend = new Zend_Cache_Backend_Test();
            $this->_instance->setBackend($this->_backend);
        }
    }
    
    public function tearDown()
    {
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Frontend_Output(array('lifetime' => 3600, 'caching' => true));      
    }
    
    public function testStartEndCorrectCall1()
    {
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('123'))) {
            echo('foobar');
            $this->_instance->end();
        }
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foo', $data);
    }
    
    public function testStartEndCorrectCall2()
    {
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('false'))) {
            echo('foobar');
            $this->_instance->end();
        }
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar', $data);
    }
}

