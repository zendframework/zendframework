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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Tool\Framework\Registry;
use Zend\Tool\Framework\Action;
use Zend\Tool\Framework\Provider;
use Zend\Tool\Framework\Manifest;
use Zend\Tool\Framework\Client;
use Zend\Tool\Framework\Client\Response;


/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->_registry = new \Zend\Tool\Framework\Registry\FrameworkRegistry();
    }

    public function teardown()
    {
        $this->_registry->reset();
    }

    public function testRegistryCanGetAndSetClient()
    {
        $this->assertNull($this->_registry->getClient());
        $this->_registry->setClient($client = new \ZendTest\Tool\Framework\TestAsset\EmptyClient());
        $this->assertTrue($this->_registry->getClient() === $client);
    }

    public function testRegistryCanGetAndSetLoader()
    {
        $this->assertTrue($this->_registry->getLoader() instanceof \Zend\Tool\Framework\Loader\AbstractLoader);
        $this->_registry->setLoader($loader = new \ZendTest\Tool\Framework\TestAsset\EmptyLoader());
        $this->assertTrue($this->_registry->getLoader() === $loader);
    }

    public function testRegistryCanGetAndSetActionRepository()
    {
        $this->assertTrue($this->_registry->getActionRepository() instanceof Action\Repository);
        $this->_registry->setActionRepository($repo = new Action\Repository());
        $this->assertTrue($this->_registry->getActionRepository() === $repo);
    }

    public function testRegistryCanGetAndSetProviderRepository()
    {
        $this->assertTrue($this->_registry->getProviderRepository() instanceof Provider\Repository);
        $this->_registry->setProviderRepository($repo = new Provider\Repository());
        $this->assertTrue($this->_registry->getProviderRepository() === $repo);
    }

    public function testRegistryCanGetAndSetManifestRepository()
    {
        $this->assertTrue($this->_registry->getManifestRepository() instanceof Manifest\Repository);
        $this->_registry->setManifestRepository($repo = new Manifest\Repository());
        $this->assertTrue($this->_registry->getManifestRepository() === $repo);
    }

    public function testRegistryCanGetAndSetRequest()
    {
        $this->assertTrue($this->_registry->getRequest() instanceof Client\Request);
        $this->_registry->setRequest($req = new Client\Request());
        $this->assertTrue($this->_registry->getRequest() === $req);
    }

    public function testRegistryCanGetAndSetResponse()
    {
        $this->assertTrue($this->_registry->getResponse() instanceof Response);
        $this->_registry->setResponse($resp = new Response());
        $this->assertTrue($this->_registry->getResponse() === $resp);
    }

    public function testMagicGetAndSetOfRegistryItems()
    {
        $this->assertTrue($this->_registry->request instanceof Client\Request);
        $this->_registry->request = new Client\Request();
        $this->assertTrue($this->_registry->request instanceof Client\Request);
    }

    public function testMagicGetThrowsExceptionOnNonExistentItem()
    {
        $this->setExpectedException('Zend\Tool\Framework\Exception');
        $foo = $this->_registry->foo;
    }

    public function testMagicSetThrowsExceptionOnNonExistentItem()
    {
        $this->setExpectedException('Zend\Tool\Framework\Exception');
        $this->_registry->foo = 'foo';
    }

    public function testIsObjectRegistryEnablableWillThrowExceptionsOnNonObject()
    {
        $this->setExpectedException('Zend\Tool\Framework\Exception');
        $this->_registry->isObjectRegistryEnablable('foo');
    }

    public function testEnableRegistryOnObjectWillThrowExceptionsOnNonObject()
    {
        $this->setExpectedException('Zend\Tool\Framework\Exception');
        $this->_registry->enableRegistryOnObject(new \ArrayObject());
    }

}

