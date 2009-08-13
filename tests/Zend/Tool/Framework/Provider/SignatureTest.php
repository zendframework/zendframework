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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see TestHelper.php
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/**
 * @see Zend_Tool_Framework_Provider_Repository
 */
require_once 'Zend/Tool/Framework/Provider/Repository.php';

require_once 'Zend/Tool/Framework/Registry.php';
require_once 'Zend/Tool/Framework/Action/Repository.php';
require_once '_files/ProviderOne.php';
require_once '_files/ProviderTwo.php';
require_once '_files/ProviderAltName.php';
require_once '_files/ProviderFullFeatured.php';
require_once '_files/ProviderFullFeatured2.php';
require_once '_files/ProviderFullFeaturedBadSpecialties.php';
require_once '_files/ProviderFullFeaturedBadSpecialties2.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Provider
 */
class Zend_Tool_Framework_Provider_SignatureTest extends PHPUnit_Framework_TestCase
{

    protected $_registry = null;

    /**
     * @var Zend_Tool_Framework_Provider_Signature
     */
    protected $_targetSignature = null;

    public function setup()
    {
        // setup the registry components required to test with
        $this->_registry = new Zend_Tool_Framework_Registry();
        $this->_registry->setActionRepository(new Zend_Tool_Framework_Action_Repository());
        $this->_targetSignature = new Zend_Tool_Framework_Provider_Signature(new Zend_Tool_Framework_Provider_ProviderFullFeatured());
        $this->_targetSignature->setRegistry($this->_registry);
        $this->_targetSignature->process();
    }

    public function teardown()
    {
        $this->_registry->reset();
    }

    public function testSignatureCanBeCreatedFromProvider()
    {
        $signature = new Zend_Tool_Framework_Provider_Signature(new Zend_Tool_Framework_Provider_ProviderOne());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $signature->process();
        $this->assertEquals('ProviderOne', $signature->getName());
    }

    public function testSignatureCanBeCreatedFromProviderWhenOverridingName()
    {
        $signature = new Zend_Tool_Framework_Provider_Signature(new Zend_Tool_Framework_Provider_ProviderFullFeatured());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $this->assertEquals('FooBarBaz', $signature->getName());
    }
    
    public function testGetProviderReturnsProvider()
    {
        $signature = new Zend_Tool_Framework_Provider_Signature(new Zend_Tool_Framework_Provider_ProviderOne());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $this->assertTrue($signature->getProvider() instanceof Zend_Tool_Framework_Provider_ProviderOne);
    }
    
    public function testGetProviderReflectionWillReturnZendReflectionClassObject()
    {
        $signature = new Zend_Tool_Framework_Provider_Signature(new Zend_Tool_Framework_Provider_ProviderOne());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $this->assertTrue($signature->getProviderReflection() instanceof Zend_Reflection_Class);
    }
    
    public function testGetSpecialtiesReturnsParsedSpecialties()
    {
        $this->assertEquals(array('_Global', 'Hi', 'BloodyMurder', 'ForYourTeam'), $this->_targetSignature->getSpecialties());
    }
    
    public function testGetSpecialtiesReturnsParsedSpecialtiesFromMethodInsteadOfProperty()
    {
        $signature = new Zend_Tool_Framework_Provider_Signature(new Zend_Tool_Framework_Provider_ProviderFullFeatured2());
        $signature->setRegistry($this->_registry);
        $signature->process();
        $this->assertEquals(array('_Global', 'Hi', 'BloodyMurder', 'ForYourTeam'), $signature->getSpecialties());
    }

    /**
     * @expectedException Zend_Tool_Framework_Provider_Exception
     */
    public function testGetSpecialtiesReturnsParsedSpecialtiesThrowsExceptionOnBadPropertyValue()
    {
        $signature = new Zend_Tool_Framework_Provider_Signature(new Zend_Tool_Framework_Provider_ProviderFullFeaturedBadSpecialties());
        $signature->setRegistry($this->_registry);
        $signature->process();
    }
    
    /**
     * @expectedException Zend_Tool_Framework_Provider_Exception
     */
    public function testGetSpecialtiesReturnsParsedSpecialtiesThrowsExceptionOnBadReturnValue()
    {
        $signature = new Zend_Tool_Framework_Provider_Signature(new Zend_Tool_Framework_Provider_ProviderFullFeaturedBadSpecialties2());
        $signature->setRegistry($this->_registry);
        $signature->process();
    }
    
    public function testGetActionsWillReturnProperActions()
    {
        $actionArray = $this->_targetSignature->getActions();
        $action = array_shift($actionArray);
        $this->assertTrue($action instanceof Zend_Tool_Framework_Action_Base);
        $this->assertEquals('Say', $action->getName());
        $action = array_shift($actionArray);
        $this->assertTrue($action instanceof Zend_Tool_Framework_Action_Base);
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
