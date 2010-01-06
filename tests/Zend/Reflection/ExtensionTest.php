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
 * @see TestHelper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/** requires */
require_once 'Zend/Reflection/Extension.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Extension
 */
class Zend_Reflection_ExtensionTest extends PHPUnit_Framework_TestCase
{

    public function testClassReturn()
    {
        $extension = new Zend_Reflection_Extension('Reflection');
        $extensionClasses = $extension->getClasses();
        $this->assertEquals(get_class(array_shift($extensionClasses)), 'Zend_Reflection_Class');
    }

    public function testFunctionReturn()
    {
        $extension = new Zend_Reflection_Extension('Spl');
        $extensionFunctions = $extension->getFunctions();
        $this->assertEquals(get_class(array_shift($extensionFunctions)), 'Zend_Reflection_Function');
    }
}

