<?php

namespace ZendTest\Amf\TestAsset;

/*
 * Used to test recursive cyclic references in the serializer.
 *@group ZF-6205
 */
class ReferenceTest 
{
    public function getReference() 
    {
        $o = new TestObject();
        $o->recursive = new TestObject();
        $o->recursive->recursive = $o;
        return $o;
    }
}

