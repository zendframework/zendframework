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

require_once 'Zend/Reflection/File.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Docblock
 * @group      Zend_Reflection_Docblock_Tag
 */
class Zend_Reflection_Docblock_TagTest extends PHPUnit_Framework_TestCase
{
    

    static protected $_sampleClassFileRequired = false;
    
    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(dirname(__FILE__)) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
    }
    
    public function testTagDescriptionIsReturned()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');

        $authorTag = $classReflection->getDocblock()->getTag('author');
        $this->assertEquals($authorTag->getDescription(), 'Ralph Schindler <ralph.schindler@zend.com>');
    }

    public function testTagShouldAllowJustTagNameInDocblockTagLine()
    {
    	$classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass6');
    	
        $tag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('emptyTag');
        $this->assertEquals($tag->getName(), 'emptyTag', 'Factory First Match Failed');
    }
    
    public function testTagShouldAllowMultipleWhitespacesBeforeDescription()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass6');
    	
        $tag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('descriptionTag');
        $this->assertEquals($tag->getDescription(), 'A tag with just a description', 'Final Match Failed');
    }

    public function testToString()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass6');

        $tag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('descriptionTag');

        $expectedString = "Docblock Tag [ * @descriptionTag ]".PHP_EOL;

        $this->assertEquals($expectedString, (string)$tag);
    }
}