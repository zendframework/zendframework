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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see TestHelper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Reflection_Parameter
 */
require_once 'Zend/Reflection/Parameter.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Parameter
 */
class Zend_Reflection_ParameterTest extends PHPUnit_Framework_TestCase
{

    static protected $_sampleClassFileRequired = false;
    
    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
    }
    
    public function testDeclaringClassReturn()
    {
        $parameter = new Zend_Reflection_Parameter(array('Zend_Reflection_TestSampleClass2', 'getProp2'), 0);
        $this->assertEquals(get_class($parameter->getDeclaringClass()), 'Zend_Reflection_Class');
    }

    public function testClassReturn_NoClassGiven_ReturnsNull()
    {
        $parameter = new Zend_Reflection_Parameter(array('Zend_Reflection_TestSampleClass2', 'getProp2'), 'param1');

        $this->assertNull($parameter->getClass());
    }
    
    public function testClassReturn()
    {
        $parameter = new Zend_Reflection_Parameter(array('Zend_Reflection_TestSampleClass2', 'getProp2'), 'param2');
        $this->assertEquals(get_class($parameter->getClass()), 'Zend_Reflection_Class');
    }
    
    public function testTypeReturn()
    {
        $parameter = new Zend_Reflection_Parameter(array('Zend_Reflection_TestSampleClass5', 'doSomething'), 'two');
        $this->assertEquals($parameter->getType(), 'int');
    }
    
}

