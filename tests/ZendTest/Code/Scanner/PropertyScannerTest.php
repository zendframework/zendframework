<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\FileScanner;
use Zend\Code\Scanner\TokenArrayScanner;
use PHPUnit_Framework_TestCase as TestCase;

class PropertyScannerTest extends TestCase
{
    public function testPropertyScannerHasPropertyInformation()
    {
        $file = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');

        $property = $class->getProperty('bar');
        $this->assertEquals('bar', $property->getName());
        $this->assertEquals('value', $property->getValue());
        $this->assertFalse($property->isPublic());
        $this->assertTrue($property->isProtected());
        $this->assertFalse($property->isPrivate());
        $this->assertTrue($property->isStatic());

        $property = $class->getProperty('foo');
        $this->assertEquals('foo', $property->getName());
        $this->assertEquals('value2', $property->getValue());
        $this->assertTrue($property->isPublic());
        $this->assertFalse($property->isProtected());
        $this->assertFalse($property->isPrivate());
        $this->assertFalse($property->isStatic());

        $property = $class->getProperty('baz');
        $this->assertEquals('baz', $property->getName());
        $this->assertEquals(3, $property->getValue());
        $this->assertFalse($property->isPublic());
        $this->assertFalse($property->isProtected());
        $this->assertTrue($property->isPrivate());
        $this->assertFalse($property->isStatic());
    }

    /**
     * @group 5384
     */
    public function testPropertyScannerReturnsProperValue()
    {
        $class = <<<'CLASS'
<?php
class Foo
{
    protected $empty;
    private $string = 'string';
    private $int = 123;
    private $array = array('test' => 2,2);
    private $arraynew = ['test' => 2,2];
    private $notarray = "['test' => 2,2]";
    private $status = false;
}
CLASS;

        $tokenScanner = new TokenArrayScanner(token_get_all($class));
        $fooClass = $tokenScanner->getClass('Foo');
        foreach ($fooClass->getProperties() as $property) {
            $value = $property->getValue();
            $valueType = $property->getValueType();
            switch ($property->getName()) {
                case "empty":
                    $this->assertNull($value);
                    $this->assertEquals('unknown', $valueType);
                    break;
                case "string":
                    $this->assertEquals('string', $value);
                    $this->assertEquals('string', $valueType);
                    break;
                case "int":
                    $this->assertEquals('123', $value);
                    $this->assertEquals('int', $valueType);
                    break;
                case "array":
                    $this->assertEquals("array('test'=>2,2)", $value);
                    $this->assertEquals('array', $valueType);
                    break;
                case "arraynew":
                    $this->assertEquals("['test'=>2,2]", $value);
                    $this->assertEquals('array', $valueType);
                    break;
                case "notarray":
                    $this->assertEquals('string', $valueType);
                    break;
                case "status":
                    $this->assertEquals('false', $value);
                    $this->assertEquals('boolean', $valueType);
                    break;
            }
        }
    }
}
