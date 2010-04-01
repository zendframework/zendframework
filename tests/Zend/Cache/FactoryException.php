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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache;
use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FactoryException extends \PHPUnit_Extensions_ExceptionTestCase
{
    function setUp(){
        $this->setExpectedException('Cache\Exception');
    }

    public function testBadFrontend()
    {
        Cache\Cache::factory('badFrontend', 'File');
    }

    public function testBadBackend()
    {
        Cache\Cache::factory('Output', 'badBackend');
    }

    public function testFrontendBadParam()
    {
        Cache\Cache::factory('badFrontend', 'File', array('badParam'=>true));
    }

    public function testBackendBadParam()
    {
        Cache\Cache::factory('Output', 'badBackend', array(), array('badParam'=>true));
    }

    public function testThrowMethod()
    {
        Cache\Cache::throwException('test');
    }
}
