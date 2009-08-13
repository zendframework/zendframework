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
 * @version    $Id:$
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Reflection_File
 */
require_once 'Zend/Reflection/File.php';

/**
 * This is a sample class docblock
 */
class Zend_Reflection_TestSampleClass extends ArrayObject
{
    
}


class Zend_Reflection_TestSampleClass2 implements IteratorAggregate  
{
    
    protected $_prop1 = null;
    protected $_prop2 = null;
    
    public function getProp1()
    {
        return $this->_prop1;
    }
    
    public function getProp2($param1, Zend_Reflection_TestSampleClass $param2)
    {
        return $this->_prop2;
    }
    
    public function getIterator()
    {
        return array();
    }
    
}


/**
 * Blah Blah
 *
 */
abstract class Zend_Reflection_TestSampleClass3 extends ArrayObject implements Iterator, Traversable
{


}

interface Zend_Reflection_TestSampleClassInterface
{
    
}

class Zend_Reflection_TestSampleClass4 implements Zend_Reflection_TestSampleClassInterface
{


}

/**
 * TestSampleClass5 Docblock Short Desc
 * 
 * This is a long description for 
 * the docblock of this class, it
 * should be longer than 3 lines.
 * It indeed is longer than 3 lines
 * now.
 * 
 * @author Ralph Schindler <ralph.schindler@zend.com>
 */
class Zend_Reflection_TestSampleClass5 {

    /**
     * Method ShortDescription
     * 
     * Method LongDescription
     * This is a long description for 
     * the docblock of this class, it
     * should be longer than 3 lines.
     * It indeed is longer than 3 lines
     * now.
     * 
     * @param int $one Description for one
     * @param int Description for two
     * @param string $three Description for three
     *                      which spans multiple lines
     * @return mixed Some return descr
     */
    public function doSomething($one, $two = 2, $three = 'three')
    {
        return 'mixedValue';
    }

    /**
     * Method ShortDescription
     * 
     * @param int $one Description for one
     * @param int Description for two
     * @param string $three Description for three
     *                      which spans multiple lines
     * @return int
     */
    public function doSomethingElse($one, $two = 2, $three = 'three')
    {
        return 'mixedValue';
    }
    
}



/**
 * Enter description here...
 *
 * @param string $one
 * @param int $two
 * @return true
 */
function zend_reflection_test_sample_function6($one, $two = 2) {
    return true;
}
