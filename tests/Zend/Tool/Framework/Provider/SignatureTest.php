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
namespace ZendTest\Tool\Framework\Provider;
use Zend\Tool\Framework\Provider;
use Zend\Tool\Framework\Action;

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
class SignatureTest extends \PHPUnit_Framework_TestCase
{

    protected $_registry = null;

    /**
     * @var Zend_Tool_Framework_Provider_Signature
     */
    protected $_targetSignature = null;

    public function setup()
    {
        // setup the registry components required to test with
        $this->_registry = new \Zend\Tool\Framework\Registry\FrameworkRegistry();
        $this->_registry->setActionRepository(new Action\Repository());
        $this->_targetSignature = new Provider\Signature(new \ZendTest\Tool\Framework\Provider\TestAsset\ProviderFullFeatured());
        $this->_targetSignature->setRegistry($this->_registry);
        $this->_targetSignature->process();
    }

    public function teardown()
    {
        $this->_registry->reset();
    }

    public function testSignatureCanBeCreatedFromProvider()
    {
        $signature = new Provider\Signature(new \ZendTest\Tool\Framework\Provider\TestAsset\ProviderOne());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $signature->process();
        $this->assertEquals('ProviderOne', $signature->getName());
    }

    public function testSignatureCanBeCreatedFromProviderWhenOverridingName()
    {
        $signature = new Provider\Signature(new \ZendTest\Tool\Framework\Provider\TestAsset\ProviderFullFeatured());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $this->assertEquals('FooBarBaz', $signature->getName());
    }

    public function testGetProviderReturnsProvider()
    {
        $signature = new Provider\Signature(new \ZendTest\Tool\Framework\Provider\TestAsset\ProviderOne());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $this->assertTrue($signature->getProvider() instanceof \ZendTest\Tool\Framework\Provider\TestAsset\ProviderOne);
    }

    public function testGetProviderReflectionWillReturnZendReflectionClassObject()
    {
        $signature = new Provider\Signature(new \ZendTest\Tool\Framework\Provider\TestAsset\ProviderOne());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $this->assertTrue($signature->getProviderReflection() instanceof \Zend\Reflection\ReflectionClass);
    }

    public function testGetSpecialtiesReturnsParsedSpecialties()
    {
        $this->assertEquals(array('_Global', 'Hi', 'BloodyMurder', 'ForYourTeam'), $this->_targetSignature->getSpecialties());
    }

    public function testGetSpecialtiesReturnsParsedSpecialtiesFromMethodInsteadOfProperty()
    {
        $signature = new Provider\Signature(new \ZendTest\Tool\Framework\Provider\TestAsset\ProviderFullFeatured2());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $this->assertEquals(array('_Global', 'Hi', 'BloodyMurder', 'ForYourTeam'), $signature->getSpecialties());
    }

    public function testGetSpecialtiesReturnsParsedSpecialtiesThrowsExceptionOnBadPropertyValue()
    {
        $this->setExpectedException('Zend\Tool\Framework\Provider\Exception');
        $signature = new Provider\Signature(new \ZendTest\Tool\Framework\Provider\TestAsset\ProviderFullFeaturedBadSpecialties());
        $signature->setRegistry($this->_registry);
        $signature->process();
    }

    public function testGetSpecialtiesReturnsParsedSpecialtiesThrowsExceptionOnBadReturnValue()
    {
        $this->setExpectedException('Zend\Tool\Framework\Provider\Exception');
        $signature = new Provider\Signature(new \ZendTest\Tool\Framework\Provider\TestAsset\ProviderFullFeaturedBadSpecialties2());
        $signature->setRegistry($this->_registry);
        $signature->process();
    }

    public function testGetActionsWillReturnProperActions()
    {
        $actionArray = $this->_targetSignature->getActions();
        $action = array_shift($actionArray);
        $this->assertTrue($action instanceof Action\Base);
        $this->assertEquals('Say', $action->getName());
        $action = array_shift($actionArray);
        $this->assertTrue($action instanceof Action\Base);
        $this->assertEquals('Scream', $action->getName());
    }

    public function testGetActionableMethodsReturnsAllActionableMethods()
    {
        $this->assertEquals(5, count($this->_targetSignature->getActionableMethods()));

        $actionableMethods = $this->_targetSignature->getActionableMethods();
        $actionableMethod = array_shift($actionableMethods);
        $this->assertEquals('say', $actionableMethod['methodName']);
        $actionableMethod = array_shift($actionableMethods);
        $this->assertEquals('scream', $actionableMethod['methodName']);
        $actionableMethod = array_shift($actionableMethods);
        $this->assertEquals('sayHi', $actionableMethod['methodName']);
        $actionableMethod = array_shift($actionableMethods);
        $this->assertEquals('screamBloodyMurder', $actionableMethod['methodName']);
        $actionableMethod = array_shift($actionableMethods);
        $this->assertEquals('screamForYourTeam', $actionableMethod['methodName']);
    }

    public function testGetActionableMethodReturnsCorrectActionableMethod()
    {
        $actionableMethod = $this->_targetSignature->getActionableMethod('scream');
        $this->assertEquals('Scream', $actionableMethod['actionName']);

        $this->assertFalse($this->_targetSignature->getActionableMethod('Foo'));
    }

    public function testGetActionableMethodByActionNameReturnsCorrectActionableMethod()
    {
        $actionableMethod = $this->_targetSignature->getActionableMethodByActionName('Scream');
        $this->assertEquals('scream', $actionableMethod['methodName']);

        $this->assertFalse($this->_targetSignature->getActionableMethodByActionName('Foo'));
    }

}
