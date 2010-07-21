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
 * @namespace
 */
namespace ZendTest\Soap\WSDL;
use Zend\Soap\WSDL\Strategy,
    Zend\Soap\WSDL,
    Zend\Soap\WSDLException;

/**
 * @package Zend_Soap
 * @subpackage UnitTests
 */


/** Zend_Soap_WSDL */


/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_WSDL
 */
class CompositeStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testCompositeApiAddingStragiesToTypes()
    {
        $strategy = new Strategy\Composite(array(), '\Zend\Soap\WSDL\Strategy\ArrayOfTypeSequence');
        $strategy->connectTypeToStrategy('Book', '\Zend\Soap\WSDL\Strategy\ArrayOfTypeComplex');

        $bookStrategy = $strategy->getStrategyOfType('Book');
        $cookieStrategy = $strategy->getStrategyOfType('Cookie');

        $this->assertTrue( $bookStrategy instanceof Strategy\ArrayOfTypeComplex );
        $this->assertTrue( $cookieStrategy instanceof Strategy\ArrayOfTypeSequence );
    }

    public function testConstructorTypeMapSyntax()
    {
        $typeMap = array('Book' => '\Zend\Soap\WSDL\Strategy\ArrayOfTypeComplex');

        $strategy = new Strategy\Composite($typeMap, '\Zend\Soap\WSDL\Strategy\ArrayOfTypeSequence');

        $bookStrategy = $strategy->getStrategyOfType('Book');
        $cookieStrategy = $strategy->getStrategyOfType('Cookie');

        $this->assertTrue( $bookStrategy instanceof Strategy\ArrayOfTypeComplex );
        $this->assertTrue( $cookieStrategy instanceof Strategy\ArrayOfTypeSequence );
    }

    public function testCompositeThrowsExceptionOnInvalidType()
    {
        $strategy = new Strategy\Composite();
        try {
            $strategy->connectTypeToStrategy(array(), 'strategy');
            $this->fail();
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof WSDLException);
        }
    }

    public function testCompositeThrowsExceptionOnInvalidStrategy()
    {
        $strategy = new Strategy\Composite(array(), 'invalid');
        $strategy->connectTypeToStrategy('Book', 'strategy');

        try {
            $book = $strategy->getStrategyOfType('Book');
            $this->fail();
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof WSDLException);
        }

        try {
            $book = $strategy->getStrategyOfType('Anything');
            $this->fail();
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof WSDLException);
        }
    }

    public function testCompositeDelegatesAddingComplexTypesToSubStrategies()
    {
        $strategy = new Strategy\Composite(array(), '\Zend\Soap\WSDL\Strategy\AnyType');
        $strategy->connectTypeToStrategy('\ZendTest\Soap\WSDL\Book',   '\Zend\Soap\WSDL\Strategy\ArrayOfTypeComplex');
        $strategy->connectTypeToStrategy('\ZendTest\Soap\WSDL\Cookie', '\Zend\Soap\WSDL\Strategy\DefaultComplexType');

        $wsdl = new WSDL('SomeService', 'http://example.com');
        $strategy->setContext($wsdl);

        $this->assertEquals('tns:ZendTest.Soap.WSDL.Book',   $strategy->addComplexType('\ZendTest\Soap\WSDL\Book'));
        $this->assertEquals('tns:ZendTest.Soap.WSDL.Cookie', $strategy->addComplexType('\ZendTest\Soap\WSDL\Cookie'));
        $this->assertEquals('xsd:anyType', $strategy->addComplexType('\ZendTest\Soap\WSDL\Anything'));
    }

    public function testCompositeRequiresContextForAddingComplexTypesOtherwiseThrowsException()
    {
        $strategy = new Strategy\Composite();
        try {
            $strategy->addComplexType('Test');
            $this->fail();
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof WSDLException);
        }
    }
}

class Book
{
    /**
     * @var int
     */
    public $somevar;
}
class Cookie
{
    /**
     * @var int
     */
    public $othervar;
}
class Anything
{
}
