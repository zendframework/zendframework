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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;
use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
