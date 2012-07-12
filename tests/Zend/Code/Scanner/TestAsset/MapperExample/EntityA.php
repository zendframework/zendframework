<?php

namespace ZendTest\Code\Scanner\TestAsset\MapperExample;

class EntityA
{

    public function __toString()
    {
        return 'I am a ' . get_class($this) . ' object (hash ' . spl_object_hash($this) . '), using this mapper object ';
    }

}
