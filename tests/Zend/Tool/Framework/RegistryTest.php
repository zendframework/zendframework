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
 * @see TestHelper.php
 */

/**
 * @see Zend_Tool_Framework_Registry
 */

/** Other Requirements */

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
class Zend_Tool_Framework_RegistryTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->_registry = new Zend_Tool_Framework_Registry();
    }

    public function teardown()
    {
        $this->_registry->reset();
    }

    public function testRegistryCanGetAndSetClient()
    {
        $this->assertNull($this->_registry->getClient());
        $this->_registry->setClient($client = new Zend_Tool_Framework_Asset_EmptyClient());
        $this->assertTrue($this->_registry->getClient() === $client);
    }

    public function testRegistryCanGetAndSetLoader()
    {
        $this->assertTrue($this->_registry->getLoader() instanceof Zend_Tool_Framework_Loader_Abstract);
        $this->_registry->setLoader($loader = new Zend_Tool_Framework_Asset_EmptyLoader());
        $this->assertTrue($this->_registry->getLoader() === $loader);
    }

    public function testRegistryCanGetAndSetActionRepository()
    {
        $this->assertTrue($this->_registry->getActionRepository() instanceof Zend_Tool_Framework_Action_Repository);
        $this->_registry->setActionRepository($repo = new Zend_Tool_Framework_Action_Repository());
        $this->assertTrue($this->_registry->getActionRepository() === $repo);
    }

    public function testRegistryCanGetAndSetProviderRepository()
    {
        $this->assertTrue($this->_registry->getProviderRepository() instanceof Zend_Tool_Framework_Provider_Repository);
        $this->_registry->setProviderRepository($repo = new Zend_Tool_Framework_Provider_Repository());
        $this->assertTrue($this->_registry->getProviderRepository() === $repo);
    }

    public function testRegistryCanGetAndSetManifestRepository()
    {
        $this->assertTrue($this->_registry->getManifestRepository() instanceof Zend_Tool_Framework_Manifest_Repository);
        $this->_registry->setManifestRepository($repo = new Zend_Tool_Framework_Manifest_Repository());
        $this->assertTrue($this->_registry->getManifestRepository() === $repo);
    }

    public function testRegistryCanGetAndSetRequest()
    {
        $this->assertTrue($this->_registry->getRequest() instanceof Zend_Tool_Framework_Client_Request);
        $this->_registry->setRequest($req = new Zend_Tool_Framework_Client_Request());
        $this->assertTrue($this->_registry->getRequest() === $req);
    }

    public function testRegistryCanGetAndSetResponse()
    {
        $this->assertTrue($this->_registry->getResponse() instanceof Zend_Tool_Framework_Client_Response);
        $this->_registry->setResponse($resp = new Zend_Tool_Framework_Client_Response());
        $this->assertTrue($this->_registry->getResponse() === $resp);
    }

    public function testMagicGetAndSetOfRegistryItems()
    {
        $this->assertTrue($this->_registry->request instanceof Zend_Tool_Framework_Client_Request);
        $this->_registry->request = new Zend_Tool_Framework_Client_Request();
        $this->assertTrue($this->_registry->request instanceof Zend_Tool_Framework_Client_Request);
    }

    /**
     * @expectedException Zend_Tool_Framework_Exception
     */
    public function testMagicGetThrowsExceptionOnNonExistentItem()
    {
        $foo = $this->_registry->foo;
    }

    /**
     * @expectedException Zend_Tool_Framework_Exception
     */
    public function testMagicSetThrowsExceptionOnNonExistentItem()
    {
        $this->_registry->foo = 'foo';
    }

    /**
     * @expectedException Zend_Tool_Framework_Exception
     */
    public function testIsObjectRegistryEnablableWillThrowExceptionsOnNonObject()
    {
        $this->_registry->isObjectRegistryEnablable('foo');
    }

    /**
     * @expectedException Zend_Tool_Framework_Exception
     */
    public function testEnableRegistryOnObjectWillThrowExceptionsOnNonObject()
    {
        $this->_registry->enableRegistryOnObject(new ArrayObject());
    }

}

