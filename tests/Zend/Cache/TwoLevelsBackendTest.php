<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/TwoLevels.php';

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
class Zend_Cache_TwoLevelsBackendTest extends Zend_Cache_CommonExtendedBackendTest {
    
    protected $_instance;
    private $_cache_dir;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend_Cache_Backend_TwoLevels', $data, $dataName);
    }
    
    public function setUp($notag = false)
    {          
        @mkdir($this->getTmpDir());
        $this->_cache_dir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $slowBackend = 'File';
        $fastBackend = 'Apc';
        $slowBackendOptions = array(
            'cache_dir' => $this->_cache_dir
        );
        $fastBackendOptions = array(
        );
        $this->_instance = new Zend_Cache_Backend_TwoLevels(array(
            'fast_backend' => $fastBackend,
            'slow_backend' => $slowBackend,
            'fast_backend_options' => $fastBackendOptions,
            'slow_backend_options' => $slowBackendOptions
        ));
        parent::setUp($notag);       
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $slowBackend = 'File';
        $fastBackend = 'Apc';
        $slowBackendOptions = array(
            'cache_dir' => $this->_cache_dir
        );
        $fastBackendOptions = array(
        );
        $test = new Zend_Cache_Backend_TwoLevels(array(
            'fast_backend' => $fastBackend,
            'slow_backend' => $slowBackend,
            'fast_backend_options' => $fastBackendOptions,
            'slow_backend_options' => $slowBackendOptions
        ));
    }
    
}


