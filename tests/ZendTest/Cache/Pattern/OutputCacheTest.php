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
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class OutputCacheTest extends CommonPatternTest
{

    /**
     * @var Zend\Cache\Storage\StorageInterface
     */
    protected $_storage;

    /**
     * Nesting level of output buffering used to restore on tearDown()
     *
     * @var null|int
     */
    protected $_obLevel;

    public function setUp()
    {
        $this->_storage = new Cache\Storage\Adapter\Memory(array(
            'memory_limit' => 0
        ));
        $this->_options = new Cache\Pattern\PatternOptions(array(
            'storage' => $this->_storage,
        ));
        $this->_pattern = new Cache\Pattern\OutputCache();
        $this->_pattern->setOptions($this->_options);

        // used to reset the level on tearDown
        $this->_obLevel = ob_get_level();

        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->_obLevel > ob_get_Level()) {
            for ($i = ob_get_level(); $i < $this->_obLevel; $i++) {
                ob_start();
            }
            $this->fail("Nesting level of output buffering to often ended");
        } elseif ($this->_obLevel < ob_get_level()) {
            for ($i = ob_get_level(); $i > $this->_obLevel; $i--) {
                ob_end_clean();
            }
            $this->fail("Nesting level of output buffering not well restored");
        }

        parent::tearDown();
    }

    public function testStartEndCacheMiss()
    {
        $output = 'foobar';
        $key    = 'testStartEndCacheMiss';

        ob_start();
        $this->assertFalse($this->_pattern->start($key));
        echo $output;
        $this->assertTrue($this->_pattern->end());
        $data = ob_get_clean();

        $this->assertEquals($output, $data);
        $this->assertEquals($output, $this->_pattern->getOptions()->getStorage()->getItem($key));
    }

    public function testStartEndCacheHit()
    {
        $output = 'foobar';
        $key    = 'testStartEndCacheHit';

        // fill cache
        $this->_pattern->getOptions()->getStorage()->setItem($key, $output);

        ob_start();
        $this->assertTrue($this->_pattern->start($key));
        $data = ob_get_clean();

        $this->assertSame($output, $data);
    }

    public function testThrowMissingKeyException()
    {
        $this->setExpectedException('Zend\Cache\Exception\MissingKeyException');
        $this->_pattern->start(''); // empty key
    }
}
