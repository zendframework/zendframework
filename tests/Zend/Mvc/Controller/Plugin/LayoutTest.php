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
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mvc\Controller\Plugin\Layout as LayoutPlugin,
    Zend\Mvc\MvcEvent,
    ZendTest\Mvc\Controller\TestAsset\SampleController,
    Zend\View\Model\ViewModel;

class LayoutTest extends TestCase
{
    public function setUp()
    {
        $this->event      = $event = new MvcEvent();
        $this->controller = new SampleController();
        $this->controller->setEvent($event);

        $this->plugin = $this->controller->plugin('layout');
    }

    public function testPluginWithoutControllerRaisesDomainException()
    {
        $plugin = new LayoutPlugin();
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'requires a controller');
        $plugin->setTemplate('home');
    }

    public function testSetTemplateAltersTemplateInEventViewModel()
    {
        $model = new ViewModel();
        $model->setTemplate('layout');
        $this->event->setViewModel($model);

        $this->plugin->setTemplate('alternate/layout');
        $this->assertEquals('alternate/layout', $model->getTemplate());
    }

    public function testInvokeProxiesToSetTemplate()
    {
        $model = new ViewModel();
        $model->setTemplate('layout');
        $this->event->setViewModel($model);

        $plugin = $this->plugin;
        $plugin('alternate/layout');
        $this->assertEquals('alternate/layout', $model->getTemplate());
    }

    public function testCallingInvokeWithNoArgumentsReturnsViewModel()
    {
        $model = new ViewModel();
        $model->setTemplate('layout');
        $this->event->setViewModel($model);

        $plugin = $this->plugin;
        $result = $plugin();
        $this->assertSame($model, $result);
    }
}
