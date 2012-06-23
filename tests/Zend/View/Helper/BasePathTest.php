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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Helper\BasePath;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class BasePathTest extends TestCase
{
    public function testBasePathWithoutFile()
    {
        $helper = new BasePath();
        $helper->setBasePath('/foo');
        
        $this->assertEquals('/foo', $helper());
    }
    
    public function testBasePathWithFile()
    {
        $helper = new BasePath();
        $helper->setBasePath('/foo');
        
        $this->assertEquals('/foo/bar', $helper('bar'));
    }
    
    public function testBasePathNoDoubleSlashes()
    {
        $helper = new BasePath();
        $helper->setBasePath('/');

        $this->assertEquals('/', $helper('/'));
    }

    public function testBasePathWithFilePrefixedBySlash()
    {
        $helper = new BasePath();
        $helper->setBasePath('/foo');
        
        $this->assertEquals('/foo/bar', $helper('/bar'));
    }
}
