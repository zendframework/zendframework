<?php

namespace ZendTest\Dojo\TestAsset;

class DataObject
{
    public $item = array(
        'id'    => 'foo',
        'title' => 'Foo',
        'email' => 'foo@foo.com',
    );

    public function toArray()
    {
        return $this->item;
    }
}


