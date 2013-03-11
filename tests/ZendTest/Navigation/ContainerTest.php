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

use Zend\Navigation;
use Zend\Navigation\Page;
use Zend\Config;

/**
 * Tests the class Zend_Navigation_Container
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @group      Zend_Navigation
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     *
     */
    protected function setUp()
    {

    }

    /**
     * Tear down the environment after running a test
     *
     */
    protected function tearDown()
    {

    }

    public function testConstructWithArray()
    {
        $argument = array(
            array(
                'label' => 'Page 1',
                'uri'   => 'page1.html'
            ),
            array(
                'label' => 'Page 2',
                'uri'   => 'page2.html'
            ),
            array(
                'label' => 'Page 3',
                'uri'   => 'page3.html'
            )
        );

        $container = new Navigation\Navigation($argument);
        $this->assertEquals(3, $container->count());
    }

    public function testConstructWithConfig()
    {
        $argument = new Config\Config(array(
            array(
                'label' => 'Page 1',
                'uri'   => 'page1.html'
            ),
            array(
                'label' => 'Page 2',
                'uri'   => 'page2.html'
            ),
            array(
                'label' => 'Page 3',
                'uri'   => 'page3.html'
            )
        ));

        $container = new Navigation\Navigation($argument);
        $this->assertEquals(3, $container->count());
    }

    public function testConstructorShouldThrowExceptionOnInvalidArgument()
    {
        try {
            $nav = new Navigation\Navigation('ok');
            $this->fail('An invalid argument was given to the constructor, ' .
                        'but a Zend\Navigation\Exception\InvalidArgumentException was ' .
                        'not thrown');
        } catch (Navigation\Exception\InvalidArgumentException $e) {
            $this->assertContains('Invalid argument: $pages', $e->getMessage());
        }

        try {
            $nav = new Navigation\Navigation(1337);
            $this->fail('An invalid argument was given to the constructor, ' .
                        'but a Zend\Navigation\Exception\InvalidArgumentException was ' .
                        'not thrown');
        } catch (Navigation\Exception\InvalidArgumentException $e) {
            $this->assertContains('Invalid argument: $pages', $e->getMessage());
        }

        try {
            $nav = new Navigation\Navigation(new \stdClass());
            $this->fail('An invalid argument was given to the constructor, ' .
                        'but a Zend\Navigation\Exception\InvalidArgumentException was ' .
                        'not thrown');
        } catch (Navigation\Exception\ExceptionInterface $e) {
            $this->assertContains('Invalid argument: $pages', $e->getMessage());
        }
    }

    /**
     * @group 3823
     * @group 3840
     */
    public function testAddPagesWithNullValueSkipsPage()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            null
        ));
        $count = count($nav->getPages());
        $this->assertEquals(1, $count);
    }

    public function testIterationShouldBeOrderAware()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#',
                'order' => -1
            ),
            array(
                'label' => 'Page 3',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 4',
                'uri' => '#',
                'order' => 100
            ),
            array(
                'label' => 'Page 5',
                'uri' => '#'
            )
        ));

        $expected = array('Page 2', 'Page 1', 'Page 3', 'Page 5', 'Page 4');
        $actual = array();
        foreach ($nav as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testRecursiveIteration()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#',
                'pages' => array(
                    array(
                        'label' => 'Page 1.1',
                        'uri' => '#',
                        'pages' => array(
                            array(
                                'label' => 'Page 1.1.1',
                                'uri' => '#'
                            ),
                            array(
                                'label' => 'Page 1.1.2',
                                'uri' => '#'
                            )
                        )
                    ),
                    array(
                        'label' => 'Page 1.2',
                        'uri' => '#'
                    )
                )
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#',
                'pages' => array(
                    array(
                        'label' => 'Page 2.1',
                        'uri' => '#'
                    )
                )
            ),
            array(
                'label' => 'Page 3',
                'uri' => '#'
            )
        ));

        $actual = array();
        $expected = array(
            'Page 1',
            'Page 1.1',
            'Page 1.1.1',
            'Page 1.1.2',
            'Page 1.2',
            'Page 2',
            'Page 2.1',
            'Page 3'
        );

        $iterator = new \RecursiveIteratorIterator($nav,
            \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testSettingPageOrderShouldUpdateContainerOrder()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));

        $page3 = Page\AbstractPage::factory(array(
            'label' => 'Page 3',
            'uri' => '#'
        ));

        $nav->addPage($page3);

        $expected = array(
            'before' => array('Page 1', 'Page 2', 'Page 3'),
            'after'  => array('Page 3', 'Page 1', 'Page 2')
        );

        $actual = array(
            'before' => array(),
            'after'  => array()
        );

        foreach ($nav as $page) {
            $actual['before'][] = $page->getLabel();
        }

        $page3->setOrder(-1);

        foreach ($nav as $page) {
            $actual['after'][] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testAddPageShouldWorkWithArray()
    {
        $pageOptions = array(
            'label' => 'From array',
            'uri' => '#array'
        );

        $nav = new Navigation\Navigation();
        $nav->addPage($pageOptions);

        $this->assertEquals(1, count($nav));
    }

    public function testAddPageShouldWorkWithConfig()
    {
        $pageOptions = array(
            'label' => 'From config',
            'uri' => '#config'
        );

        $pageOptions = new Config\Config($pageOptions);

        $nav = new Navigation\Navigation();
        $nav->addPage($pageOptions);

        $this->assertEquals(1, count($nav));
    }

    public function testAddPageShouldWorkWithPageInstance()
    {
        $pageOptions = array(
            'label' => 'From array 1',
            'uri' => '#array'
        );

        $nav = new Navigation\Navigation(array($pageOptions));

        $page = Page\AbstractPage::factory($pageOptions);
        $nav->addPage($page);

        $this->assertEquals(2, count($nav));
    }

    public function testAddPagesShouldWorkWithArray()
    {
        $nav = new Navigation\Navigation();
        $nav->addPages(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'action' => 'index',
                'controller' => 'index'
            )
        ));

        $this->assertEquals(2, count($nav),
                            'Expected 2 pages, found ' . count($nav));
    }

    public function testAddPagesShouldWorkWithConfig()
    {
        $nav = new Navigation\Navigation();
        $nav->addPages(new Config\Config(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'action' => 'index',
                'controller' => 'index'
            )
        )));

        $this->assertEquals(2, count($nav),
                            'Expected 2 pages, found ' . count($nav));
    }

    public function testAddPagesShouldWorkWithMixedArray()
    {
        $nav = new Navigation\Navigation();
        $nav->addPages(new Config\Config(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            new Config\Config(array(
                'label' => 'Page 2',
                'action' => 'index',
                'controller' => 'index'
            )),
            Page\AbstractPage::factory(array(
                'label' => 'Page 3',
                'uri' => '#'
            ))
        )));

        $this->assertEquals(3, count($nav),
                            'Expected 3 pages, found ' . count($nav));
    }

    /**
     * @group ZF-9815
     */
    public function testAddPagesShouldWorkWithNavigationContainer()
    {
        $nav = new Navigation\Navigation();
        $nav->addPages($this->_getFindByNavigation());

        $this->assertEquals(
            3, count($nav), 'Expected 3 pages, found ' . count($nav)
        );

        $this->assertEquals(
            $this->_getFindByNavigation()->toArray(),
            $nav->toArray()
        );
    }

    public function testAddPagesShouldThrowExceptionWhenGivenString()
    {
        $nav = new Navigation\Navigation();

        try {
            $nav->addPages('this is a string');
            $this->fail('An invalid argument was given to addPages(), ' .
                        'but a Zend\Navigation\Exception\InvalidArgumentException was ' .
                        'not thrown');
        } catch (Navigation\Exception\InvalidArgumentException $e) {
            $this->assertContains('Invalid argument: $pages must be', $e->getMessage());
        }
    }

    public function testAddPagesShouldThrowExceptionWhenGivenAnArbitraryObject()
    {
        $nav = new Navigation\Navigation();

        try {
            $nav->addPages($pages = new \stdClass());
            $this->fail('An invalid argument was given to addPages(), ' .
                        'but a Zend\Navigation\Exception\InvalidArgumentException was ' .
                        'not thrown');
        } catch (Navigation\Exception\InvalidArgumentException $e) {
            $this->assertContains('Invalid argument: $pages must be', $e->getMessage());
        }
    }

    public function testRemovingAllPages()
    {
        $nav = new Navigation\Navigation();
        $nav->addPages(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));

        $nav->removePages();

        $this->assertEquals(0, count($nav),
                            'Expected 0 pages, found ' . count($nav));
    }

    public function testSettingPages()
    {
        $nav = new Navigation\Navigation();
        $nav->addPages(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));

        $nav->setPages(array(
            array(
                'label' => 'Page 3',
                'uri' => '#'
            )
        ));

        $this->assertEquals(1, count($nav),
                            'Expected 1 page, found ' . count($nav));
    }

    public function testGetPagesShouldReturnAnArrayOfPages()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'uri' => 'Page 1'
            ),
            array(
                'uri' => 'Page 2'
            )
        ));

        $pages = $nav->getPages();

        $expected = array(
            'type' => 'array',
            'count' => 2
        );

        $actual = array(
            'type' => gettype($pages),
            'count' => count($pages)
        );

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('Zend\Navigation\Page\Uri', $pages, false);
    }

    public function testGetPagesShouldReturnUnorderedPages()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'label' => 'Page 2',
                'uri' => '#',
                'order' => -1
            ),
            array(
                'label' => 'Page 4',
                'uri' => '#',
                'order' => 100
            ),
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 5',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 3',
                'uri' => '#'
            )
        ));

        $expected = array('Page 2', 'Page 4', 'Page 1', 'Page 5', 'Page 3');
        $actual = array();
        foreach ($nav->getPages() as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testRemovingPageByOrder()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#',
                'order' => 32
            ),
            array(
                'label' => 'Page 3',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 4',
                'uri' => '#'
            )
        ));

        $expected = array(
            'remove0'      => true,
            'remove32'     => true,
            'remove0again' => true,
            'remove1000'   => false,
            'count'        => 1,
            'current'      => 'Page 4'
        );

        $actual = array(
            'remove0'      => $nav->removePage(0),
            'remove32'     => $nav->removePage(32),
            'remove0again' => $nav->removePage(0),
            'remove1000'   => $nav->removePage(1000),
            'count'        => $nav->count(),
            'current'      => $nav->current()->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testRemovingPageByInstance()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));

        $page3 = Page\AbstractPage::factory(array(
            'label' => 'Page 3',
            'uri' => '#'
        ));

        $nav->addPage($page3);

        $this->assertEquals(true, $nav->removePage($page3));
    }

    public function testRemovingPageByInstanceShouldReturnFalseIfPageIsNotInContainer()
    {
        $nav = new Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));

        $page = Page\AbstractPage::factory(array(
            'label' => 'Page lol',
            'uri' => '#'
        ));

        $this->assertEquals(false, $nav->removePage($page));
    }

    public function testHasPage()
    {
        $page0 = Page\AbstractPage::factory(array(
            'label' => 'Page 0',
            'uri' => '#'
        ));

        $page1 = Page\AbstractPage::factory(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));

        $page1_1 = Page\AbstractPage::factory(array(
            'label' => 'Page 1.1',
            'uri' => '#'
        ));

        $page1_2 = Page\AbstractPage::factory(array(
            'label' => 'Page 1.2',
            'uri' => '#'
        ));

        $page1_2_1 = Page\AbstractPage::factory(array(
            'label' => 'Page 1.2.1',
            'uri' => '#'
        ));

        $page1_3 = Page\AbstractPage::factory(array(
            'label' => 'Page 1.3',
            'uri' => '#'
        ));

        $page2 = Page\AbstractPage::factory(array(
            'label' => 'Page 2',
            'uri' => '#'
        ));

        $page3 = Page\AbstractPage::factory(array(
            'label' => 'Page 3',
            'uri' => '#'
        ));

        $nav = new Navigation\Navigation(array($page1, $page2, $page3));

        $page1->addPage($page1_1);
        $page1->addPage($page1_2);
        $page1_2->addPage($page1_2_1);
        $page1->addPage($page1_3);

        $expected = array(
            'haspage0'            => false,
            'haspage2'            => true,
            'haspage1_1'          => false,
            'haspage1_1recursive' => true
        );

        $actual = array(
            'haspage0'            => $nav->hasPage($page0),
            'haspage2'            => $nav->hasPage($page2),
            'haspage1_1'          => $nav->hasPage($page1_1),
            'haspage1_1recursive' => $nav->hasPage($page1_1, true)
        );

        $this->assertEquals($expected, $actual);
    }

    public function testHasPages()
    {
        $nav1 = new Navigation\Navigation();
        $nav2 = new Navigation\Navigation();
        $nav2->addPage(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));

        $expected = array(
            'empty' => false,
            'notempty' => true
        );

        $actual = array(
            'empty' => $nav1->hasPages(),
            'notempty' => $nav2->hasPages()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testSetParentShouldWorkWithPage()
    {
        $page1 = Page\AbstractPage::factory(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));

        $page2 = Page\AbstractPage::factory(array(
            'label' => 'Page 2',
            'uri' => '#'
        ));

        $page2->setParent($page1);

        $expected = array(
            'parent' => 'Page 1',
            'hasPages' => true
        );

        $actual = array(
            'parent' => $page2->getParent()->getLabel(),
            'hasPages' => $page1->hasPages()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testSetParentShouldWorkWithNull()
    {
        $page1 = Page\AbstractPage::factory(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));

        $page2 = Page\AbstractPage::factory(array(
            'label' => 'Page 2',
            'uri' => '#'
        ));

        $page2->setParent($page1);
        $page2->setParent(null);

        $this->assertEquals(null, $page2->getParent());
    }

    public function testSetParentShouldRemoveFromOldParentPage()
    {
        $page1 = Page\AbstractPage::factory(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));

        $page2 = Page\AbstractPage::factory(array(
            'label' => 'Page 2',
            'uri' => '#'
        ));

        $page2->setParent($page1);
        $page2->setParent(null);

        $expected = array(
            'parent' => null,
            'haspages' => false
        );

        $actual = array(
            'parent' => $page2->getParent(),
            'haspages' => $page2->hasPages()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testFinderMethodsShouldWorkWithCustomProperties()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findOneBy('page2', 'page2');
        $this->assertInstanceOf('Zend\\Navigation\\Page\\AbstractPage', $found);
        $this->assertEquals('Page 2', $found->getLabel());
    }

    public function testFindOneByShouldReturnOnlyOnePage()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findOneBy('id', 'page_2_and_3');
        $this->assertInstanceOf('Zend\\Navigation\\Page\\AbstractPage', $found);
        $this->assertEquals('Page 2', $found->getLabel());
    }

    public function testFindOneByShouldReturnNullIfNotFound()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findOneBy('id', 'non-existant');
        $this->assertNull($found);
    }

    public function testFindAllByShouldReturnAllMatchingPages()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findAllBy('id', 'page_2_and_3');
        $this->assertContainsOnly('Zend\Navigation\Page\AbstractPage', $found, false);

        $expected = array('Page 2', 'Page 3');
        $actual = array();

        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindAllByShouldReturnEmptyArrayifNotFound()
    {
        $nav = $this->_getFindByNavigation();
        $found = $nav->findAllBy('id', 'non-existant');

        $expected = array('type' => 'array', 'count' => 0);
        $actual = array('type' => gettype($found), 'count' => count($found));
        $this->assertEquals($expected, $actual);
    }

    public function testFindByShouldDefaultToFindOneBy()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findBy('id', 'page_2_and_3');
        $this->assertInstanceOf('Zend\\Navigation\\Page\\AbstractPage', $found);
    }

    public function testFindOneByMagicMethodNativeProperty()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findOneById('page_2_and_3');
        $this->assertInstanceOf('Zend\\Navigation\\Page\\AbstractPage', $found);
        $this->assertEquals('Page 2', $found->getLabel());
    }

    public function testFindOneByMagicMethodCustomProperty()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findOneBypage2('page2');
        $this->assertInstanceOf('Zend\\Navigation\\Page\\AbstractPage', $found);
        $this->assertEquals('Page 2', $found->getLabel());
    }

    public function testFindAllByWithMagicMethodNativeProperty()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findAllById('page_2_and_3');
        $this->assertContainsOnly('Zend\Navigation\Page\\AbstractPage', $found, false);

        $expected = array('Page 2', 'Page 3');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindAllByMagicUcfirstPropDoesNotFindCustomLowercaseProps()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findAllByAction('about');
        $this->assertContainsOnly('Zend\Navigation\Page\\AbstractPage', $found, false);

        $expected = array('Page 3');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindAllByMagicLowercaseFindsBothNativeAndCustomProps()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findAllByaction('about');
        $this->assertContainsOnly('Zend\Navigation\Page\\AbstractPage', $found, false);

        $expected = array('Page 1.3', 'Page 3');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindByMagicMethodIsEquivalentToFindOneBy()
    {
        $nav = $this->_getFindByNavigation();

        $found = $nav->findById('page_2_and_3');
        $this->assertInstanceOf('Zend\\Navigation\\Page\\AbstractPage', $found);
        $this->assertEquals('Page 2', $found->getLabel());
    }

    public function testInvalidMagicFinderMethodShouldThrowException()
    {
        $nav = $this->_getFindByNavigation();

        try {
            $found = $nav->findSomeById('page_2_and_3');
            $this->fail('An invalid magic finder method was used, ' .
                        'but a Zend\Navigation\Exception\InvalidArgumentException was ' .
                        'not thrown');
        } catch (Navigation\Exception\BadMethodCallException $e) {
            $this->assertContains('Bad method call', $e->getMessage());
        }
    }

    public function testInvalidMagicMethodShouldThrowException()
    {
        $nav = $this->_getFindByNavigation();

        try {
            $found = $nav->getPagez();
            $this->fail('An invalid magic finder method was used, ' .
                        'but a Zend\Navigation\Exception\InvalidArgumentException was ' .
                        'not thrown');
        } catch (Navigation\Exception\BadMethodCallException $e) {
            $this->assertContains('Bad method call', $e->getMessage());
        }
    }

    protected function _getFindByNavigation()
    {
        // findAllByFoo('bar')         // Page 1, Page 1.1
        // findById('page_2_and_3')    // Page 2
        // findOneById('page_2_and_3') // Page 2
        // findAllById('page_2_and_3') // Page 2, Page 3
        // findAllByAction('about')    // Page 1.3, Page 3
        return new Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'uri'   => 'page-1',
                'foo'   => 'bar',
                'pages' => array(
                    array(
                        'label' => 'Page 1.1',
                        'uri'   => 'page-1.1',
                        'foo'   => 'bar',
                        'title' => 'The given title'
                    ),
                    array(
                        'label' => 'Page 1.2',
                        'uri'   => 'page-1.2',
                        'title' => 'The given title'
                    ),
                    array(
                        'type'   => 'uri',
                        'label'  => 'Page 1.3',
                        'uri'    => 'page-1.3',
                        'title'  => 'The given title',
                        'action' => 'about'
                    )
                )
            ),
            array(
                'id'         => 'page_2_and_3',
                'label'      => 'Page 2',
                'module'     => 'page2',
                'controller' => 'index',
                'action'     => 'page1',
                'page2'      => 'page2'
            ),
            array(
                'id'         => 'page_2_and_3',
                'label'      => 'Page 3',
                'module'     => 'page3',
                'controller' => 'index',
                'action'     => 'about'
            )
        ));
    }

    public function testCurrent()
    {
        $container = new Navigation\Navigation(array(
            array(
                'label' => 'Page 2',
                'type'  => 'uri'
            ),
            array(
                'label' => 'Page 1',
                'type'  => 'uri',
                'order' => -1
            )
        ));

        $page = $container->current();
        $this->assertEquals('Page 1', $page->getLabel());
    }

    public function testCurrentShouldThrowExceptionIfIndexIsInvalid()
    {
        $container = new \ZendTest\Navigation\TestAsset\AbstractContainer(array(
            array(
                'label' => 'Page 2',
                'type'  => 'uri'
            ),
            array(
                'label' => 'Page 1',
                'type'  => 'uri',
                'order' => -1
            )
        ));

        try {
            $page = $container->current();
            $this->fail('AbstractContainer index is invalid, ' .
                        'but a Zend\Navigation\Exception\InvalidArgumentException was ' .
                        'not thrown');
        } catch (Navigation\Exception\OutOfBoundsException $e) {
            $this->assertContains('Corruption detected', $e->getMessage());
        }
    }

    public function testKeyWhenContainerIsEmpty()
    {
        $container = new Navigation\Navigation();
        $this->assertEquals(null, $container->key());
    }

    public function testKeyShouldReturnCurrentPageHash()
    {
        $container = new Navigation\Navigation();
        $page = Page\AbstractPage::factory(array(
            'type' => 'uri'
        ));
        $container->addPage($page);

        $this->assertEquals($page->hashCode(), $container->key());
    }

    public function testGetChildrenShouldReturnTheCurrentPage()
    {
        $container = new Navigation\Navigation();
        $page = Page\AbstractPage::factory(array(
            'type' => 'uri'
        ));
        $container->addPage($page);

        $this->assertEquals($page, $container->getChildren());
    }

    public function testGetChildrenShouldReturnNullWhenContainerIsEmpty()
    {
        $container = new Navigation\Navigation();

        $this->assertEquals(null, $container->getChildren());
    }
}
