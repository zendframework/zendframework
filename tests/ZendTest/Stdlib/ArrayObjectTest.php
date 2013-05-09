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

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\ArrayObject;

class ArrayObjectTest extends TestCase
{

    public function testConstructorDefaults()
    {
        $ar = new ArrayObject();
        $this->assertEquals(ArrayObject::STD_PROP_LIST, $ar->getFlags());
        $this->assertEquals('ArrayIterator', $ar->getIteratorClass());
        $this->assertInstanceOf('ArrayIterator', $ar->getIterator());
        $this->assertSame(array(), $ar->getArrayCopy());
        $this->assertEquals(0, $ar->count());
    }

    public function testConstructorParameters()
    {
        $ar = new ArrayObject(array('foo' => 'bar'), ArrayObject::ARRAY_AS_PROPS, 'RecursiveArrayIterator');
        $this->assertEquals(ArrayObject::ARRAY_AS_PROPS, $ar->getFlags());
        $this->assertEquals('RecursiveArrayIterator', $ar->getIteratorClass());
        $this->assertInstanceOf('RecursiveArrayIterator', $ar->getIterator());
        $this->assertSame(array('foo' => 'bar'), $ar->getArrayCopy());
        $this->assertEquals(1, $ar->count());
        $this->assertSame('bar', $ar->foo);
        $this->assertSame('bar', $ar['foo']);
    }

    public function testStdPropList()
    {
        $ar = new ArrayObject();
        $ar->foo = 'bar';
        $ar->bar = 'baz';
        $this->assertSame('bar', $ar->foo);
        $this->assertSame('baz', $ar->bar);
        $this->assertFalse(isset($ar['foo']));
        $this->assertFalse(isset($ar['bar']));
        $this->assertEquals(0, $ar->count());
        $this->assertSame(array(), $ar->getArrayCopy());
    }

    public function testStdPropListCannotAccessObjectVars()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Behavior is for overwritten ArrayObject in greater than 5.3.3');
        }
        $this->setExpectedException('InvalidArgumentException');
        $ar = new ArrayObject();
        $ar->flag;
    }

    public function testStdPropListStillHandlesArrays()
    {
        $ar = new ArrayObject();
        $ar->foo = 'bar';
        $ar['foo'] = 'baz';

        $this->assertSame('bar', $ar->foo);
        $this->assertSame('baz', $ar['foo']);
        $this->assertEquals(1, $ar->count());
    }

    public function testArrayAsProps()
    {
        $ar = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $ar->foo = 'bar';
        $ar['foo'] = 'baz';
        $ar->bar = 'foo';
        $ar['baz'] = 'bar';

        $this->assertSame('baz', $ar->foo);
        $this->assertSame('baz', $ar['foo']);
        $this->assertSame($ar->foo, $ar['foo']);
        $this->assertEquals(3, $ar->count());
    }

    public function testAppend()
    {
        $ar = new ArrayObject(array('one', 'two'));
        $this->assertEquals(2, $ar->count());

        $ar->append('three');

        $this->assertSame('three', $ar[2]);
        $this->assertEquals(3, $ar->count());
    }

    public function testAsort()
    {
        $ar = new ArrayObject(array('d' => 'lemon', 'a' => 'orange', 'b' => 'banana', 'c' => 'apple'));
        $sorted = $ar->getArrayCopy();
        asort($sorted);
        $ar->asort();
        $this->assertSame($sorted, $ar->getArrayCopy());
    }

    public function testCount()
    {
        $ar = new ArrayObject(new TestAsset\ArrayObjectObjectVars());
        $this->assertEquals(1, $ar->count());
    }

    public function testExchangeArray()
    {
        $ar = new ArrayObject(array('foo' => 'bar'));
        $old = $ar->exchangeArray(array('bar' => 'baz'));

        $this->assertSame(array('foo' => 'bar'), $old);
        $this->assertSame(array('bar' => 'baz'), $ar->getArrayCopy());
    }

    public function testExchangeArrayPhpArrayObject()
    {
        $ar = new ArrayObject(array('foo' => 'bar'));
        $old = $ar->exchangeArray(new \ArrayObject(array('bar' => 'baz')));

        $this->assertSame(array('foo' => 'bar'), $old);
        $this->assertSame(array('bar' => 'baz'), $ar->getArrayCopy());
    }

    public function testExchangeArrayStdlibArrayObject()
    {
        $ar = new ArrayObject(array('foo' => 'bar'));
        $old = $ar->exchangeArray(new ArrayObject(array('bar' => 'baz')));

        $this->assertSame(array('foo' => 'bar'), $old);
        $this->assertSame(array('bar' => 'baz'), $ar->getArrayCopy());
    }

    public function testExchangeArrayTestAssetIterator()
    {
        $ar = new ArrayObject();
        $ar->exchangeArray(new TestAsset\ArrayObjectIterator(array('foo' => 'bar')));

        // make sure it does what php array object does:
        $ar2 = new \ArrayObject();
        $ar2->exchangeArray(new TestAsset\ArrayObjectIterator(array('foo' => 'bar')));

        $this->assertEquals($ar2->getArrayCopy(), $ar->getArrayCopy());
    }

    public function testExchangeArrayArrayIterator()
    {
        $ar = new ArrayObject();
        $ar->exchangeArray(new \ArrayIterator(array('foo' => 'bar')));

        $this->assertEquals(array('foo' => 'bar'), $ar->getArrayCopy());
    }

    public function testExchangeArrayStringArgumentFail()
    {
        $this->setExpectedException('InvalidArgumentException');
        $ar     = new ArrayObject(array('foo' => 'bar'));
        $old    = $ar->exchangeArray('Bacon');
    }

    public function testGetArrayCopy()
    {
        $ar = new ArrayObject(array('foo' => 'bar'));
        $this->assertSame(array('foo' => 'bar'), $ar->getArrayCopy());
    }

    public function testFlags()
    {
        $ar = new ArrayObject();
        $this->assertEquals(ArrayObject::STD_PROP_LIST, $ar->getFlags());
        $ar = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $this->assertEquals(ArrayObject::ARRAY_AS_PROPS, $ar->getFlags());

        $ar->setFlags(ArrayObject::STD_PROP_LIST);
        $this->assertEquals(ArrayObject::STD_PROP_LIST, $ar->getFlags());
        $ar->setFlags(ArrayObject::ARRAY_AS_PROPS);
        $this->assertEquals(ArrayObject::ARRAY_AS_PROPS, $ar->getFlags());
    }

    public function testIterator()
    {
        $ar = new ArrayObject(array('1' => 'one', '2' => 'two', '3' => 'three'));
        $iterator = $ar->getIterator();
        $iterator2 = new \ArrayIterator($ar->getArrayCopy());
        $this->assertEquals($iterator2->getArrayCopy(), $iterator->getArrayCopy());
    }

    public function testIteratorClass()
    {
        $ar = new ArrayObject(array(), ArrayObject::STD_PROP_LIST, 'RecursiveArrayIterator');
        $this->assertEquals('RecursiveArrayIterator', $ar->getIteratorClass());
        $ar = new ArrayObject(array(), ArrayObject::STD_PROP_LIST, 'ArrayIterator');
        $this->assertEquals('ArrayIterator', $ar->getIteratorClass());
        $ar->setIteratorClass('RecursiveArrayIterator');
        $this->assertEquals('RecursiveArrayIterator', $ar->getIteratorClass());
        $ar->setIteratorClass('ArrayIterator');
        $this->assertEquals('ArrayIterator', $ar->getIteratorClass());
    }

    public function testInvalidIteratorClassThrowsInvalidArgumentException()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Behavior is for overwritten ArrayObject in greater than 5.3.3');
        }
        $this->setExpectedException('InvalidArgumentException');
        $ar = new ArrayObject(array(), ArrayObject::STD_PROP_LIST, 'InvalidArrayIterator');
    }

    public function testKsort()
    {
        $ar = new ArrayObject(array('d' => 'lemon', 'a' => 'orange', 'b' => 'banana', 'c' => 'apple'));
        $sorted = $ar->getArrayCopy();
        ksort($sorted);
        $ar->ksort();
        $this->assertSame($sorted, $ar->getArrayCopy());
    }

    public function testNatcasesort()
    {
        $ar = new ArrayObject(array('IMG0.png', 'img12.png', 'img10.png', 'img2.png', 'img1.png', 'IMG3.png'));
        $sorted = $ar->getArrayCopy();
        natcasesort($sorted);
        $ar->natcasesort();
        $this->assertSame($sorted, $ar->getArrayCopy());
    }

    public function testNatsort()
    {
        $ar = new ArrayObject(array('img12.png', 'img10.png', 'img2.png', 'img1.png'));
        $sorted = $ar->getArrayCopy();
        natsort($sorted);
        $ar->natsort();
        $this->assertSame($sorted, $ar->getArrayCopy());
    }

    public function testOffsetExists()
    {
        $ar = new ArrayObject();
        $ar['foo'] = 'bar';
        $ar->bar = 'baz';

        $this->assertTrue($ar->offsetExists('foo'));
        $this->assertFalse($ar->offsetExists('bar'));
        $this->assertTrue(isset($ar->bar));
        $this->assertFalse(isset($ar->foo));
    }

    public function testOffsetExistsThrowsExceptionOnProtectedProperty()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Behavior is for overwritten ArrayObject in greater than 5.3.3');
        }
        $this->setExpectedException('InvalidArgumentException');
        $ar = new ArrayObject();
        isset($ar->protectedProperties);
    }

    public function testOffsetGetOffsetSet()
    {
        $ar = new ArrayObject();
        $ar['foo'] = 'bar';
        $ar->bar = 'baz';

        $this->assertSame('bar', $ar['foo']);
        $this->assertSame('baz', $ar->bar);
        $this->assertFalse(isset($ar->unknown));
        $this->assertFalse(isset($ar['unknown']));
    }

    public function testOffsetGetThrowsExceptionOnProtectedProperty()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Behavior is for overwritten ArrayObject in greater than 5.3.3');
        }
        $this->setExpectedException('InvalidArgumentException');
        $ar = new ArrayObject();
        $ar->protectedProperties;
    }

    public function testOffsetSetThrowsExceptionOnProtectedProperty()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Behavior is for overwritten ArrayObject in greater than 5.3.3');
        }
        $this->setExpectedException('InvalidArgumentException');
        $ar = new ArrayObject();
        $ar->protectedProperties = null;
    }

    public function testOffsetUnset()
    {
        $ar = new ArrayObject();
        $ar['foo'] = 'bar';
        $ar->bar = 'foo';
        unset($ar['foo']);
        unset($ar->bar);
        $this->assertFalse(isset($ar['foo']));
        $this->assertFalse(isset($ar->bar));
        $this->assertSame(array(), $ar->getArrayCopy());
    }

    public function testOffsetUnsetMultidimensional()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Behavior is for overwritten ArrayObject in greater than 5.3.3');
        }
        $ar = new ArrayObject();
        $ar['foo'] = array('bar' => array('baz' => 'boo'));
        unset($ar['foo']['bar']['baz']);
    }

    public function testOffsetUnsetThrowsExceptionOnProtectedProperty()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Behavior is for overwritten ArrayObject in greater than 5.3.3');
        }
        $this->setExpectedException('InvalidArgumentException');
        $ar = new ArrayObject();
        unset($ar->protectedProperties);
    }

    public function testSerializeUnserialize()
    {
        $ar = new ArrayObject();
        $ar->foo = 'bar';
        $ar['bar'] = 'foo';
        $serialized = $ar->serialize();

        $ar = new ArrayObject();
        $ar->unserialize($serialized);

        $this->assertSame('bar', $ar->foo);
        $this->assertSame('foo', $ar['bar']);
    }

    public function testUasort()
    {
        $function = function ($a, $b) {
            if ($a == $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        };
        $ar = new ArrayObject(array('a' => 4, 'b' => 8, 'c' => -1, 'd' => -9, 'e' => 2, 'f' => 5, 'g' => 3, 'h' => -4));
        $sorted = $ar->getArrayCopy();
        uasort($sorted, $function);
        $ar->uasort($function);
        $this->assertSame($sorted, $ar->getArrayCopy());
    }

    public function testUksort()
    {
        $function = function ($a, $b) {
            $a = preg_replace('@^(a|an|the) @', '', $a);
            $b = preg_replace('@^(a|an|the) @', '', $b);

            return strcasecmp($a, $b);
        };

        $ar = new ArrayObject(array('John' => 1, 'the Earth' => 2, 'an apple' => 3, 'a banana' => 4));
        $sorted = $ar->getArrayCopy();
        uksort($sorted, $function);
        $ar->uksort($function);
        $this->assertSame($sorted, $ar->getArrayCopy());
    }

}
