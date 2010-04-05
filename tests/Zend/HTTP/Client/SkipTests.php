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
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class Zend_Http_Client_Skip_SocketTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped("Zend_Http_Client dynamic tests are not enabled in TestConfiguration.php");
    }

    public function testSocket()
    {
        // this is here only so we have at least one test
    }
}

/**
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class Zend_Http_Client_Skip_ProxyAdapterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped("Zend_Http_Client proxy server tests are not enabled in TestConfiguration.php");
    }

    public function testProxyAdapter()
    {
        // this is here only so we have at least one test
    }
}
