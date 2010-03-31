<?php
/**
 * License Info
 *
 * This is a test File docblock
 *
 * @author Ralph Schindler
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
 *
 * blah
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
abstract class Zend_Reflection_TestSampleClass3 extends ArrayObject
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
class Zend_Reflection_TestSampleClass5
{

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
 * TestSampleClass6 Docblock Short Desc
 *
 * Testing for formatted dockblock tags. See ZF-6726.
 * (This long description should be longer than 3 lines.
 * It indeed is longer than 3 lines
 * now.)
 *
 * @author Carlton Gibson <carlton.gibson@noumenal.co.uk>
 */
class Zend_Reflection_TestSampleClass6
{

    /**
     * Method ShortDescription
     *
     * Notice the multiple spaces aligning the columns in the docblock
     * tags. (This long description should be longer than 3 lines.
     * It indeed is longer than 3 lines
     * now.)
     *
     * @emptyTag
     * @descriptionTag           A tag with just a description
     * @param   int     $var     Description of $var
     * @return  string           Description of return value
     */
    public function doSomething($var)
    {
        //we need a multi-line method body.
        $assigned = 1;
        $alsoAssigined = 2;
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


class Zend_Reflection_Docblock_Param_WithNamespace
{
    /**
     * @param Zend\Foo\Bar $var desc
     * @return Zend\Reflection\Docblock
     */
    public function doSomething()
    {

    }
}
