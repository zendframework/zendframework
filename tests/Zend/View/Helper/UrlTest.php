<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper\Url as UrlHelper;
use Zend\Mvc\Router\SimpleRouteStack as Router;

/**
 * Zend_View_Helper_UrlTest
 *
 * Tests formText helper, including some common functionality of all form helpers
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $router = new Router();
        $router->addRoute('home', array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/',
            )
        ));
        $router->addRoute('default', array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/:controller[/:action]',
                )
        ));

        $this->url = new UrlHelper;
        $this->url->setRouter($router);
    }

    public function testHelperHasHardDependencyWithRouter()
    {
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'No RouteStackInterface instance provided');
        $url = new UrlHelper;
        $url('home');
    }

    public function testHomeRoute()
    {
        $url = $this->url->__invoke('home');
        $this->assertEquals('/', $url);
    }

    public function testModuleRoute()
    {
        $url = $this->url->__invoke('default', array('controller' => 'ctrl', 'action' => 'act'));
        $this->assertEquals('/ctrl/act', $url);
    }
}
