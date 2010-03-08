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
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @package Zend_Soap
 * @subpackage UnitTests
 */


/** Zend_Soap_Wsdl */


/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class Zend_Soap_Wsdl_CompositeStrategyTest extends PHPUnit_Framework_TestCase
{
    public function testCompositeApiAddingStragiesToTypes()
    {
        $strategy = new Zend_Soap_Wsdl_Strategy_Composite(array(), "Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence");
        $strategy->connectTypeToStrategy("Book", "Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex");

        $bookStrategy = $strategy->getStrategyOfType("Book");
        $cookieStrategy = $strategy->getStrategyOfType("Cookie");

        $this->assertTrue( $bookStrategy instanceof Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex );
        $this->assertTrue( $cookieStrategy instanceof Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence );
    }

    public function testConstructorTypeMapSyntax()
    {
        $typeMap = array("Book" => "Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex");

        $strategy = new Zend_Soap_Wsdl_Strategy_Composite($typeMap, "Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence");

        $bookStrategy = $strategy->getStrategyOfType("Book");
        $cookieStrategy = $strategy->getStrategyOfType("Cookie");

        $this->assertTrue( $bookStrategy instanceof Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex );
        $this->assertTrue( $cookieStrategy instanceof Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence );
    }

    public function testCompositeThrowsExceptionOnInvalidType()
    {
        $strategy = new Zend_Soap_Wsdl_Strategy_Composite();
        try {
            $strategy->connectTypeToStrategy(array(), "strategy");
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_Wsdl_Exception);
        }
    }

    public function testCompositeThrowsExceptionOnInvalidStrategy()
    {
        $strategy = new Zend_Soap_Wsdl_Strategy_Composite(array(), "invalid");
        $strategy->connectTypeToStrategy("Book", "strategy");

        try {
            $book = $strategy->getStrategyOfType("Book");
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_Wsdl_Exception);
        }

        try {
            $book = $strategy->getStrategyOfType("Anything");
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_Wsdl_Exception);
        }
    }

    public function testCompositeDelegatesAddingComplexTypesToSubStrategies()
    {
        $strategy = new Zend_Soap_Wsdl_Strategy_Composite(array(), "Zend_Soap_Wsdl_Strategy_AnyType");
        $strategy->connectTypeToStrategy("Zend_Soap_Wsdl_Book", "Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex");
        $strategy->connectTypeToStrategy("Zend_Soap_Wsdl_Cookie", "Zend_Soap_Wsdl_Strategy_DefaultComplexType");

        $wsdl = new Zend_Soap_Wsdl("SomeService", "http://example.com");
        $strategy->setContext($wsdl);

        $this->assertEquals("tns:Zend_Soap_Wsdl_Book", $strategy->addComplexType("Zend_Soap_Wsdl_Book"));
        $this->assertEquals("tns:Zend_Soap_Wsdl_Cookie", $strategy->addComplexType("Zend_Soap_Wsdl_Cookie"));
        $this->assertEquals("xsd:anyType", $strategy->addComplexType("Zend_Soap_Wsdl_Anything"));
    }

    public function testCompositeRequiresContextForAddingComplexTypesOtherwiseThrowsException()
    {
        $strategy = new Zend_Soap_Wsdl_Strategy_Composite();
        try {
            $strategy->addComplexType("Test");
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_Wsdl_Exception);
        }
    }
}

class Zend_Soap_Wsdl_Book
{
    /**
     * @var int
     */
    public $somevar;
}
class Zend_Soap_Wsdl_Cookie
{
    /**
     * @var int
     */
    public $othervar;
}
class Zend_Soap_Wsdl_Anything
{
}
