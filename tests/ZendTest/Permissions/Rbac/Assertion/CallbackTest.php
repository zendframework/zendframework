<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Permissions\Rbac\Assertion;

use Zend\Permissions\Rbac;

class CallbackTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Ensures constructor throws InvalidArgumentException if not callable is provided
     */
    public function testConstructorThrowsExceptionIfNotCallable()
    {
        $this->setExpectedException(
            'Zend\Permissions\Rbac\Exception\InvalidArgumentException',
            'Invalid callback provided; not callable'
        );
        new Rbac\Assertion\Callback('I\'m not callable!');
    }

    /**
     * Ensures callback is set in object
     */
    public function testCallbackIsSet()
    {
        $callback   = function () {};
        $assert     = new Rbac\Assertion\Callback($callback);
        $reflection = new \ReflectionProperty(get_class($assert), 'callback');
        $reflection->setAccessible(true);
        $this->assertEquals($callback, $reflection->getValue($assert));
    }

    /**
     * Ensures assert method provides callback with rbac as argument
     */
    public function testAssertMethodPassRbacToCallback()
    {
        $rbac   = new Rbac\Rbac();
        $that   = $this;
        $assert = new Rbac\Assertion\Callback(function ($rbacArg) use ($that, $rbac) {
            $that->assertEquals($rbacArg, $rbac);
            return false;
        });
        $foo  = new Rbac\Role('foo');
        $foo->addPermission('can.foo');
        $rbac->isGranted($foo, 'can.foo', $assert);
    }

    /**
     * Ensures assert method returns callback's function value
     */
    public function testAssertMethod()
    {
        $rbac = new Rbac\Rbac();
        $foo  = new Rbac\Role('foo');
        $bar  = new Rbac\Role('bar');

        $assertRoleMatch = function ($role) {
            return function ($rbac) use ($role) {
                return $role->getName() == 'foo';
            };
        };

        $roleNoMatch = new Rbac\Assertion\Callback($assertRoleMatch($bar));
        $roleMatch   = new Rbac\Assertion\Callback($assertRoleMatch($foo));

        $foo->addPermission('can.foo');
        $bar->addPermission('can.bar');

        $rbac->addRole($foo);
        $rbac->addRole($bar);

        $this->assertEquals(false, $rbac->isGranted($bar, 'can.bar', $roleNoMatch));
        $this->assertEquals(false, $rbac->isGranted($bar, 'can.foo', $roleNoMatch));

        $this->assertEquals(true, $rbac->isGranted($foo, 'can.foo', $roleMatch));
    }
}
