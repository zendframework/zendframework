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
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_Memory */
require_once 'Zend/Memory.php';

/**
 * @category   Zend
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Memory_Container_Movable_Dummy extends Zend_Memory_Container_Movable
{
    /**
     * Dummy object constructor
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


/**
 * @package    Zend_Memory
 * @subpackage UnitTests
 */
class Zend_Memory_ValueTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests the Value object creation
     */
    public function testCreation()
    {
        $valueObject = new Zend_Memory_Value('data data data ...', new Zend_Memory_Container_Movable_Dummy());
        $this->assertTrue($valueObject instanceof Zend_Memory_Value);
        $this->assertEquals($valueObject->getRef(), 'data data data ...');
    }


    /**
     * tests the value reference retrieval
     */
    public function testGetRef()
    {
        $valueObject = new Zend_Memory_Value('0123456789', new Zend_Memory_Container_Movable_Dummy());
        $valueRef = &$valueObject->getRef();
        $valueRef[3] = '_';

        $this->assertEquals($valueObject->getRef(), '012_456789');
    }


    /**
     * tests the __toString() functionality
     */
    public function testToString()
    {
        $valueObject = new Zend_Memory_Value('0123456789', new Zend_Memory_Container_Movable_Dummy());
        $this->assertEquals($valueObject->__toString(), '0123456789');

        if (version_compare(PHP_VERSION, '5.2') < 0) {
            // Skip following tests for PHP versions before 5.2
            return;
        }

        $this->assertEquals(strlen($valueObject), 10);
        $this->assertEquals((string)$valueObject, '0123456789');
    }

    /**
     * tests the access through ArrayAccess methods
     */
    public function testArrayAccess()
    {
        if (version_compare(PHP_VERSION, '5.2') < 0) {
            // Skip following tests for PHP versions before 5.2
            return;
        }

        $valueObject = new Zend_Memory_Value('0123456789', new Zend_Memory_Container_Movable_Dummy());
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
