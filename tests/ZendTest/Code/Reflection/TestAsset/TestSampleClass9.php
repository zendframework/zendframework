<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection\TestAsset;

use ZendTest\Code\Reflection\TestAsset\SampleAnnotation as Sample;

class TestSampleClass9
    extends Sample
    implements \IteratorAggregate
{
    protected $_prop1 = null;

    /**
     * @Sample({"foo":"bar"})
     */
    protected $_prop2 = null;

    public function getProp1()
    {
        return $this->_prop1;
    }

    public function getProp2($param1, TestSampleClass $param2)
    {
        return $this->_prop2;
    }

    public function getIterator()
    {
        return array();
    }

}
