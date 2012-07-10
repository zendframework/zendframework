<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\SubClass;

class SubClassTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '5.3.7', '>=')) {
            $this->markTestSkipped('Test is only for PHP Versions smaller then 5.3.7');
        }
        require_once 'TestAsset/DummySubclasses.php';
    }

    public function testIsSubclassOf()
    {
        $test1 = SubClass::isSubclassOf('ZendTest\Stdlib\TestAsset\ChildClass', 'ZendTest\Stdlib\TestAsset\TestInterface');
        $test2 = SubClass::isSubclassOf('ZendTest\Stdlib\TestAsset\ParentClass', 'ZendTest\Stdlib\TestAsset\TestInterface');
        $this->assertTrue($test1);
        $this->assertTrue($test2);
    }

}
