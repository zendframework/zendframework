<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use ArrayObject;
use Zend\Stdlib\Exception;
use ZendTest\Stdlib\TestAsset\TestOptions;
use ZendTest\Stdlib\TestAsset\TestOptionsDerived;
use ZendTest\Stdlib\TestAsset\TestOptionsNoStrict;
use ZendTest\Stdlib\TestAsset\TestOptionsWithoutGetter;

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

    public function testConstructionWithOptions()
    {
        $options = new TestOptions(new TestOptions(array('test_field' => 1)));

        $this->assertEquals(1, $options->test_field);
    }

    public function testInvalidFieldThrowsException()
    {
        $this->setExpectedException('BadMethodCallException');

        new TestOptions(array('foo' => 'bar'));
    }

    public function testNonStrictOptionsDoesNotThrowException()
    {
        $this->assertInstanceOf(
            'ZendTest\Stdlib\TestAsset\TestOptionsNoStrict',
            new TestOptionsNoStrict(array('foo' => 'bar'))
        );
    }

    public function testConstructionWithNull()
    {
        $this->assertInstanceOf('ZendTest\Stdlib\TestAsset\TestOptions', new TestOptions(null));
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
        $options = new TestOptions;

        $this->setExpectedException('InvalidArgumentException');

        unset($options->foobarField);
    }

    public function testGetThrowsBadMethodCallException()
    {
        $options = new TestOptions();

        $this->setExpectedException('BadMethodCallException');

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

    public function testParentPublicProperty()
    {
        $options = new TestOptionsDerived(array('parent_public' => 1));

        $this->assertEquals(1, $options->parent_public);
    }

    public function testParentProtectedProperty()
    {
        $options = new TestOptionsDerived(array('parent_protected' => 1));

        $this->assertEquals(1, $options->parent_protected);
    }

    public function testParentPrivateProperty()
    {
        $this->setExpectedException(
            'Zend\Stdlib\Exception\BadMethodCallException',
            'The option "parent_private" does not have a callable "setParentPrivate" ("setparentprivate")'
            . ' setter method which must be defined'
        );

        new TestOptionsDerived(array('parent_private' => 1));
    }

    public function testDerivedPublicProperty()
    {
        $options = new TestOptionsDerived(array('derived_public' => 1));

        $this->assertEquals(1, $options->derived_public);
    }

    public function testDerivedProtectedProperty()
    {
        $options = new TestOptionsDerived(array('derived_protected' => 1));

        $this->assertEquals(1, $options->derived_protected);
    }

    public function testDerivedPrivateProperty()
    {
        $this->setExpectedException(
            'Zend\Stdlib\Exception\BadMethodCallException',
            'The option "derived_private" does not have a callable "setDerivedPrivate" ("setderivedprivate")'
            .' setter method which must be defined'
        );

        new TestOptionsDerived(array('derived_private' => 1));
    }

    public function testExceptionMessageContainsActualUsedSetter()
    {
        $this->setExpectedException(
            'BadMethodCallException',
            'The option "foo bar" does not have a callable "setFooBar" ("setfoo bar")'
            . ' setter method which must be defined'
        );

        new TestOptions(array(
            'foo bar' => 'baz',
        ));
    }

    /**
     * @group 7287
     */
    public function testIssetReturnsFalseWhenMatchingGetterDoesNotExist()
    {
        $options = new TestOptionsWithoutGetter(array(
            'foo' => 'bar',
        ));
        $this->assertFalse(isset($options->foo));
    }

    /**
     * @group 7287
     */
    public function testIssetDoesNotThrowExceptionWhenMatchingGetterDoesNotExist()
    {
        $options   = new TestOptionsWithoutGetter();

        try {
            isset($options->foo);
        } catch (Exception\BadMethodCallException $exception) {
            $this->fail("Unexpected BadMethodCallException raised");
        }
    }

    /**
     * @group 7287
     */
    public function testIssetReturnsTrueWithValidDataWhenMatchingGetterDoesNotExist()
    {
        $options = new TestOptions(array(
            'test_field' => 1,
        ));
        $this->assertTrue(isset($options->testField));
    }
}
