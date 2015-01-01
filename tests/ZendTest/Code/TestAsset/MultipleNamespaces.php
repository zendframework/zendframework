<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
