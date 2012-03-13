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
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Navigation;

use Zend\Navigation\Page\AbstractPage,
    Zend\Navigation;

/**
 * Tests Zend_Navigation_Page::factory()
 *
/**
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Navigation
 */
class PageFactoryTest extends \PHPUnit_Framework_TestCase
{


    public function testDetectMvcPage()
    {
        $pages = array(
            AbstractPage::factory(array(
                'label' => 'MVC Page',
                'action' => 'index'
            )),
            AbstractPage::factory(array(
                'label' => 'MVC Page',
                'controller' => 'index'
            )),
            AbstractPage::factory(array(
                'label' => 'MVC Page',
                'module' => 'index'
            )),
            AbstractPage::factory(array(
                'label' => 'MVC Page',
                'route' => 'home'
            ))
        );

        $this->assertContainsOnly('Zend\Navigation\Page\Mvc', $pages);
    }

    public function testDetectUriPage()
    {
        $page = AbstractPage::factory(array(
            'label' => 'URI Page',
            'uri' => '#'
        ));

        $this->assertInstanceOf('Zend\\Navigation\\Page\\Uri', $page);
    }

    public function testMvcShouldHaveDetectionPrecedence()
    {
        $page = AbstractPage::factory(array(
            'label' => 'MVC Page',
            'action' => 'index',
            'controller' => 'index',
            'uri' => '#'
        ));

        $this->assertInstanceOf('Zend\\Navigation\\Page\\Mvc', $page);
    }

    public function testSupportsMvcShorthand()
    {
        $mvcPage = AbstractPage::factory(array(
            'type' => 'mvc',
            'label' => 'MVC Page',
            'action' => 'index',
            'controller' => 'index'
        ));

        $this->assertInstanceOf('Zend\\Navigation\\Page\\Mvc', $mvcPage);
    }

    public function testSupportsUriShorthand()
    {
        $uriPage = AbstractPage::factory(array(
            'type' => 'uri',
            'label' => 'URI Page',
            'uri' => 'http://www.example.com/'
        ));

        $this->assertInstanceOf('Zend\\Navigation\\Page\\Uri', $uriPage);
    }

    public function testSupportsCustomPageTypes()
    {
        $page = AbstractPage::factory(array(
            'type' => 'ZendTest\Navigation\TestAsset\Page',
            'label' => 'My Custom Page'
        ));

        return $this->assertInstanceOf('ZendTest\\Navigation\\TestAsset\\Page', $page);
    }

    public function testShouldFailForInvalidType()
    {
        try {
            $page = AbstractPage::factory(array(
                'type' => 'ZendTest\Navigation\TestAsset\InvalidPage',
                'label' => 'My Invalid Page'
            ));
        } catch(Navigation\Exception\InvalidArgumentException $e) {
            return;
        }

        $this->fail('An exception has not been thrown for invalid page type');
    }

    public function testShouldFailForNonExistantType()
    {
        $pageConfig = array(
            'type' => 'My_NonExistant_Page',
            'label' => 'My non-existant Page'
        );

        try {
            $page = AbstractPage::factory($pageConfig);
        } catch(Navigation\Exception\InvalidArgumentException $e) {
            return;
        }

        $msg = 'An exception has not been thrown for non-existant class';
        $this->fail($msg);
    }

    public function testShouldFailIfUnableToDetermineType()
    {
        try {
            $page = AbstractPage::factory(array(
                'label' => 'My Invalid Page'
            ));
        } catch(Navigation\Exception\InvalidArgumentException $e) {
            return;
        }

        $this->fail('An exception has not been thrown for invalid page type');
    }
}
