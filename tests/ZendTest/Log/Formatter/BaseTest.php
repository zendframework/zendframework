<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Formatter;

use DateTime;
use stdClass;
use EmptyIterator;
use ArrayIterator;
use ZendTest\Log\TestAsset\StringObject;
use Zend\Log\Formatter\Base as BaseFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultDateTimeFormat()
    {
        $formatter = new BaseFormatter();
        $this->assertEquals(BaseFormatter::DEFAULT_DATETIME_FORMAT, $formatter->getDateTimeFormat());
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testAllowsSpecifyingDateTimeFormatAsConstructorArgument($dateTimeFormat)
    {
        $formatter = new BaseFormatter($dateTimeFormat);

        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
    }

    /**
     * @return array
     */
    public function provideDateTimeFormats()
    {
        return array(
            array('r'),
            array('U'),
            array(DateTime::RSS),
        );
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testSetDateTimeFormat($dateTimeFormat)
    {
        $formatter = new BaseFormatter();
        $formatter->setDateTimeFormat($dateTimeFormat);

        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testSetDateTimeFormatInConstructor($dateTimeFormat)
    {
        $options = array('dateTimeFormat' => $dateTimeFormat);
        $formatter = new BaseFormatter($options);

        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
    }

    public function testFormatAllTypes()
    {
        $datetime = new DateTime();
        $object = new stdClass();
        $object->foo = 'bar';
        $formatter = new BaseFormatter();

        $event = array(
            'timestamp' => $datetime,
            'priority' => 1,
            'message' => 'tottakai',
            'extra' => array(
                'float' => 0.2,
                'boolean' => false,
                'array_empty' => array(),
                'array' => range(0, 4),
                'traversable_empty' => new EmptyIterator(),
                'traversable' => new ArrayIterator(array('id', 42)),
                'null' => null,
                'object_empty' => new stdClass(),
                'object' => $object,
                'string object' => new StringObject(),
                'resource' => fopen('php://stdout', 'w'),
            ),
        );
        $outputExpected = array(
            'timestamp' => $datetime->format($formatter->getDateTimeFormat()),
            'priority' => 1,
            'message' => 'tottakai',
            'extra' => array(
                'boolean' => false,
                'float' => 0.2,
                'array_empty' => '[]',
                'array' => '[0,1,2,3,4]',
                'traversable_empty' => '[]',
                'traversable' => '["id",42]',
                'null' => null,
                'object_empty' => 'object(stdClass) {}',
                'object' => 'object(stdClass) {"foo":"bar"}',
                'string object' => 'Hello World',
                'resource' => 'resource(stream)',
            ),
        );

        $this->assertEquals($outputExpected, $formatter->format($event));
    }
}
