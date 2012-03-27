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
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class OutputCacheTest extends CommonPatternTest
{

    /**
     * @var Zend\Cache\Storage\Adapter
     */
    protected $_storage;

    public function setUp()
    {
        $this->_storage = new Cache\Storage\Adapter\Memory();
        $this->_options = new Cache\Pattern\PatternOptions(array(
            'storage' => $this->_storage,
        ));
        $this->_pattern = new Cache\Pattern\OutputCache();
        $this->_pattern->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testStartEndOutput()
    {
        $output = 'foobar';
        $key    = 'testStartEndOutput';

        ob_start();
        if (!($this->_pattern->start($key))) {
            echo $output;
            $this->_pattern->end();
        }
        $data = ob_get_clean();
        $this->assertEquals($output, $data);
    }

    public function testStartEndReturnOutput()
    {
        $output = 'foobar';
        $key    = 'testStartEndReturnOutput';
        $data   = '';
        if (!($this->_pattern->start($key))) {
            echo $output;
            $data = $this->_pattern->end(array('output' => false));
        }
        $this->assertEquals($output, $data);
    }

    public function testStartEndCachedOutput()
    {
        $output = 'foobar';
        $key    = 'testStartEndCachedOutput';

        // first run to write to cache
        ob_start();
        if (!($this->_pattern->start($key))) {
            echo $output;
            $this->_pattern->end();
        }
        $data = ob_get_clean();
        $this->assertEquals($output, $data);

        // second run to check if cached
        // first run to write to cache
        ob_start();
        if (!($this->_pattern->start($key))) {
            ob_end_clean();
            $this->fail('Second run has to be cached');
        }
        $data = ob_get_clean();
        $this->assertEquals($output, $data);
    }

    public function testStartEndReturnCachedOutput()
    {
        $output = 'foobar';
        $key    = 'testStartEndReturnCachedOutput';

        // first run to write to cache
        ob_start();
        if (!($this->_pattern->start($key))) {
            echo $output;
            $this->_pattern->end();
        }
        $data = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals($output, $data);

        // second run to check if cached
        // first run to write to cache
        if ( ($data = $this->_pattern->start($key, array('output' => false))) === false ) {
            $this->fail('Second run has to be cached');
        }
        $this->assertEquals($output, $data);
    }

    public function testThrowMissingKeyException()
    {
        $this->setExpectedException('Zend\\Cache\\Exception\\MissingKeyException');
        $this->_pattern->start(''); // empty key
    }

}
