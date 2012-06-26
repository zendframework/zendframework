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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator;
use DateTime;
use ReflectionClass;

/**
 * Test helper
 */

/**
 * @see Zend_Validator_Date
 */

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend\Validator\Date object
     *
     * @var Validator\Date
     */
    protected $_validator;

    /**
     * Creates a new Zend\Validator\Date object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Validator\Date();
    }

    public function datesDataProvider()
    {
        return array(
            //    date                       format             isValid
            array('2007-01-01',              null,              true),
            array('2007-02-28',              null,              true),
            array('2007-02-29',              null,              false),
            array('2008-02-29',              null,              true),
            array('2007-02-30',              null,              false),
            array('2007-02-99',              null,              false),
            array('2007-02-99',              'Y-m-d',           false),
            array('9999-99-99',              null,              false),
            array('9999-99-99',              'Y-m-d',           false),
            array('Jan 1 2007',              null,              false),
            array('Jan 1 2007',              'M j Y',           true),
            array('asdasda',                 null,              false),
            array('sdgsdg',                  null,              false),
            array('2007-01-01something',     null,              false),
            array('something2007-01-01',     null,              false),
            array('10.01.2008',              'd.m.Y',           true),
            array('01 2010',                 'm Y',             true),
            array('2008/10/22',              'd/m/Y',           false),
            array('22/10/08',                'd/m/y',           true),
            array('22/10',                   'd/m/Y',           false),
            // time
            array('2007-01-01T12:02:55Z',    DateTime::ISO8601, true),
            array('12:02:55',                'H:i:s',           true),
            array('25:02:55',                'H:i:s',           false),
            // int
            array(0,                         null,              true),
            array(1340677235,                null,              true),
            array(999999999999,              null,              false),
            // array
            array(array('2012', '06', '25'), null,              true),
            array(array('12', '06', '25'),   null,              false),
            array(array(1 => 1),             null,              false),
            // DateTime
            array(new DateTime(),            null,              true),
            // invalid obj
            array(new \stdClass(),           null,              false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider datesDataProvider
     * @return void
     */
    public function testBasic($input, $format, $result)
    {
        $this->_validator->setFormat($format);
        $this->assertEquals($result, $this->_validator->isValid($input));
        $this->assertEquals($format, $this->_validator->getFormat());
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->_validator;
        $reflection = new ReflectionClass($validator);

        if (!$reflection->hasProperty('_messageTemplates')) {
            return;
        }

        $property = $reflection->getProperty('_messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->_validator;
        $reflection = new ReflectionClass($validator);

        if (!$reflection->hasProperty('_messageVariables')) {
            return;
        }

        $property = $reflection->getProperty('_messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }
}
