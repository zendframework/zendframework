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
    Zend\Application,
    Zend\Application\Resource\Translator as TranslateResource,
    Zend\Translator\Translator,
    Zend\Registry;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    private $_translationOptions = array('content' => array(
        'message1' => 'message1',
        'message2' => 'message2',
        'message3' => 'message3'
    ));

    public function setUp()
    {
        $this->application = new Application\Application('testing');
        $this->bootstrap = new Application\Bootstrap($this->application);
        Registry::_unsetInstance();
    }

    public function tearDown()
    {
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
        $this->assertTrue(Registry::isRegistered('Zend_Translator'));
        $this->assertSame(Registry::get('Zend_Translator'), $translate);
    }

    public function testResourceThrowsExceptionWithoutData()
    {
        $this->setExpectedException('Zend\Application\Resource\Exception\InitializationException');
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
                                          array('content' => array('message4' => 'bericht4')));

        $translate = new \Zend\Translator\Translator(\Zend\Translator\Translator::AN_ARRAY, $options1);
        Registry::set('Zend_Translator', $translate);

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
        $this->markTestSkipped('TranslateResource has fatal error - skip this test now.');
        return;

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

        $this->assertInstanceOf('Zend\Cache\Frontend\Core', Translator::getCache());
        Translator::clearCache();
    }
}
