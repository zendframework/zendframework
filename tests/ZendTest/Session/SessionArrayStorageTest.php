<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session;

use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\SessionManager;
use Zend\Session\Container;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class SessionArrayStorageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $_SESSION = array();
        $this->storage = new SessionArrayStorage;
    }

    public function tearDown()
    {
        $_SESSION = array();
    }

    public function testStorageWritesToSessionSuperglobal()
    {
        $this->storage['foo'] = 'bar';
        $this->assertSame($_SESSION['foo'], $this->storage->foo);
        unset($this->storage['foo']);
        $this->assertFalse(array_key_exists('foo', $_SESSION));
    }

    public function testPassingArrayToConstructorOverwritesSessionSuperglobal()
    {
        $_SESSION['foo'] = 'bar';
        $array   = array('foo' => 'FOO');
        $storage = new SessionArrayStorage($array);
        $expected = array(
            'foo' => 'FOO',
            '__ZF' => array(
                '_REQUEST_ACCESS_TIME' => $storage->getRequestAccessTime(),
            ),
        );
        $this->assertSame($expected, $_SESSION);
    }

    public function testModifyingSessionSuperglobalDirectlyUpdatesStorage()
    {
        $_SESSION['foo'] = 'bar';
        $this->assertTrue(isset($this->storage['foo']));
    }

    public function testDestructorSetsSessionToArray()
    {
        $this->storage->foo = 'bar';
        $expected = array(
            '__ZF' => array(
                '_REQUEST_ACCESS_TIME' => $this->storage->getRequestAccessTime(),
            ),
            'foo' => 'bar',
        );
        $this->storage->__destruct();
        $this->assertSame($expected, $_SESSION);
    }

    public function testModifyingOneSessionObjectModifiesTheOther()
    {
        $this->storage->foo = 'bar';
        $storage = new SessionArrayStorage();
        $storage->bar = 'foo';
        $this->assertEquals('foo', $this->storage->bar);
    }

    public function testMarkingOneSessionObjectImmutableShouldMarkOtherInstancesImmutable()
    {
        $this->storage->foo = 'bar';
        $storage = new SessionArrayStorage();
        $this->assertEquals('bar', $storage['foo']);
        $this->storage->markImmutable();
        $this->assertTrue($storage->isImmutable(), var_export($_SESSION, 1));
    }

    public function testAssignment()
    {
        $_SESSION['foo'] = 'bar';
        $this->assertEquals('bar', $this->storage['foo']);
    }

    public function testMultiDimensionalAssignment()
    {
        $_SESSION['foo']['bar'] = 'baz';
        $this->assertEquals('baz', $this->storage['foo']['bar']);
    }

    public function testUnset()
    {
        $_SESSION['foo'] = 'bar';
        unset($_SESSION['foo']);
        $this->assertFalse(isset($this->storage['foo']));
    }

    public function testMultiDimensionalUnset()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Known issue on versions of PHP less than 5.3.4');
        }
        $this->storage['foo'] = array('bar' => array('baz' => 'boo'));
        unset($this->storage['foo']['bar']['baz']);
        $this->assertFalse(isset($this->storage['foo']['bar']['baz']));
        unset($this->storage['foo']['bar']);
        $this->assertFalse(isset($this->storage['foo']['bar']));
    }

    public function testSessionWorksWithContainer()
    {
        $container = new Container('test');
        $container->foo = 'bar';

        $this->assertSame($container->foo, $_SESSION['test']['foo']);
    }

    public function testToArrayWithMetaData()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';
        $this->storage->setMetadata('foo', 'bar');
        $expected = array(
            '__ZF' => array(
                '_REQUEST_ACCESS_TIME' => $this->storage->getRequestAccessTime(),
                'foo' => 'bar',
            ),
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $this->assertSame($expected, $this->storage->toArray(true));
    }

    public function testUndefinedSessionManipulation()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Known issue on versions of PHP less than 5.3.4');
        }

        $this->storage['foo'] = 'bar';
        $this->storage['bar'][] = 'bar';
        $this->storage['baz']['foo'] = 'bar';

        $expected = array(
            '__ZF' => array(
                '_REQUEST_ACCESS_TIME' => $this->storage->getRequestAccessTime(),
            ),
            'foo' => 'bar',
            'bar' => array('bar'),
            'baz' => array('foo' => 'bar'),
        );
        $this->assertSame($expected, $this->storage->toArray(true));
    }

    /**
     * @runInSeparateProcess
     */
    public function testExpirationHops()
    {
        // since we cannot explicitly test reinitalizing the session
        // we will act in how session manager would in this case.
        $storage = new SessionArrayStorage();
        $manager = new SessionManager(null, $storage);
        $manager->start();

        $container = new Container('test');
        $container->foo = 'bar';
        $container->setExpirationHops(1);

        $copy = $_SESSION;
        $_SESSION = null;
        $storage->init($copy);
        $this->assertEquals('bar', $container->foo);

        $copy = $_SESSION;
        $_SESSION = null;
        $storage->init($copy);
        $this->assertNull($container->foo);
    }

    /**
     * @runInSeparateProcess
     */
    public function testPreserveRequestAccessTimeAfterStart()
    {
        $manager = new SessionManager(null, $this->storage);
        $this->assertGreaterThan(0, $this->storage->getRequestAccessTime());
        $manager->start();
        $this->assertGreaterThan(0, $this->storage->getRequestAccessTime());
    }

}
