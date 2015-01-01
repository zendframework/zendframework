<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection\TestAsset;


/**
 * TestSampleClass6 DocBlock Short Desc
 *
 * Testing for formatted dockblock tags. See ZF-6726.
 * (This long description should be longer than 3 lines.
 * It indeed is longer than 3 lines
 * now.)
 *
 * @author Carlton Gibson <carlton.gibson@noumenal.co.uk>
 */
class TestSampleClass6
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
