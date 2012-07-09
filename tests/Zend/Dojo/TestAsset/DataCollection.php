<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\TestAsset;

class DataCollection implements \IteratorAggregate
{
    public $items = array();

    public function __construct()
    {
        for ($i = 1; $i < 4; ++$i) {
            $item = new DataObject;
            $item->item['id'] = $i;
            $item->item['title'] .= $i;
            $this->items[] = $item;
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
