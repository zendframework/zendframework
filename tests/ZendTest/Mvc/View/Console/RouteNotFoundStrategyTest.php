<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
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
        $this->strategy   = new RouteNotFoundStrategy();
    }

    public function testRenderTableConcatenateAndInvalidInputDoesNotThrowException()
    {
        $reflection = new ReflectionClass('Zend\Mvc\View\Console\RouteNotFoundStrategy');
        $method = $reflection->getMethod('renderTable');
        $method->setAccessible(true);
        $method->invokeArgs($this->strategy, array(array(array()), 1, 0));
    }
}
