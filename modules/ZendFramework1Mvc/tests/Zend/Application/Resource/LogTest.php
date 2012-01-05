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
    Zend\Application\Resource\Log as LogResource,
    Zend\Application,
    Zend\Controller\Front as FrontController;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class LogTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application\Application('testing');
        $this->bootstrap = new Application\Bootstrap($this->application);

        FrontController::getInstance()->resetInstance();
    }

    public function tearDown()
    {
    }

    public function testInitializationInitializesLogObject()
    {
        $resource = new LogResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions(array(
            'Mock' => array('writerName' => 'Mock'),
        ));
        $resource->init();
        $this->assertTrue($resource->getLog() instanceof \Zend\Log\Logger);
    }

    public function testInitializationReturnsLogObject()
    {
        $resource = new LogResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions(array(
            'Mock' => array('writerName' => 'Mock'),
        ));
        $test = $resource->init();
        $this->assertTrue($test instanceof \Zend\Log\Logger);
    }

    public function testOptionsPassedToResourceAreUsedToInitializeLog()
    {
        $stream = fopen('php://memory', 'w+', false);
        $options = array('memory' => array(
            'writerName'   => 'Stream',
            'writerParams' => array(
                'stream' => $stream,
            )
        ));

        $resource = new LogResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

        $log      = $resource->getLog();
        $this->assertTrue($log instanceof \Zend\Log\Logger);

        $log->log($message = 'logged-message', \Zend\Log\Logger::INFO);
        rewind($stream);
        $this->assertContains($message, stream_get_contents($stream));
    }

    /**
     * @group ZF-8602
     */
    public function testNumericLogStreamFilterParamsPriorityDoesNotFail()
    {
        $options = array(
            'stream' => array(
                'writerName'   => 'Stream',
                'writerParams' => array(
                    'stream' => "php://memory",
                    'mode'   => 'a'
                ),
                'filterName' => 'Priority',
                'filterParams' => array(
                    'priority' => 4,
                ),
            ),
        );
        $resource = new LogResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
    }

    /**
     * @group ZF-9790
     */
    public function testInitializationWithFilterAndFormatter()
    {
        $stream = fopen('php://memory', 'w+');
        $options = array(
            'memory' => array(
                'writerName' => 'Stream',
                'writerParams' => array(
                     'stream' => $stream,
                ),
                'filterName' => 'Priority',
                'filterParams' => array(
                    'priority' => \Zend\Log\Logger::INFO,
                ),
                'formatterName' => 'Simple',
                'formatterParams' => array(
                    'format' => '%timestamp%: %message%',
                )
            )
        );
        $message = 'tottakai';

        $resource = new LogResource($options);
        $resource->setBootstrap($this->bootstrap);
        $log = $resource->init();

        $this->assertInstanceOf('Zend\Log\Logger', $log);

        $log->log($message, \Zend\Log\Logger::INFO);
        rewind($stream);
        $contents = stream_get_contents($stream);

        $this->assertStringEndsWith($message, $contents);
        $this->assertRegexp('/\d\d:\d\d:\d\d/', $contents);
    }
}
