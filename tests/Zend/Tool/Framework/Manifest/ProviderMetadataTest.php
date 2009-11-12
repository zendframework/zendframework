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
 * @see Zend_Tool_Framework_Action_Base
 */
require_once 'Zend/Tool/Framework/Manifest/ProviderMetadata.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Manifest
 */
class Zend_Tool_Framework_Manifest_ProviderMetadataTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Manifest_ProviderMetadata
     */
    protected $_metadata = null;

    public function setup()
    {
        $this->_metadata = new Zend_Tool_Framework_Manifest_ProviderMetadata();
    }

    public function teardown()
    {
        $this->_metadata = null;
    }

    public function testInitialTypeNameIsCorrect()
    {
        $this->assertEquals('Provider', $this->_metadata->getType());
    }

    public function testConstructorWillAcceptAndPersistValues()
    {
        $obj1 = new ArrayObject();

        $metadata = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
            'name' => 'Foo',
            'providerName' => 'FooBar',
            'actionName' => 'BarBaz',
            'specialtyName' => 'FooBarBaz',
            'value' => 'Bar',
            'reference' => $obj1
            ));

        $this->assertEquals('Foo',       $metadata->getName());
        $this->assertEquals('FooBar',    $metadata->getProviderName());
        $this->assertEquals('BarBaz',    $metadata->getActionName());
        $this->assertEquals('FooBarBaz', $metadata->getSpecialtyName());
        $this->assertEquals('Bar',       $metadata->getValue());
        $this->assertTrue($obj1 === $metadata->getReference());
    }

    public function testSetOptionsPersistValues()
    {
        $obj1 = new ArrayObject();

        $this->_metadata->setOptions(array(
            'name' => 'Foo',
            'providerName' => 'FooBar',
            'actionName' => 'BarBaz',
            'specialtyName' => 'FooBarBaz',
            'value' => 'Bar',
            'reference' => $obj1
            ));

        $this->assertEquals('Foo',       $this->_metadata->getName());
        $this->assertEquals('FooBar',    $this->_metadata->getProviderName());
        $this->assertEquals('BarBaz',    $this->_metadata->getActionName());
        $this->assertEquals('FooBarBaz', $this->_metadata->getSpecialtyName());
        $this->assertEquals('Bar',       $this->_metadata->getValue());
        $this->assertTrue($obj1 === $this->_metadata->getReference());
    }

    public function testSettersPersistValuesAndAreRetievableThroughGetters()
    {
        $this->_metadata->setProviderName('Foo');
        $this->assertEquals('Foo', $this->_metadata->getProviderName());

        $this->_metadata->setActionName('Bar');
        $this->assertEquals('Bar', $this->_metadata->getActionName());

        $this->_metadata->setSpecialtyName('FooBar');
        $this->assertEquals('FooBar', $this->_metadata->getSpecialtyName());
    }

    public function testMetadataObjectCanCastToStringRepresentation()
    {
        $obj1 = new ArrayObject();

        $this->_metadata->setOptions(array(
            'name' => 'Foo',
            'providerName' => 'FooBar',
            'actionName' => 'BarBaz',
            'specialtyName' => 'FooBarBaz',
            'value' => 'Bar',
            'reference' => $obj1
            ));

        $this->assertEquals('Type: Provider, Name: Foo, Value: Bar (ProviderName: FooBar, ActionName: BarBaz, SpecialtyName: FooBarBaz)', (string) $this->_metadata);
    }

}
