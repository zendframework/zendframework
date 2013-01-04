<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace ZendTest\XmlRpc\Server;

use Zend\XmlRpc\Server;

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @group      Zend_XmlRpc
 */
class FaultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend\XmlRpc\Server\Fault::getInstance() test
     */
    public function testGetInstance()
    {
        $e = new Server\Exception\RuntimeException('Testing fault', 411);
        $fault = Server\Fault::getInstance($e);

        $this->assertTrue($fault instanceof Server\Fault);
    }

    /**
     * Zend\XmlRpc\Server\Fault::attachFaultException() test
     */
    public function testAttachFaultException()
    {
        Server\Fault::attachFaultException('ZendTest\\XmlRpc\\Server\\Exception');
        $e = new Exception('test exception', 411);
        $fault = Server\Fault::getInstance($e);
        $this->assertEquals('test exception', $fault->getMessage());
        $this->assertEquals(411, $fault->getCode());
        Server\Fault::detachFaultException('ZendTest\\XmlRpc\\Server\\Exception');

        $exceptions = array(
            'ZendTest\\XmlRpc\\Server\\Exception',
            'ZendTest\\XmlRpc\\Server\\Exception2',
            'ZendTest\\XmlRpc\\Server\\Exception3',
        );
        Server\Fault::attachFaultException($exceptions);
        foreach ($exceptions as $class) {
            $e = new $class('test exception', 411);
            $fault = Server\Fault::getInstance($e);
            $this->assertEquals('test exception', $fault->getMessage());
            $this->assertEquals(411, $fault->getCode());
        }
        Server\Fault::detachFaultException($exceptions);
    }

    /**
     * Tests ZF-1825
     * @return void
     */
    public function testAttachFaultExceptionAllowsForDerivativeExceptionClasses()
    {
        Server\Fault::attachFaultException('ZendTest\\XmlRpc\\Server\\Exception');
        $e = new Exception4('test exception', 411);
        $fault = Server\Fault::getInstance($e);
        $this->assertEquals('test exception', $fault->getMessage());
        $this->assertEquals(411, $fault->getCode());
        Server\Fault::detachFaultException('ZendTest\\XmlRpc\\Server\\Exception');
    }

    /**
     * Zend_XmlRpc_Server_Fault::detachFaultException() test
     */
    public function testDetachFaultException()
    {
        Server\Fault::attachFaultException('ZendTest\\XmlRpc\\Server\\Exception');
        $e = new Exception('test exception', 411);
        $fault = Server\Fault::getInstance($e);
        $this->assertEquals('test exception', $fault->getMessage());
        $this->assertEquals(411, $fault->getCode());
        Server\Fault::detachFaultException('ZendTest\\XmlRpc\\Server\\Exception');
        $fault = Server\Fault::getInstance($e);
        $this->assertEquals('Unknown error', $fault->getMessage());
        $this->assertEquals(404, $fault->getCode());


        $exceptions = array(
            'ZendTest\\XmlRpc\\Server\\Exception',
            'ZendTest\\XmlRpc\\Server\\Exception2',
            'ZendTest\\XmlRpc\\Server\\Exception3'
        );
        Server\Fault::attachFaultException($exceptions);
        foreach ($exceptions as $class) {
            $e = new $class('test exception', 411);
            $fault = Server\Fault::getInstance($e);
            $this->assertEquals('test exception', $fault->getMessage());
            $this->assertEquals(411, $fault->getCode());
        }
        Server\Fault::detachFaultException($exceptions);
        foreach ($exceptions as $class) {
            $e = new $class('test exception', 411);
            $fault = Server\Fault::getInstance($e);
            $this->assertEquals('Unknown error', $fault->getMessage());
            $this->assertEquals(404, $fault->getCode());
        }
    }

    /**
     * Zend_XmlRpc_Server_Fault::attachObserver() test
     */
    public function testAttachObserver()
    {
        Server\Fault::attachObserver('ZendTest\\XmlRpc\\Server\\Observer');
        $e = new Server\Exception\RuntimeException('Checking observers', 411);
        $fault = Server\Fault::getInstance($e);
        $observed = Observer::getObserved();
        Observer::clearObserved();
        Server\Fault::detachObserver('ZendTest\\XmlRpc\\Server\\Observer');

        $this->assertTrue(!empty($observed));
        $f = array_shift($observed);
        $this->assertTrue($f instanceof Server\Fault);
        $this->assertEquals('Checking observers', $f->getMessage());
        $this->assertEquals(411, $f->getCode());

        $this->assertFalse(Server\Fault::attachObserver('foo'));
    }

    /**
     * Zend\XmlRpc\Server\Fault::detachObserver() test
     */
    public function testDetachObserver()
    {
        Server\Fault::attachObserver('ZendTest\\XmlRpc\\Server\\Observer');
        $e = new Server\Exception\RuntimeException('Checking observers', 411);
        $fault = Server\Fault::getInstance($e);
        Observer::clearObserved();
        Server\Fault::detachObserver('ZendTest\\XmlRpc\\Server\\Observer');

        $e = new Server\Exception\RuntimeException('Checking observers', 411);
        $fault = Server\Fault::getInstance($e);
        $observed = Observer::getObserved();
        $this->assertTrue(empty($observed));

        $this->assertFalse(Server\Fault::detachObserver('foo'));
    }

    /**
     * getCode() test
     */
    public function testGetCode()
    {
        $e = new Server\Exception\RuntimeException('Testing fault', 411);
        $fault = Server\Fault::getInstance($e);

        $this->assertEquals(411, $fault->getCode());
    }

    /**
     * getException() test
     */
    public function testGetException()
    {
        $e = new Server\Exception\RuntimeException('Testing fault', 411);
        $fault = Server\Fault::getInstance($e);

        $this->assertSame($e, $fault->getException());
    }

    /**
     * getMessage() test
     */
    public function testGetMessage()
    {
        $e = new Server\Exception\RuntimeException('Testing fault', 411);
        $fault = Server\Fault::getInstance($e);

        $this->assertEquals('Testing fault', $fault->getMessage());
    }

    /**
     * __toString() test
     */
    public function test__toString()
    {
        $dom  = new \DOMDocument('1.0', 'UTF-8');
        $r    = $dom->appendChild($dom->createElement('methodResponse'));
        $f    = $r->appendChild($dom->createElement('fault'));
        $v    = $f->appendChild($dom->createElement('value'));
        $s    = $v->appendChild($dom->createElement('struct'));

        $m1   = $s->appendChild($dom->createElement('member'));
        $m1->appendChild($dom->createElement('name', 'faultCode'));
        $cv   = $m1->appendChild($dom->createElement('value'));
        $cv->appendChild($dom->createElement('int', 411));

        $m2   = $s->appendChild($dom->createElement('member'));
        $m2->appendChild($dom->createElement('name', 'faultString'));
        $sv   = $m2->appendChild($dom->createElement('value'));
        $sv->appendChild($dom->createElement('string', 'Testing fault'));

        $xml = $dom->saveXML();

        $e = new Server\Exception\RuntimeException('Testing fault', 411);
        $fault = Server\Fault::getInstance($e);

        $this->assertEquals(trim($xml), trim($fault->__toString()));
    }
}

class Exception extends \Exception {}
class Exception2 extends \Exception {}
class Exception3 extends \Exception {}
class Exception4 extends Exception {}

class Observer
{
    private static $_instance = false;

    public $observed = array();

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function observe(Server\Fault $fault)
    {
        self::getInstance()->observed[] = $fault;
    }

    public static function clearObserved()
    {
        self::getInstance()->observed = array();
    }

    public static function getObserved()
    {
        return self::getInstance()->observed;
    }
}
