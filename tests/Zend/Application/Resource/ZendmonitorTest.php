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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Application_Resource_Resource */
require_once 'Zend/Application/Resource/Resource.php';

/** Zend_Application_Resource_ResourceAbstract */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/** Zend_Application_Resource_Zendmonitor */
require_once 'Zend/Application/Resource/Zendmonitor.php';

/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Log_Writer_ZendMonitor */
require_once 'Zend/Log/Writer/ZendMonitor.php';

/** Zend_Log_Writer_Mock */
require_once 'Zend/Log/Writer/Mock.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application_Resource
 */
class Zend_Application_Resource_ZendmonitorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->resource = new Zend_Application_Resource_Zendmonitor();
    }

    public function testGetLogLazyLoadsLog()
    {
        $log = $this->resource->getLog();
        $this->assertTrue($log instanceof Zend_Log);
    }

    public function testInitReturnsLogInstance()
    {
        $log = $this->resource->init();
        $this->assertTrue($log instanceof Zend_Log);
    }

    public function testInitReturnsSameLogInstanceAsGetter()
    {
        $log = $this->resource->getLog();
        $this->assertSame($log, $this->resource->init());
    }

    public function testSetterWillOverwriteExistingLogInstance()
    {
        $existing = $this->resource->getLog();
        $this->resource->setLog($log = new Zend_Log(new Zend_Log_Writer_Mock()));
        $this->assertNotSame($existing, $this->resource->getLog());
        $this->assertSame($log, $this->resource->getLog());
    }
}
