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
    Zend\Application\Resource\Mail as MailResource,
    Zend\Application,
    Zend\Controller\Front as FrontController,
    Zend\Mail\Mail,
    ZendTest\Application\Resource\TestAsset\CustomMailTranSPorT;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class MailTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application\Application('testing');
        $this->bootstrap = new Application\Bootstrap($this->application);

        FrontController::getInstance()->resetInstance();
    }

    public function tearDown()
    {
        Mail::clearDefaultTransport();
    }

    public function testInitializationInitializesMailObject()
    {
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions(array('transport' => array('type' => 'sendmail')));
        $resource->init();
        $this->assertTrue($resource->getMail() instanceof \Zend\Mail\AbstractTransport);
        $this->assertTrue($resource->getMail() instanceof \Zend\Mail\Transport\Sendmail);
    }

    public function testInitializationReturnsMailObject()
    {
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions(array('transport' => array('type' => 'sendmail')));
        $resource->init();
        $this->assertTrue($resource->init() instanceof \Zend\Mail\AbstractTransport);
        $this->assertTrue(Mail::getDefaultTransport() instanceof \Zend\Mail\Transport\Sendmail);
    }

    public function testOptionsPassedToResourceAreUsedToInitializeMailTransportSmtp()
    {
        // If host option isn't passed on, an exception is thrown, making this text effective
        $options = array('transport' => array('type' => 'smtp',
                                              'host' => 'example.com',
                                              'register' => true));
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertTrue(Mail::getDefaultTransport() instanceof \Zend\Mail\Transport\Smtp);
    }

    public function testNotRegisteringTransport()
    {
        // If host option isn't passed on, an exception is thrown, making this test effective
        $options = array('transport' => array('type' => 'sendmail',
                                              'register' => false));
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertNull(Mail::getDefaultTransport());
    }

    public function testDefaultFromAndReplyTo()
    {
        $options = array('defaultfrom'    => array('email' => 'foo@example.com',
                                                   'name' => 'Foo Bar'),
                         'defaultreplyto' => array('email' => 'john@example.com',
                                                   'name' => 'John Doe'));
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertNull(Mail::getDefaultTransport());
        $this->assertEquals($options['defaultfrom'], Mail::getDefaultFrom());
        $this->assertEquals($options['defaultreplyto'], Mail::getDefaultReplyTo());
    }

    /**
     * Got notice: Undefined index:  type
     */
    public function testDefaultTransport() {
        $options = array('transport' => array(//'type' => 'sendmail', // dont define type
                                              'register' => true));
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertTrue(Mail::getDefaultTransport() instanceof \Zend\Mail\Transport\Sendmail);
    }

    /**
    * @group ZF-8811
    */
    public function testDefaultsCaseSensivity() {
        $options = array('defaultFroM'    => array('email' => 'f00@example.com', 'name' => null),
                         'defAultReplyTo' => array('email' => 'j0hn@example.com', 'name' => null));
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertNull(Mail::getDefaultTransport());
        $this->assertEquals($options['defaultFroM'], Mail::getDefaultFrom());
        $this->assertEquals($options['defAultReplyTo'], Mail::getDefaultReplyTo());
    }

    /**
     * @group ZF-8981
     */
    public function testNumericRegisterDirectiveIsPassedOnCorrectly() {
        $options = array('transport' => array('type' => 'sendmail',
                                              'register' => '1')); // Culprit
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $resource->init();
        $this->assertTrue(Mail::getDefaultTransport() instanceof \Zend\Mail\Transport\Sendmail);
    }

    /**
     * @group ZF-9136
     */
    public function testCustomMailTransportWithFQName()
    {
        $options = array('transport' => array('type' => 'Zend\Mail\Transport\Sendmail'));
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $this->assertTrue($resource->init() instanceof \Zend\Mail\Transport\Sendmail);
    }

    /**
     * @group ZF-9136
     */
    public function testCustomMailTransportWithWrongCasesAsShouldBe()
    {
        $options = array('transport' => array('type' => 'ZendTest\Application\Resource\TestAsset\CustomMailTranSPorT'));
        $resource = new MailResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $this->assertTrue($resource->init() instanceof CustomMailTranSPorT);
    }
}
