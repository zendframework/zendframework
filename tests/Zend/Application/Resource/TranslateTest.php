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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Application\Resource;

use Zend\Loader\Autoloader,
    Zend\Application,
    Zend\Application\Resource\Translate as TranslateResource,
    Zend\Translator\Translator,
    Zend\Registry;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class TranslateTest extends \PHPUnit_Framework_TestCase
{
    private $_translationOptions = array('data' => array(
        'message1' => 'message1',
        'message2' => 'message2',
        'message3' => 'message3'
    ));

    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        Autoloader::resetInstance();
        $this->autoloader = Autoloader::getInstance();

        $this->application = new Application\Application('testing');

        $this->bootstrap = new Application\Bootstrap($this->application);

        Registry::_unsetInstance();
    }

    public function tearDown()
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Reset autoloader instance so it doesn't affect other tests
        Autoloader::resetInstance();
    }

    public function testInitializationInitializesTranslateObject()
    {
        $resource = new TranslateResource($this->_translationOptions);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue($resource->getTranslate() instanceof \Zend\Translator\Translator);
    }

    public function testInitializationReturnsLocaleObject()
    {
        $resource = new TranslateResource($this->_translationOptions);
        $resource->setBootstrap($this->bootstrap);
        $test = $resource->init();
        $this->assertTrue($test instanceof \Zend\Translator\Translator);
    }

    public function testOptionsPassedToResourceAreUsedToSetLocaleState()
    {
        $resource = new TranslateResource($this->_translationOptions);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $translate = $resource->getTranslate();
        $this->assertTrue(Registry::isRegistered('Zend_Translate'));
        $this->assertSame(Registry::get('Zend_Translate'), $translate);
    }

    public function testResourceThrowsExceptionWithoutData()
    {
        $this->setExpectedException('Zend\\Application\\ResourceException');
        $resource = new TranslateResource();
        $resource->getTranslate();
    }

    /**
     * @group ZF-7352
     */
    public function testTranslationIsAddedIfRegistryKeyExistsAlready()
    {
        $options1 = array('foo' => 'bar');
        $options2 = array_merge_recursive($this->_translationOptions,
                                          array('data' => array('message4' => 'bericht4')));

        $translate = new \Zend\Translator\Translator(\Zend\Translator\Translator::AN_ARRAY, $options1);
        Registry::set('Zend_Translate', $translate);

        $resource = new TranslateResource($options2);

        $this->assertTrue($translate === $resource->getTranslate());
        $this->assertEquals('bar', $translate->translate('foo'));
        $this->assertEquals('bericht4', $translate->translate('message4'));
        $this->assertEquals('shouldNotExist', $translate->translate('shouldNotExist'));
    }

    /**
     * @group ZF-10034
     */
    public function testSetCacheFromCacheManager()
    {
        $configCache = array(
            'translate' => array(
                'frontend' => array(
                    'name' => 'Core',
                    'options' => array(
                        'lifetime' => 120,
                        'automatic_serialization' => true
                    )
                ),
                'backend' => array(
                    'name' => 'Black Hole'
                )
            )
        );
        $this->bootstrap->registerPluginResource('cachemanager', $configCache);

        $options = $this->_translationOptions;
        $options['cache'] = 'translate';
        $resource = new TranslateResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

        $this->assertType('Zend\Cache\Frontend\Core', Translator::getCache());
        Translator::clearCache();
    }
}
