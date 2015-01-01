<?php
/**
* Zend Framework (http://framework.zend.com/)
*
* @link http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
namespace ZendTest\Mvc\View\Console;

use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;
use Zend\Mvc\View\Console\RouteNotFoundStrategy;

class RouteNotFoundStrategyTest extends TestCase
{
    /**
     * @var RouteNotFoundStrategy
     */
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new RouteNotFoundStrategy();
    }

    public function testRenderTableConcatenateAndInvalidInputDoesNotThrowException()
    {
        $reflection = new ReflectionClass('Zend\Mvc\View\Console\RouteNotFoundStrategy');
        $method = $reflection->getMethod('renderTable');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->strategy, array(array(array()), 1, 0));
        $this->assertSame('', $result);
    }
}
