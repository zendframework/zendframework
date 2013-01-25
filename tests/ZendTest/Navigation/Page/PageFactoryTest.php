<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace ZendTest\Navigation;

use Zend\Navigation\Page\AbstractPage;
use Zend\Navigation;

/**
 * Tests Zend_Navigation_Page::factory()
 *
/**
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
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
        } catch (Navigation\Exception\InvalidArgumentException $e) {
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
        } catch (Navigation\Exception\InvalidArgumentException $e) {
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
        } catch (Navigation\Exception\InvalidArgumentException $e) {
            return;
        }

        $this->fail('An exception has not been thrown for invalid page type');
    }
}
