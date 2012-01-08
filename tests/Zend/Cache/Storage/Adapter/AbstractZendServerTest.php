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

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter,
    Zend\Cache\Storage\Adapter\AdapterOptions,
    Zend\Cache\Storage\Adapter\AbstractZendServer;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
        $this->assertInternalType('string', $options->getNamespacePattern());
        $this->assertInternalType('string', $options->getKeyPattern());
        $this->assertInternalType('boolean', $options->getIgnoreMissingItems());
    }

    public function testGetWithDefaultNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('defaultns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals('value', $this->_storage->getItem('key'));
    }

    public function testGetWithArgNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('argns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals('value', $this->_storage->getItem('key', array('namespace' => 'argns')));
    }

    public function testInfoWithDefaultNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('defaultns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals(array(), $this->_storage->getMetadata('key'));
    }

    public function testInfoWithArgNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('argns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals(array(), $this->_storage->getMetadata('key', array('namespace' => 'argns')));
    }

    public function testExistsWithDefaultNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('defaultns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals(true, $this->_storage->hasItem('key'));
    }

    public function testExistsWithArgNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcFetch')
                       ->with($this->equalTo('argns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue('value'));

        $this->assertEquals(true, $this->_storage->hasItem('key', array('namespace' => 'argns')));
    }

    public function testSetWithDefaultNamespaceAndTtl()
    {
        $this->_options->setTtl(10);
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcStore')
                       ->with(
                           $this->equalTo('defaultns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'),
                           $this->equalTo('value'),
                           $this->equalTo(10)
                       )
                       ->will($this->returnValue(true));

        $this->assertEquals(true, $this->_storage->setItem('key', 'value'));
    }

    public function testSetWithArgNamespaceAndTtl()
    {
        $this->_options->setTtl(10);
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcStore')
                       ->with(
                           $this->equalTo('argns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'),
                           $this->equalTo('value'),
                           $this->equalTo(100)
                       )
                       ->will($this->returnValue(true));

        $this->assertEquals(true, $this->_storage->setItem('key', 'value', array('namespace' => 'argns', 'ttl' => 100)));
    }

    public function testRemoveWithDefaultNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcDelete')
                       ->with($this->equalTo('defaultns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue(true));

        $this->assertEquals(true, $this->_storage->removeItem('key'));
    }

    public function testRemoveWithArgNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $this->_storage->expects($this->once())
                       ->method('zdcDelete')
                       ->with($this->equalTo('argns' . AbstractZendServer::NAMESPACE_SEPARATOR . 'key'))
                       ->will($this->returnValue(true));

        $this->assertEquals(true, $this->_storage->removeItem('key', array('namespace' => 'argns')));
    }

    public function testClearExpired()
    {
        $this->_storage->expects($this->never())
                       ->method('zdcClear');

        $this->assertEquals(true, $this->_storage->clear(Adapter::MATCH_EXPIRED));
    }

    public function testClearActive()
    {
        $this->_storage->expects($this->once())
                       ->method('zdcClear')
                       ->will($this->returnValue(true));

        $rs = $this->_storage->clear(Adapter::MATCH_ACTIVE);
        $this->assertEquals(true, $rs);
    }

    public function testClearActiveByDefaultNamespace()
    {
        $this->_storage->expects($this->once())
                       ->method('zdcClearByNamespace')
                       ->with($this->equalTo($this->_options->getNamespace()))
                       ->will($this->returnValue(true));

        $rs = $this->_storage->clearByNamespace(Adapter::MATCH_ACTIVE);
        $this->assertEquals(true, $rs);
    }

    public function testClearActiveByArgNamespace()
    {
        $ns = 'namespace';
        $this->_storage->expects($this->once())
                       ->method('zdcClearByNamespace')
                       ->with($this->equalTo($ns))
                       ->will($this->returnValue(true));

        $rs = $this->_storage->clearByNamespace(Adapter::MATCH_ACTIVE, array('namespace' => $ns));
        $this->assertEquals(true, $rs);
    }

}
