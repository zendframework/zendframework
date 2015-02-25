<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Permissions\Acl\Assertion;

use Zend\Permissions\Acl\Assertion\AssertionAggregate;
use Zend\Di\Exception\UndefinedReferenceException;

class AssertionAggregateTest extends \PHPUnit_Framework_TestCase
{
    protected $assertionAggregate;

    public function setUp()
    {
        $this->assertionAggregate = new AssertionAggregate();
    }

    public function testAddAssertion()
    {
        $assertion = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $this->assertionAggregate->addAssertion($assertion);

        $this->assertAttributeEquals(array(
            $assertion
        ), 'assertions', $this->assertionAggregate);

        $aggregate = $this->assertionAggregate->addAssertion('other.assertion');
        $this->assertAttributeEquals(array(
            $assertion,
            'other.assertion'
        ), 'assertions', $this->assertionAggregate);

        // test fluent interface
        $this->assertSame($this->assertionAggregate, $aggregate);

        return clone $this->assertionAggregate;
    }

    public function testAddAssertions()
    {
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');

        $aggregate = $this->assertionAggregate->addAssertions($assertions);

        $this->assertAttributeEquals($assertions, 'assertions', $this->assertionAggregate);

        // test fluent interface
        $this->assertSame($this->assertionAggregate, $aggregate);
    }

    /**
     * @depends testAddAssertion
     */
    public function testClearAssertions(AssertionAggregate $assertionAggregate)
    {
        $this->assertAttributeCount(2, 'assertions', $assertionAggregate);

        $aggregate = $assertionAggregate->clearAssertions();

        $this->assertAttributeEmpty('assertions', $assertionAggregate);

        // test fluent interface
        $this->assertSame($assertionAggregate, $aggregate);
    }

    public function testDefaultModeValue()
    {
        $this->assertAttributeEquals(AssertionAggregate::MODE_ALL, 'mode', $this->assertionAggregate);
    }

    /**
     * @dataProvider getDataForTestSetMode
     */
    public function testSetMode($mode, $exception = false)
    {
        if ($exception) {
            $this->setExpectedException('\Zend\Permissions\Acl\Exception\InvalidArgumentException');
            $this->assertionAggregate->setMode($mode);
        } else {
            $this->assertionAggregate->setMode($mode);
            $this->assertAttributeEquals($mode, 'mode', $this->assertionAggregate);
        }
    }

    public static function getDataForTestSetMode()
    {
        return array(
            array(
                AssertionAggregate::MODE_ALL
            ),
            array(
                AssertionAggregate::MODE_AT_LEAST_ONE
            ),
            array(
                'invalid mode',
                true
            )
        );
    }

    public function testManagerAccessors()
    {
        $manager = $this->getMock('Zend\Permissions\Acl\Assertion\AssertionManager');

        $aggregate = $this->assertionAggregate->setAssertionManager($manager);
        $this->assertAttributeEquals($manager, 'assertionManager', $this->assertionAggregate);
        $this->assertEquals($manager, $this->assertionAggregate->getAssertionManager());
        $this->assertSame($this->assertionAggregate, $aggregate);
    }

    public function testCallingAssertWillFetchAssertionFromManager()
    {
        $acl = $this->getMock('\Zend\Permissions\Acl\Acl');
        $role = $this->getMock('\Zend\Permissions\Acl\Role\GenericRole', array(), array(
            'test.role'
        ));
        $resource = $this->getMock('\Zend\Permissions\Acl\Resource\GenericResource', array(), array(
            'test.resource'
        ));

        $assertion = $this->getMockForAbstractClass('Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertion->expects($this->once())
            ->method('assert')
            ->will($this->returnValue(true));

        $manager = $this->getMock('Zend\Permissions\Acl\Assertion\AssertionManager', array(
            'get'
        ));
        $manager->expects($this->once())
            ->method('get')
            ->with('assertion')
            ->will($this->returnValue($assertion));

        $this->assertionAggregate->setAssertionManager($manager);
        $this->assertionAggregate->addAssertion('assertion');

        $this->assertTrue($this->assertionAggregate->assert($acl, $role, $resource, 'privilege'));
    }

    public function testAssertThrowsAnExceptionWhenReferingToNonExistentAssertion()
    {
        $acl = $this->getMock('\Zend\Permissions\Acl\Acl');
        $role = $this->getMock('\Zend\Permissions\Acl\Role\GenericRole', array(), array(
            'test.role'
        ));
        $resource = $this->getMock('\Zend\Permissions\Acl\Resource\GenericResource', array(), array(
            'test.resource'
        ));

        $manager = $this->getMock('Zend\Permissions\Acl\Assertion\AssertionManager', array(
            'get'
        ));
        $manager->expects($this->once())
            ->method('get')
            ->with('assertion')
            ->will($this->throwException(new UndefinedReferenceException()));

        $this->assertionAggregate->setAssertionManager($manager);

        $this->setExpectedException('Zend\Permissions\Acl\Assertion\Exception\InvalidAssertionException');
        $this->assertionAggregate->addAssertion('assertion');
        $this->assertionAggregate->assert($acl, $role, $resource, 'privilege');
    }

    public function testAssertWithModeAll()
    {
        $acl = $this->getMock('\Zend\Permissions\Acl\Acl');
        $role = $this->getMock('\Zend\Permissions\Acl\Role\GenericRole', array(), array(
            'test.role'
        ));
        $resource = $this->getMock('\Zend\Permissions\Acl\Resource\GenericResource', array(), array(
            'test.resource'
        ));

        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');

        $assertions[0]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(true));
        $assertions[1]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(true));
        $assertions[2]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(true));

        foreach ($assertions as $assertion) {
            $this->assertionAggregate->addAssertion($assertion);
        }

        $this->assertTrue($this->assertionAggregate->assert($acl, $role, $resource, 'privilege'));
    }

    public function testAssertWithModeAtLeastOne()
    {
        $acl = $this->getMock('\Zend\Permissions\Acl\Acl');
        $role = $this->getMock('\Zend\Permissions\Acl\Role\GenericRole', array(), array(
            'test.role'
        ));
        $resource = $this->getMock('\Zend\Permissions\Acl\Resource\GenericResource', array(), array(
            'test.resource'
        ));

        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');

        $assertions[0]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(false));
        $assertions[1]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(false));
        $assertions[2]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(true));

        foreach ($assertions as $assertion) {
            $this->assertionAggregate->addAssertion($assertion);
        }

        $this->assertionAggregate->setMode(AssertionAggregate::MODE_AT_LEAST_ONE);
        $this->assertTrue($this->assertionAggregate->assert($acl, $role, $resource, 'privilege'));
    }

    public function testDoesNotAssertWithModeAll()
    {
        $acl = $this->getMock('\Zend\Permissions\Acl\Acl');
        $role = $this->getMock('\Zend\Permissions\Acl\Role\GenericRole', array(
            'assert'
        ), array(
            'test.role'
        ));
        $resource = $this->getMock('\Zend\Permissions\Acl\Resource\GenericResource', array(
            'assert'
        ), array(
            'test.resource'
        ));

        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');

        $assertions[0]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(true));
        $assertions[1]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(true));
        $assertions[2]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(false));

        foreach ($assertions as $assertion) {
            $this->assertionAggregate->addAssertion($assertion);
        }

        $this->assertFalse($this->assertionAggregate->assert($acl, $role, $resource, 'privilege'));
    }

    public function testDoesNotAssertWithModeAtLeastOne()
    {
        $acl = $this->getMock('\Zend\Permissions\Acl\Acl');
        $role = $this->getMock('\Zend\Permissions\Acl\Role\GenericRole', array(
            'assert'
        ), array(
            'test.role'
        ));
        $resource = $this->getMock('\Zend\Permissions\Acl\Resource\GenericResource', array(
            'assert'
        ), array(
            'test.resource'
        ));

        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $assertions[] = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');

        $assertions[0]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(false));
        $assertions[1]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(false));
        $assertions[2]->expects($this->once())
            ->method('assert')
            ->with($acl, $role, $resource, 'privilege')
            ->will($this->returnValue(false));

        foreach ($assertions as $assertion) {
            $this->assertionAggregate->addAssertion($assertion);
        }

        $this->assertionAggregate->setMode(AssertionAggregate::MODE_AT_LEAST_ONE);
        $this->assertFalse($this->assertionAggregate->assert($acl, $role, $resource, 'privilege'));
    }

    public function testAssertThrowsAnExceptionWhenNoAssertionIsAggregated()
    {
        $acl = $this->getMock('\Zend\Permissions\Acl\Acl');
        $role = $this->getMock('\Zend\Permissions\Acl\Role\GenericRole', array(
            'assert'
        ), array(
            'test.role'
        ));
        $resource = $this->getMock('\Zend\Permissions\Acl\Resource\GenericResource', array(
            'assert'
        ), array(
            'test.resource'
        ));

        $this->setExpectedException('Zend\Permissions\Acl\Exception\RuntimeException');

        $this->assertionAggregate->assert($acl, $role, $resource, 'privilege');
    }
}
