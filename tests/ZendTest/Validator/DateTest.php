<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use DateTime;
use stdClass;
use Zend\Validator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Validator\Date
     */
    protected $validator;

    /**
     * Creates a new Zend\Validator\Date object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Validator\Date();
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
            // Commenting out, as value appears to vary based on OS
            // array(999999999999,              null,              true),
            // array
            array(array('2012', '06', '25'), null,              true),
            array(array('12', '06', '25'),   null,              false),
            array(array(1 => 1),             null,              false),
            // DateTime
            array(new DateTime(),            null,              true),
            // invalid obj
            array(new stdClass(),           null,              false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider datesDataProvider
     */
    public function testBasic($input, $format, $result)
    {
        $this->validator->setFormat($format);
        $this->assertEquals($result, $this->validator->isValid($input));
        $this->assertEquals($format, $this->validator->getFormat());
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures that the validator can handle different manual dateformats
     *
     * @group  ZF-2003
     * @return void
     */
    public function testUseManualFormat()
    {
        $this->assertTrue($this->validator->setFormat('d.m.Y')->isValid('10.01.2008'), var_export(date_get_last_errors(), 1));
        $this->assertEquals('d.m.Y', $this->validator->getFormat());

        $this->assertTrue($this->validator->setFormat('m Y')->isValid('01 2010'));
        $this->assertFalse($this->validator->setFormat('d/m/Y')->isValid('2008/10/22'));
        $this->assertTrue($this->validator->setFormat('d/m/Y')->isValid('22/10/08'));
        $this->assertFalse($this->validator->setFormat('d/m/Y')->isValid('22/10'));
        // Omitting the following assertion, as it varies from 5.3.3 to 5.3.11,
        // and there is no indication in the PHP changelog as to when or why it
        // may have changed. Leaving for posterity, to indicate original expectation.
        // $this->assertFalse($this->validator->setFormat('s')->isValid(0));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
