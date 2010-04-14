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
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Reflection;
use Zend\Reflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Function
 */
class ReflectionFunctionTest extends \PHPUnit_Framework_TestCase
{
    public function testParemeterReturn()
    {
        $function = new Reflection\ReflectionFunction('array_splice');
        $parameters = $function->getParameters();
        $this->assertEquals(count($parameters), 4);
        $this->assertEquals(get_class(array_shift($parameters)), 'Zend\Reflection\ReflectionParameter');
    }

    public function testFunctionDocblockReturn()
    {
        require_once __DIR__ . '/TestAsset/functions.php';
        $function = new Reflection\ReflectionFunction('ZendTest\Reflection\TestAsset\function6');
        $this->assertEquals(get_class($function->getDocblock()), 'Zend\Reflection\ReflectionDocblock');
    }
}
