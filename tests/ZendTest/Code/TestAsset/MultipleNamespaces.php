<?php

namespace ZendTest\Code\TestAsset {

    use Foo\Bar;
    use A\B\C as X;

    class Baz
    {
        public function __construct(Bar\Boo $boo, Bam $bam)
        {

        }
    }

    use Something\More as SM;
    use OtherThing\SomethingElse;

    class ExtendingSomethingMore extends SM\Blah
    {

    }

}


namespace {

    use X\Y\Z;

    class Foo
    {
        public function setGlobalStuff(GlobalStuff $stuff)
        {

        }
    }

}
