<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class MemoryTest extends CommonAdapterTest
{

    public function setUp()
    {
        // instantiate memory adapter
        $this->_options = new Cache\Storage\Adapter\MemoryOptions();
        $this->_storage = new Cache\Storage\Adapter\Memory();
        $this->_storage->setOptions($this->_options);

        parent::setUp();
    }

    public function testThrowOutOfSpaceException()
    {
        $this->_options->setMemoryLimit(memory_get_usage(true) - 8);

        $this->setExpectedException('Zend\Cache\Exception\OutOfSpaceException');
        $this->_storage->addItem('test', 'test');
    }
}
