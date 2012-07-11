<?php

namespace ZendTest\Stdlib\TestAsset
{
    interface TestInterface
    {
    }

    class ParentClass implements TestInterface
    {
    }

    class ChildClass extends ParentClass
    {
    }
}
