<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
 /**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/ZendPlatform.php';

/**
 * Common tests for backends
 */
require_once 'CommonBackendTest.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class Zend_Cache_ZendPlatformBackendTest extends Zend_Cache_CommonBackendTest {
    
    protected $_instance;
 
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend_Cache_Backend_ZendPlatform', $data, $dataName);
    }
       
    public function setUp($notag = false)
    {        
        if(!function_exists('output_cache_get')) {
            $this->markTestSkipped('Zend Platform is not installed, skipping test');
            return;
        }
        $this->_instance = new Zend_Cache_Backend_ZendPlatform(array());
        parent::setUp($notag);          
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_ZendPlatform();    
    }
       
    public function testRemoveCorrectCall()
    {
        $this->assertTrue($this->_instance->remove('bar'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertTrue($this->_instance->remove('barbar'));
        $this->assertFalse($this->_instance->test('barbar'));
    }

    public function testGetWithAnExpiredCacheId()
    {
    sleep(2);
        $this->_instance->setDirectives(array('lifetime' => 1));
        $this->assertEquals('bar : data to cache', $this->_instance->load('bar', true));
        $this->assertFalse($this->_instance->load('bar'));
        $this->_instance->setDirectives(array('lifetime' => 3600));
    }

    // Because of limitations of this backend...
    public function testCleanModeNotMatchingTags2() {}
    public function testCleanModeNotMatchingTags3() {}
    public function testCleanModeOld() {}
    public function testCleanModeNotMatchingTags() {}
}


