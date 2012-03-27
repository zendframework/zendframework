<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Pattern;
use Zend\Cache;

/**
 * Test class
 */
class TestClassCache
{
    /**
     * A counter how oftern the method "bar" was called
     */
    public static $fooCounter = 0;

    public static function bar()
    {
        ++self::$fooCounter;
        $args = func_get_args();

        echo 'foobar_output('.implode(', ', $args) . ') : ' . self::$fooCounter;
        return 'foobar_return('.implode(', ', $args) . ') : ' . self::$fooCounter;
    }

    public static function emptyMethod() {}

}

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class ClassCacheTest extends CommonPatternTest
{

    /**
     * @var Zend\Cache\Storage\Adapter
     */
    protected $_storage;

    public function setUp()
    {
        $this->_storage = new Cache\Storage\Adapter\Memory();
        $this->_options = new Cache\Pattern\PatternOptions(array(
            'class'   => __NAMESPACE__ . '\TestClassCache',
            'storage' => $this->_storage,
        ));
        $this->_pattern = new Cache\Pattern\ClassCache();
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
            'bar',
            array('testCallEnabledCacheOutputByDefault', 'arg2')
        );
    }

    public function testCallDisabledCacheOutput()
    {
        $this->_options->setCacheOutput(false);
        $this->_testCall(
            'bar',
            array('testCallDisabledCacheOutput', 'arg2')
        );
    }

    public function testGenerateKey()
    {
        $args = array('arg1', 2, 3.33, null);

        $generatedKey = $this->_pattern->generateKey('emptyMethod', $args);
        $usedKey      = null;
        $this->_options->getStorage()->events()->attach('setItem.pre', function ($event) use (&$usedKey) {
            $params = $event->getParams();
            $usedKey = $params['key'];
        });

        $this->_pattern->call('emptyMethod', $args);
        $this->assertEquals($generatedKey, $usedKey);
    }

    public function testCallUnknownMethodException()
    {
        $this->setExpectedException('Zend\\Cache\\Exception\\InvalidArgumentException');
        $this->_pattern->call('notExiststingMethod');
    }

    protected function _testCall($method, array $args)
    {
        $returnSpec = 'foobar_return(' . implode(', ', $args) . ') : ';
        $outputSpec = 'foobar_output(' . implode(', ', $args) . ') : ';

        // first call - not cached
        $firstCounter = TestClassCache::$fooCounter + 1;

        ob_start();
        ob_implicit_flush(false);
        $return = call_user_func_array(array($this->_pattern, $method), $args);
        $data = ob_get_clean();

        $this->assertEquals($returnSpec . $firstCounter, $return);
        $this->assertEquals($outputSpec . $firstCounter, $data);

        // second call - cached
        ob_start();
        ob_implicit_flush(false);
        $return = call_user_func_array(array($this->_pattern, $method), $args);
        $data = ob_get_clean();

        $this->assertEquals($returnSpec . $firstCounter, $return);
        if ($this->_options->getCacheOutput()) {
            $this->assertEquals($outputSpec . $firstCounter, $data);
        } else {
            $this->assertEquals('', $data);
        }
    }

}
