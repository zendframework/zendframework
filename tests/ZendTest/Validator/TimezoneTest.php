<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\Timezone;

/**
 * Tests for {@see \Zend\Validator\Timezone}
 *
 * @covers \Zend\Validator\Timezone
 */
class TimezoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Timezone
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new Timezone();
    }

    /**
     * Test locations
     *
     * @return void
     *
     * @dataProvider locationProvider
     */
    public function testLocations($value, $valid)
    {
        $this->validator->setType(Timezone::LOCATION);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Test locations by type is string
     *
     * @return void
     *
     * @dataProvider locationProvider
     */
    public function testLocationsByTypeAsString($value, $valid)
    {
        $this->validator->setType('location');
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides location values
     *
     * @return array
     */
    public function locationProvider()
    {
        return array(
            array('America/Anguilla', true),
            array('Antarctica/Palmer', true),
            array('Asia/Dubai', true),
            array('Atlantic/Cape_Verde', true),
            array('Australia/Broken_Hill', true),
            array('America/Sao_Paulo', true),
            array('America/Toronto', true),
            array('Pacific/Easter', true),
            array('Europe/Copenhagen', true),
            array('Indian/Maldives', true),

            array('anast', false),              // abbreviation of Anadyr Summer Time

            array('Asia/London', false),        // wrong location
            array('', false),                   // empty string
            array(null, false),                 // null value
        );
    }

    /**
     * Test abbreviations
     *
     * @return void
     *
     * @dataProvider abbreviationProvider
     */
    public function testAbbreviations($value, $valid)
    {
        $this->validator->setType(Timezone::ABBREVIATION);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Test abbreviations byTypeAsString
     *
     * @return void
     *
     * @dataProvider abbreviationProvider
     */
    public function testAbbreviationsByTypeAsString($value, $valid)
    {
        $this->validator->setType('abbreviation');
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides abbreviation values
     *
     * @return array
     */
    public function abbreviationProvider()
    {
        return array(
            array('anast', true),               // Anadyr Summer Time
            array('bnt', true),                 // Brunei Darussalam Time
            array('cest', true),                // Central European Summer Time
            array('easst', true),               // Easter Island Summer Time
            array('egst', true),                // Eastern Greenland Summer Time
            array('hkt', true),                 // Hong Kong Time
            array('irkst', true),               // Irkutsk Summer Time
            array('krast', true),               // Krasnoyarsk Summer Time
            array('nzdt', true),                // New Zealand Daylight Time
            array('sast', true),                // South Africa Standard Time

            array('America/Toronto', false),    // location

            array('xyz', false),                // wrong abbreviation
            array('', false),                   // empty string
            array(null, false),                 // null value
        );
    }

    /**
     * Test locations and abbreviations
     *
     * @return void
     *
     * @dataProvider locationAndAbbreviationProvider
     */
    public function testlocationsAndAbbreviationsWithAllTypeAsString($value, $valid)
    {
        $this->validator->setType(Timezone::ALL);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Test locations and abbreviations
     *
     * @return void
     *
     * @dataProvider locationAndAbbreviationProvider
     */
    public function testlocationsAndAbbreviationsWithAllTypeAsArray($value, $valid)
    {
        $this->validator->setType(array(Timezone::LOCATION, Timezone::ABBREVIATION));
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Test locations and abbreviations
     *
     * @return void
     *
     * @dataProvider locationAndAbbreviationProvider
     */
    public function testlocationsAndAbbreviationsWithAllTypeAsArrayWithStrings($value, $valid)
    {
        $this->validator->setType(array('location', 'abbreviation'));
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides location and abbreviation values
     *
     * @return array
     */
    public function locationAndAbbreviationProvider()
    {
        return array(
            array('America/Anguilla', true),
            array('Antarctica/Palmer', true),
            array('Asia/Dubai', true),
            array('Atlantic/Cape_Verde', true),
            array('Australia/Broken_Hill', true),

            array('hkt', true),                 // Hong Kong Time
            array('irkst', true),               // Irkutsk Summer Time
            array('krast', true),               // Krasnoyarsk Summer Time
            array('nzdt', true),                // New Zealand Daylight Time
            array('sast', true),                // South Africa Standard Time

            array('xyz', false),                // wrong abbreviation
            array('Asia/London', false),        // wrong location

            array('', false),                   // empty string
            array(null, false),                 // null value
        );
    }

    /**
     * Test wrong type
     *
     * @return void
     *
     * @dataProvider wrongTypesProvider
     */
    public function testWrongType($value)
    {
        $this->checkExpectedException($value);
    }

    /**
     * Provides wrong types
     *
     * @return array
     */
    public function wrongTypesProvider()
    {
        return array(
            array(null),
            array(''),
            array(array()),
            array(0),
            array(4),
        );
    }

    /**
     * Test pass `type` option through constructor
     *
     *  @return void
     */
    public function testTypeThroughConstructor()
    {
        $timezone1 = new Timezone(Timezone::LOCATION);
        $this->assertTrue($timezone1->isValid('Asia/Dubai'));
        $this->assertFalse($timezone1->isValid('sast'));

        $timezone2 = new Timezone('location');
        $this->assertTrue($timezone2->isValid('Asia/Dubai'));
        $this->assertFalse($timezone2->isValid('sast'));

        $timezone3 = new Timezone(array('type' => 'location'));
        $this->assertTrue($timezone3->isValid('Asia/Dubai'));
        $this->assertFalse($timezone3->isValid('sast'));

        $timezone4 = new Timezone(Timezone::ABBREVIATION);
        $this->assertFalse($timezone4->isValid('Asia/Dubai'));
        $this->assertTrue($timezone4->isValid('sast'));

        $timezone5 = new Timezone('abbreviation');
        $this->assertFalse($timezone5->isValid('Asia/Dubai'));
        $this->assertTrue($timezone5->isValid('sast'));

        $timezone6 = new Timezone(array('type' => 'abbreviation'));
        $this->assertFalse($timezone6->isValid('Asia/Dubai'));
        $this->assertTrue($timezone6->isValid('sast'));

        // default value is `all`
        $timezone7 = new Timezone();
        $this->assertTrue($timezone7->isValid('Asia/Dubai'));
        $this->assertTrue($timezone7->isValid('sast'));

        $timezone8 = new Timezone(array('type' => array('location', 'abbreviation')));
        $this->assertTrue($timezone8->isValid('Asia/Dubai'));
        $this->assertTrue($timezone8->isValid('sast'));
    }

    /**
     * @param mixed $invalidType
     *
     * @dataProvider getInvalidTypes
     */
    public function testRejectsInvalidIntType($invalidType)
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException');

        new Timezone(array('type' => $invalidType));
    }

    /**
     * Checks that the validation value matches the expected validity
     *
     * @param mixed $value Value to validate
     * @param bool  $valid Expected validity
     *
     * @return void
     */
    protected function checkValidationValue($value, $valid)
    {
        $isValid = $this->validator->isValid($value);

        if ($valid) {
            $this->assertTrue($isValid);
        } else {
            $this->assertFalse($isValid);
        }
    }

    /**
     * Checks expected exception on wrong type
     *
     * @param mixed $value Value to validate
     *
     * @return void
     */
    protected function checkExpectedException($value)
    {
        $this->setExpectedException('\Zend\Validator\Exception\InvalidArgumentException');
        $this->validator->setType($value);
    }

    /**
     * Data provider
     *
     * @return mixed[][]
     */
    public function getInvalidTypes()
    {
        return array(
            array(new \stdClass()),
            array(array()),
            array(0),
            array(10),
            array('foo'),
        );
    }
}
