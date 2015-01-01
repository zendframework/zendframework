<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Helper\BasePath;

/**
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
