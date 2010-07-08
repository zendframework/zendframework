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
    Zend\Application\Resource\Log as LogResource,
    Zend\Application,
    Zend\Controller\Front as FrontController;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class LogTest extends \PHPUnit_Framework_TestCase
{
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

        FrontController::getInstance()->resetInstance();
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
}
