<?php

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
