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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Application\Resource;

use Zend\Loader\Autoloader,
    Zend\Application\Resource\Locale as LocaleResource,
    Zend\Application,
    Zend\Registry;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class LocaleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application\Application('testing');

        $this->bootstrap = new Application\Bootstrap($this->application);

        Registry::_unsetInstance();
    }

    public function tearDown()
    {
    }

    public function testInitializationInitializesLocaleObject()
    {
        $resource = new LocaleResource(array());
        $resource->init();
        $this->assertTrue($resource->getLocale() instanceof \Zend\Locale\Locale);
    }

    public function testInitializationReturnsLocaleObject()
    {
        $resource = new LocaleResource(array());
        $resource->setBootstrap($this->bootstrap);
        $test = $resource->init();
        $this->assertTrue($test instanceof \Zend\Locale\Locale);
    }

    public function testOptionsPassedToResourceAreUsedToSetLocaleState()
    {
        $options = array(
            'default'      => 'kok_IN',
            'registry_key' => 'Foo_Bar',
            'force'        => true
        );

        $resource = new LocaleResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $locale   = $resource->getLocale();
        $this->assertEquals('kok_IN', $locale->__toString());
        $this->assertTrue(Registry::isRegistered('Foo_Bar'));
        $this->assertSame(Registry::get('Foo_Bar'), $locale);
    }

    public function testOptionsPassedToResourceAreUsedToSetLocaleState1()
    {
        $this->markTestSkipped('Skipped until Zend\Locale and the Resource can be further examined. Logic in the resource and in Locale do not match up.');
        $options = array(
            'default'      => 'kok_IN',
        );

        $resource = new LocaleResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $locale   = $resource->getLocale();
        var_dump($locale->__toString());
        // This test will fail if your configured locale is kok_IN
        $this->assertFalse('kok_IN' == $locale->__toString());
        $this->assertSame(Registry::get('Zend_Locale'), $locale);
    }
}
