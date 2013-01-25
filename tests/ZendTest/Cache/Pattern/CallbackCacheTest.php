<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Pattern;

use Zend\Cache;

/**
 * Test class
 */
class TestCallbackCache
{
    /**
     * A counter how oftern the method "foo" was called
     */
    public static $fooCounter = 0;

    public static function bar()
    {
        ++self::$fooCounter;
        $args = func_get_args();

        echo   'foobar_output('.implode(', ', $args) . ') : ' . self::$fooCounter;
        return 'foobar_return('.implode(', ', $args) . ') : ' . self::$fooCounter;
    }

    public static function emptyMethod() {}

}

/**
 * Test function
 * @see ZendTest\Cache\Pattern\Foo::bar
 */
function bar ()
{
    return call_user_func_array(__NAMESPACE__ . '\TestCallbackCache::bar', func_get_args());
}

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class CallbackCacheTest extends CommonPatternTest
{

    /**
     * @var Zend\Cache\Storage\StorageInterface
     */
    protected $_storage;

    public function setUp()
    {
        $this->_storage = new Cache\Storage\Adapter\Memory(array(
            'memory_limit' => 0
        ));
        $this->_options = new Cache\Pattern\PatternOptions(array(
            'storage' => $this->_storage,
        ));
        $this->_pattern = new Cache\Pattern\CallbackCache();
        $this->_pattern->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testCallEnabledCacheOutputByDefault()
    {
        $this->_testCall(
            __NAMESPACE__ . '\TestCallbackCache::bar',
            array('testCallEnabledCacheOutputByDefault', 'arg2')
        );
    }

    public function testCallDisabledCacheOutput()
    {
        $options = $this->_pattern->getOptions();
        $options->setCacheOutput(false);
        $this->_testCall(
            __NAMESPACE__ . '\TestCallbackCache::bar',
            array('testCallDisabledCacheOutput', 'arg2')
        );
    }

    public function testMagicFunctionCall()
    {
        $this->_testCall(
            __NAMESPACE__ . '\bar',
            array('testMagicFunctionCall', 'arg2')
        );
    }

    public function testGenerateKey()
    {
        $callback = __NAMESPACE__ . '\TestCallbackCache::emptyMethod';
        $args     = array('arg1', 2, 3.33, null);

        $generatedKey = $this->_pattern->generateKey($callback, $args);
        $usedKey      = null;
        $this->_options->getStorage()->getEventManager()->attach('setItem.pre', function ($event) use (&$usedKey) {
            $params = $event->getParams();
            $usedKey = $params['key'];
        });

        $this->_pattern->call($callback, $args);
        $this->assertEquals($generatedKey, $usedKey);
    }

    public function testCallInvalidCallbackException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_pattern->call(1);
    }

    public function testCallUnknownCallbackException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_pattern->call('notExiststingFunction');
    }

    /**
     * Running tests calling ZendTest\Cache\Pattern\TestCallbackCache::bar
     * using different callbacks resulting in this method call
     */
    protected function _testCall($callback, array $args)
    {
        $returnSpec = 'foobar_return(' . implode(', ', $args) . ') : ';
        $outputSpec = 'foobar_output(' . implode(', ', $args) . ') : ';

        // first call - not cached
        $firstCounter = TestCallbackCache::$fooCounter + 1;

        ob_start();
        ob_implicit_flush(false);
        $return = $this->_pattern->call($callback, $args);
        $data = ob_get_clean();

        $this->assertEquals($returnSpec . $firstCounter, $return);
        $this->assertEquals($outputSpec . $firstCounter, $data);

        // second call - cached
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_pattern->call($callback, $args);
        $data = ob_get_clean();

        $this->assertEquals($returnSpec . $firstCounter, $return);
        $options = $this->_pattern->getOptions();
        if ($options->getCacheOutput()) {
            $this->assertEquals($outputSpec . $firstCounter, $data);
        } else {
            $this->assertEquals('', $data);
        }
    }
}
