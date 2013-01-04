<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace ZendTest\Json\TestAsset;

/**
 * @see ZF-12347
 */
class TestIteratorAggregate implements \IteratorAggregate
{
    protected $array = array(
        'foo' => 'bar',
        'baz' => 5
    );

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }
}
