<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\AnnotationScanner,
    Zend\Code\NameInformation,
    Zend\Code\Annotation\AnnotationManager;

class AnnotationScannerTest extends \PHPUnit_Framework_TestCase
{

    public function testScannerWorks()
    {
        $annotationManager = new AnnotationManager(array(
            $foo = new TestAsset\Annotation\Foo(),
            $bar = new TestAsset\Annotation\Bar()
        ));

        $docComment = '/**' . "\n"
            . ' * @Test\Foo(\'anything I want()' . "\n" . ' * to be\')' . "\n"
            . ' * @Test\Bar' . "\n */";

        $nameInfo = new NameInformation();
        $nameInfo->addUse('ZendTest\Code\Scanner\TestAsset\Annotation', 'Test');

        $annotationScanner = new AnnotationScanner($annotationManager, $docComment, $nameInfo);
        $this->assertEquals(get_class($foo), get_class($annotationScanner[0]));
        $this->assertEquals("'anything I want()\n to be'", $annotationScanner[0]->getContent());
        $this->assertEquals(get_class($bar), get_class($annotationScanner[1]));
    }

}