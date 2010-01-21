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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_MailTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * @see Zend_Application_Resource_Mail
 */
require_once 'Zend/Application/Resource/Mail.php';


/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_Resource_MailTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();
        $this->application = new Zend_Application('testing');
        $this->bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);

        Zend_Controller_Front::getInstance()->resetInstance();
    }

    public function tearDown()
    {
        Zend_Mail::clearDefaultTransport();
        
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function testInitializationInitializesMailObject()
    {
        $resource = new Zend_Application_Resource_Mail(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions(array('transport' => array('type' => 'sendmail')));
        $resource->init();
        $this->assertTrue($resource->getMail() instanceof Zend_Mail_Transport_Abstract);
        $this->assertTrue($resource->getMail() instanceof Zend_Mail_Transport_Sendmail);
    }

    public function testInitializationReturnsMailObject()
    {
        $resource = new Zend_Application_Resource_Mail(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions(array('transport' => array('type' => 'sendmail')));
        $resource->init();
        $this->assertTrue($resource->init() instanceof Zend_Mail_Transport_Abstract);
        $this->assertTrue(Zend_Mail::getDefaultTransport() instanceof Zend_Mail_Transport_Sendmail);
    }

    public function testOptionsPassedToResourceAreUsedToInitializeMailTransportSmtp()
    {
        // If host option isn't passed on, an exception is thrown, making this text effective
        $options = array('transport' => array('type' => 'smtp',
                                              'host' => 'example.com',
                                              'register' => true));
        $resource = new Zend_Application_Resource_Mail(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertTrue(Zend_Mail::getDefaultTransport() instanceof Zend_Mail_Transport_Smtp);
    }

    public function testNotRegisteringTransport()
    {
        // If host option isn't passed on, an exception is thrown, making this test effective
        $options = array('transport' => array('type' => 'sendmail',
                                              'register' => false));
        $resource = new Zend_Application_Resource_Mail(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertNull(Zend_Mail::getDefaultTransport()); 
    }
    
    public function testDefaultFromAndReplyTo()
    {
        $options = array('defaultfrom'    => array('email' => 'foo@example.com',
                                                   'name' => 'Foo Bar'),
                         'defaultreplyto' => array('email' => 'john@example.com',
                                                   'name' => 'John Doe'));
        $resource = new Zend_Application_Resource_Mail(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertNull(Zend_Mail::getDefaultTransport());
        $this->assertEquals($options['defaultfrom'], Zend_Mail::getDefaultFrom());
        $this->assertEquals($options['defaultreplyto'], Zend_Mail::getDefaultReplyTo());
    }

    /**
    * @group ZF-8811
    */
    public function testDefaultsCaseSensivity() {
        $options = array('defaultFroM'    => array('email' => 'f00@example.com', 'name' => null),
                         'defAultReplyTo' => array('email' => 'j0hn@example.com', 'name' => null));
        $resource = new Zend_Application_Resource_Mail(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertNull(Zend_Mail::getDefaultTransport());
        $this->assertEquals($options['defaultFroM'], Zend_Mail::getDefaultFrom());
        $this->assertEquals($options['defAultReplyTo'], Zend_Mail::getDefaultReplyTo());

    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_LogTest::main') {
    Zend_Application_Resource_LogTest::main();
}
