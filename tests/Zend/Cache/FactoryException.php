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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'PHPUnit/Extensions/ExceptionTestCase.php';
require_once 'Zend/Cache.php';
 
/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_FactoryException extends PHPUnit_Extensions_ExceptionTestCase
{
    function setUp(){
        $this->setExpectedException('Zend_Cache_Exception');
    }
    
    public function testBadFrontend()
    {
        Zend_Cache::factory('badFrontend', 'File');
    }
    
    public function testBadBackend()
    {
        Zend_Cache::factory('Output', 'badBackend');
    }
    
    public function testFrontendBadParam()
    {
        Zend_Cache::factory('badFrontend', 'File', array('badParam'=>true));
    }
    
    public function testBackendBadParam()
    {
        Zend_Cache::factory('Output', 'badBackend', array(), array('badParam'=>true));
    }
    
    public function testThrowMethod()
    {
        Zend_Cache::throwException('test');
    }
}
