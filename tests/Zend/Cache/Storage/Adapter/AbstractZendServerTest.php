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

use Zend\Cache\Storage\StorageInterface,
    Zend\Cache\Storage\Adapter\AdapterOptions,
    Zend\Cache\Storage\Adapter\AbstractZendServer;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class AbstractZendServerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->_options = new AdapterOptions();
        $this->_storage = $this->getMockForAbstractClass('Zend\Cache\Storage\Adapter\AbstractZendServer');
        $this->_storage->setOptions($this->_options);
        $this->_storage->expects($this->any())
                       ->method('getOptions')
                       ->will($this->returnValue($this->_options));
    }

    public function testGetOptions()
    {
        $options = $this->_storage->getOptions();
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\AdapterOptions', $options);
        $this->assertInternalType('boolean', $options->getWritable());
        $this->assertInternalType('boolean', $options->getReadable());
        $this->assertInternalType('integer', $options->getTtl());
        $this->assertInternalType('string', $options->getNamespace());
        $this->assertInternalType('string', $options->getKeyPattern());
    }

    public function testGetItem()
    {
        $this->_options->setNamespace('ns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('ns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals('value', $this->_storage->getItem('key'));
    }

    public function testGetMetadata()
    {
        $this->_options->setNamespace('ns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('ns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals(array(), $this->_storage->getMetadata('key'));
    }

    public function testHasItem()
    {
        $this->_options->setNamespace('ns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('ns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals(true, $this->_storage->hasItem('key'));
    }

    public function testSetItem()
    {
        $this->_options->setTtl(10);
        $this->_options->setNamespace('ns');

        $this->_storage->expects($this->once())
                       ->method('zdcStore')
                       ->with(
                           $this->equalTo('ns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'),
                           $this->equalTo('value'),
                           $this->equalTo(10)
                       )
                       ->will($this->returnValue(true));

        $this->assertEquals(true, $this->_storage->setItem('key', 'value'));
    }

    public function testRemoveItem()
    {
        $this->_options->setNamespace('ns');

        $this->_storage->expects($this->once())
                       ->method('zdcDelete')
                       ->with($this->equalTo('ns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue(true));

        $this->assertEquals(true, $this->_storage->removeItem('key'));
    }
}
