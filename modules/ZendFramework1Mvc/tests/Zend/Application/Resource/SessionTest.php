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
    Zend\Application\Resource\Session as SessionResource,
    Zend\Controller\Front as FrontController;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @group      Zend_Application
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    public $resource;

    public function setUp()
    {
        $this->resource = new SessionResource();
    }

    public function testReturnsSessionManager()
    {
        $sessionManager = $this->resource->init();
        $this->assertInstanceOf('Zend\Session\Manager', $sessionManager);
    }

    /**
     * @group disable
     */
    public function testSetSaveHandler()
    {
        $saveHandler = $this->getMock('Zend\Session\SaveHandler');

        $this->resource->setSaveHandler($saveHandler);
        $this->assertSame($saveHandler, $this->resource->getSaveHandler());
    }

    public function testSetSaveHandlerString()
    {
        $saveHandlerClassName = 'ZendTest\\Application\\TestAsset\\SessionHandlerMock1';

        $this->resource->setSaveHandler($saveHandlerClassName);

        $this->assertInstanceOf($saveHandlerClassName, $this->resource->getSaveHandler());
    }

    public function testSetSaveHandlerArray()
    {
        $saveHandlerClassName = 'ZendTest\\Application\\TestAsset\\SessionHandlerMock1';

        $this->resource->setSaveHandler(array('class' => $saveHandlerClassName));

        $this->assertInstanceOf($saveHandlerClassName, $this->resource->getSaveHandler());
    }

    public function testSetOptions()
    {
        $this->resource->setOptions(array(
             'use_only_cookies' => true,
             'remember_me_seconds' => 7200,
        ));

        $sessionManager = $this->resource->init();
        $config = $sessionManager->getConfig();

        $this->assertTrue($config->getUseOnlyCookies());
        $this->assertEquals(7200, $config->getRememberMeSeconds());
    }

    public function testSaveManagerInjectedWithSessionManager()
    {
        $this->resource->setOptions(array(
            'use_only_cookies'    => true,
            'remember_me_seconds' => 7200,
            'savehandler'         => array(
                'class' => 'ZendTest\\Application\\TestAsset\\SessionHandlerMock1',
            ),
        ));

        $sessionManager = $this->resource->init();
        $saveHandler = $this->resource->getSaveHandler();
        $this->assertSame($sessionManager, $saveHandler->getManager());
    }
}
