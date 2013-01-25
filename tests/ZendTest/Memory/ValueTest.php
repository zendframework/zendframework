<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Memory
 */

namespace ZendTest\Memory;

use Zend\Memory;
use Zend\Memory\Container;

/**
 * @category   Zend
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @group      Zend_Memory
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests the Value object creation
     */
    public function testCreation()
    {
        $valueObject = new Memory\Value('data data data ...', new DummyMovableContainer());
        $this->assertTrue($valueObject instanceof Memory\Value);
        $this->assertEquals($valueObject->getRef(), 'data data data ...');
    }

    /**
     * tests the value reference retrieval
     */
    public function testGetRef()
    {
        $valueObject = new Memory\Value('0123456789', new DummyMovableContainer());
        $valueRef = &$valueObject->getRef();
        $valueRef[3] = '_';

        $this->assertEquals($valueObject->getRef(), '012_456789');
    }

    /**
     * tests the __toString() functionality
     */
    public function testToString()
    {
        $valueObject = new Memory\Value('0123456789', new DummyMovableContainer());
        $this->assertEquals($valueObject->__toString(), '0123456789');

        $this->assertEquals(strlen($valueObject), 10);
        $this->assertEquals((string)$valueObject, '0123456789');
    }

    /**
     * tests the access through ArrayAccess methods
     */
    public function testArrayAccess()
    {
        $valueObject = new Memory\Value('0123456789', new DummyMovableContainer());
        $this->assertEquals($valueObject[8], '8');

        $valueObject[2] = '_';
        $this->assertEquals((string)$valueObject, '01_3456789');


        $error_level = error_reporting();
        error_reporting($error_level & ~E_NOTICE);
        $valueObject[10] = '_';
        $this->assertEquals((string)$valueObject, '01_3456789_');
        error_reporting($error_level);
    }
}

class DummyMovableContainer extends Container\Movable
{
    /**
     * Empty constructor
     */
    public function __construct()
    {
        // Do nothing
    }

    /**
     * Dummy value update callback method
     */
    public function processUpdate()
    {
        // Do nothing
    }
}
