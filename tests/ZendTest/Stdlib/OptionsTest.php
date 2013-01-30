<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use ArrayObject;
use ZendTest\Stdlib\TestAsset\TestOptions;
use ZendTest\Stdlib\TestAsset\TestOptionsNoStrict;
use Zend\Stdlib\Exception\InvalidArgumentException;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructionWithArray()
    {
        $options = new TestOptions(array('test_field' => 1));

        $this->assertEquals(1, $options->test_field);
    }

    public function testConstructionWithTraversable()
    {
        $config = new ArrayObject(array('test_field' => 1));
        $options = new TestOptions($config);

        $this->assertEquals(1, $options->test_field);
    }

    public function testInvalidFieldThrowsException()
    {
        $this->setExpectedException('BadMethodCallException');
        $options = new TestOptions(array('foo' => 'bar'));
    }

    public function testNonStrictOptionsDoesNotThrowException()
    {
        try {
            $options = new TestOptionsNoStrict(array('foo' => 'bar'));
        } catch (\Exception $e) {
            $this->fail('Nonstrict options should not throw an exception');
        }
    }

    public function testConstructionWithNull()
    {
        try {
            $options = new TestOptions(null);
        } catch (InvalidArgumentException $e) {
            $this->fail("Unexpected InvalidArgumentException raised");
        }
    }

    public function testUnsetting()
    {
        $options = new TestOptions(array('test_field' => 1));

        $this->assertEquals(true, isset($options->test_field));
        unset($options->testField);
        $this->assertEquals(false, isset($options->test_field));
    }

    public function testUnsetThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new TestOptions;
        unset($options->foobarField);
    }

    public function testGetThrowsBadMethodCallException()
    {
        $this->setExpectedException('BadMethodCallException');
        $options = new TestOptions();
        $options->fieldFoobar;
    }

    public function testSetFromArrayAcceptsArray()
    {
        $array = array('test_field' => 3);
        $options = new TestOptions();

        $this->assertSame($options, $options->setFromArray($array));
        $this->assertEquals(3, $options->test_field);
    }

    public function testSetFromArrayThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new TestOptions;
        $options->setFromArray('asd');
    }
}
