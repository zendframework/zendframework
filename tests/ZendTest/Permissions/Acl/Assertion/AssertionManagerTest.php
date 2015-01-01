<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Permissions\Acl\Assertion;

use Zend\Permissions\Acl\Assertion\AssertionManager;

class AssertionManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new AssertionManager();
    }

    public function testValidatePlugin()
    {
        $assertion = $this->getMockForAbstractClass('Zend\Permissions\Acl\Assertion\AssertionInterface');

        $this->assertTrue($this->manager->validatePlugin($assertion));

        $this->setExpectedException('Zend\Permissions\Acl\Exception\InvalidArgumentException');

        $this->manager->validatePlugin('invalid plugin');
    }
}
