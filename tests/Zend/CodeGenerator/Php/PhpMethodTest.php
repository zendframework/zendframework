<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @namespace
 */
namespace ZendTest\CodeGenerator\Php;
use Zend\CodeGenerator\Php;

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 */
class PhpMethodTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @var Zend_CodeGenerator_Php_Method
     */
    protected $_method = null;

    public function setup()
    {
        $this->_method = new Php\PhpMethod();
    }

    public function teardown()
    {
        $this->_method = null;
    }

    public function testMethodConstructor()
    {
        $codeGenMethod = new Php\PhpMethod();
        $this->isInstanceOf($codeGenMethod, '\Zend\CodeGenerator\Php\PhpMethod');
    }

    public function testMethodParameterAccessors()
    {
        $codeGen = new Php\PhpMethod();
        $codeGen->setParameters(array(
            array('name' => 'one')
            ));
        $params = $codeGen->getParameters();
        $param = array_shift($params);
        $this->assertTrue($param instanceof \Zend\CodeGenerator\Php\PhpParameter, 'Failed because $param was not instance of Zend_CodeGenerator_Php_Property');
    }

    public function testMethodBodyGetterAndSetter()
    {
        $this->_method->setBody('Foo');
        $this->assertEquals('Foo', $this->_method->getBody());
    }

    public function testDocblockGetterAndSetter()
    {
        $d = new \Zend\CodeGenerator\Php\PhpDocblock();

        $this->_method->setDocblock($d);
        $this->assertTrue($d === $this->_method->getDocblock());
    }


    public function testMethodFromReflection()
    {
        $ref = new \Zend\Reflection\ReflectionMethod('\ZendTest\CodeGenerator\Php\TestAsset\TestSampleSingleClass', 'someMethod');

        $codeGenMethod = Php\PhpMethod::fromReflection($ref);
        $target = <<<EOS
    /**
     * Enter description here...
     * 
     * @return bool
     * 
     */
    public function someMethod()
    {
        /* test test */
    }

EOS;
        $this->assertEquals($target, (string) $codeGenMethod);
    }

    /**
     * @group ZF-6444
     */
    public function testMethodWithStaticModifierIsEmitted()
    {
        $codeGen = new Php\PhpMethod();
        $codeGen->setName('foo');
        $codeGen->setParameters(array(
            array('name' => 'one')
            ));
        $codeGen->setStatic(true);

        $expected = <<<EOS
    public static function foo(\$one)
    {
    }

EOS;

        $this->assertEquals($expected, $codeGen->generate());
    }

    /**
     * @group ZF-6444
     */
    public function testMethodWithFinalModifierIsEmitted()
    {
        $codeGen = new Php\PhpMethod();
        $codeGen->setName('foo');
        $codeGen->setParameters(array(
            array('name' => 'one')
            ));
        $codeGen->setFinal(true);

        $expected = <<<EOS
    final public function foo(\$one)
    {
    }

EOS;
        $this->assertEquals($expected, $codeGen->generate());
    }

    /**
     * @group ZF-6444
     */
    public function testMethodWithFinalModifierIsNotEmittedWhenMethodIsAbstract()
    {
        $codeGen = new Php\PhpMethod();
        $codeGen->setName('foo');
        $codeGen->setParameters(array(
            array('name' => 'one')
            ));
        $codeGen->setFinal(true);
        $codeGen->setAbstract(true);

        $expected = <<<EOS
    abstract public function foo(\$one)
    {
    }

EOS;
        $this->assertEquals($expected, $codeGen->generate());
    }

    /**
     * @group ZF-7205
     */
    public function testMethodCanHaveDocblock()
    {
        $codeGenProperty = new Php\PhpMethod(array(
            'name' => 'someFoo',
            'static' => true,
            'visibility' => 'protected',
            'docblock' => '@var string $someVal This is some val'
            ));

        $expected = <<<EOS
    /**
     * @var string \$someVal This is some val
     */
    protected static function someFoo()
    {
    }

EOS;
        $this->assertEquals($expected, $codeGenProperty->generate());
    }

}
