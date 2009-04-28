<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/Sqlite.php';

/**
 * Common tests for backends
 */
require_once 'CommonExtendedBackendTest.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class Zend_Cache_sqliteBackendTest extends Zend_Cache_CommonExtendedBackendTest {
    
    protected $_instance;
    private $_cache_dir;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend_Cache_Backend_Sqlite', $data, $dataName);
    }
    
    public function setUp($notag = false)
    {          
        @mkdir($this->getTmpDir());
        $this->_cache_dir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $this->_instance = new Zend_Cache_Backend_Sqlite(array(
            'cache_db_complete_path' => $this->_cache_dir . 'cache.db'
        ));
        parent::setUp($notag);       
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
        @unlink($this->_cache_dir . 'cache.db');
        $this->rmdir();
    }
    
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_Sqlite(array('cache_db_complete_path' => $this->_cache_dir . 'cache.db'));    
    }
    
    public function testConstructorWithABadDBPath()
    {
        try {
            $test = new Zend_Cache_Backend_Sqlite(array('cache_db_complete_path' => '/foo/bar/lfjlqsdjfklsqd/cache.db'));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }
    
    public function testCleanModeAllWithVacuum()
    {
        $this->_instance = new Zend_Cache_Backend_Sqlite(array(
            'cache_db_complete_path' => $this->_cache_dir . 'cache.db',
            'automatic_vacuum_factor' => 1
        ));
        parent::setUp();    
        $this->assertTrue($this->_instance->clean('all'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->test('bar2'));
    }
    
    public function testRemoveCorrectCallWithVacuum()
    {   
        $this->_instance = new Zend_Cache_Backend_Sqlite(array(
            'cache_db_complete_path' => $this->_cache_dir . 'cache.db',
            'automatic_vacuum_factor' => 1
        ));
        parent::setUp();  
        $this->assertTrue($this->_instance->remove('bar'));
        $this->assertFalse($this->_instance->test('bar')); 
        $this->assertFalse($this->_instance->remove('barbar'));
        $this->assertFalse($this->_instance->test('barbar'));        
    }
    
}


