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
 * @see Zend_Tool_Framework_Provider_Repository
 */

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Provider
 */
class Zend_Tool_Framework_Provider_RepositoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Provider_Repository
     */
    protected $_repository = null;

    public function setup()
    {
        $this->_repository = new Zend_Tool_Framework_Provider_Repository();

        // setup the registry components required to test with
        $this->_registry = new Zend_Tool_Framework_Registry();
        $this->_registry->setProviderRepository($this->_repository);
        $this->_registry->setActionRepository(new Zend_Tool_Framework_Action_Repository());
    }

    public function teardown()
    {
        $this->_registry->reset();
        $this->_repository = null;
    }

    public function testRepositoryIsEmpty()
    {
        $this->assertEquals(0, count($this->_repository));
    }

    public function testAddProviderCanHandleProviderObjects()
    {
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderOne());
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderTwo());
        $this->_repository->process();
        $this->assertEquals(2, count($this->_repository));
    }

    public function testAddProviderCanHandleProviderWithAlternateName()
    {
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderOne());
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderTwo());
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderAltName());
        $this->_repository->process();
        $this->assertEquals(3, count($this->_repository));
        $this->assertEquals('FooBar', $this->_repository->getProviderSignature('FooBar')->getName());
    }

    /**
     * @expectedException Zend_Tool_Framework_Provider_Exception
     */
    public function testAddProviderThrowsExceptionOnDuplicateName()
    {
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderOne());
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderOne());
    }

    public function testAddProviderWillProcessOnCall()
    {
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderOne());
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderTwo());
        $this->_repository->process();
        $this->_repository->setProcessOnAdd(true);
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderAltName());
        $this->assertEquals(3, count($this->_repository));
        $this->assertEquals('FooBar', $this->_repository->getProviderSignature('FooBar')->getName());
    }

    public function testGetProvidersReturnsProviders()
    {
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderOne());
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderTwo());
        $this->_repository->process();
        $this->assertEquals(2, count($this->_repository));
        foreach ($this->_repository->getProviders() as $provider) {
            $this->assertTrue($provider instanceof Zend_Tool_Framework_Provider_Interface);
        }

    }

    public function testGetProviderSignaturesReturnsProviderSignatures()
    {
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderOne());
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderTwo());
        $this->_repository->process();
        $this->assertEquals(2, count($this->_repository));
        foreach ($this->_repository->getProviderSignatures() as $providerSignature) {
            $this->assertTrue($providerSignature instanceof Zend_Tool_Framework_Provider_Signature);
        }

    }

    public function testHasProviderReturnsCorrectValues()
    {
        $this->_repository->addProvider(($providerOne = new Zend_Tool_Framework_Provider_Asset_ProviderOne()));
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderTwo());

        $this->assertTrue($this->_repository->hasProvider('Zend_Tool_Framework_Provider_Asset_ProviderOne', false));
        $this->assertTrue($this->_repository->hasProvider($providerOne, false));
        $this->assertTrue($this->_repository->hasProvider('Zend_Tool_Framework_Provider_Asset_ProviderTwo', false));
        $this->assertFalse($this->_repository->hasProvider('Zend_Tool_Framework_Provider_Asset_ProviderThree', false));
        $this->assertFalse($this->_repository->hasProvider('Zend_Tool_Framework_Provider_Asset_ProviderOne'));

        $this->_repository->process();
        $this->assertTrue($this->_repository->hasProvider('Zend_Tool_Framework_Provider_Asset_ProviderOne', false));
        $this->assertTrue($this->_repository->hasProvider('Zend_Tool_Framework_Provider_Asset_ProviderOne'));
        $this->assertFalse($this->_repository->hasProvider('Zend_Tool_Framework_Provider_Asset_ProviderThree'));
    }

    public function testGetProviderReturnsProvider()
    {
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderOne());
        $this->_repository->addProvider(new Zend_Tool_Framework_Provider_Asset_ProviderTwo());
        $this->_repository->process();
        $this->assertTrue($this->_repository->getProvider('ProviderOne') instanceof Zend_Tool_Framework_Provider_Interface);
    }


    public function testRepositoryIsCountable()
    {
        $this->assertTrue($this->_repository instanceof Countable);
    }

    public function testRepositoryIsIterable()
    {
        $this->assertTrue($this->_repository instanceof Traversable);
        foreach ($this->_repository as $provider) {
            $this->assertTrue(true);
        }
    }

}
