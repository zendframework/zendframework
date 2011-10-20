<?php

namespace ZendTest\View\Helper\TestAsset;

class Stringified
{
    public function __toString()
    {
        return get_called_class();
    }
}
