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
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\DocBlockGenerator;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class DocBlockGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testShortDescriptionGetterAndSetter()
    {
        $docblockGenerator = new DocBlockGenerator();
        $docblockGenerator->setShortDescription('Short Description');
        $this->assertEquals('Short Description', $docblockGenerator->getShortDescription());
    }

    public function testLongDescriptionGetterAndSetter()
    {
        $docblockGenerator = new DocBlockGenerator();
        $docblockGenerator->setLongDescription('Long Description');
        $this->assertEquals('Long Description', $docblockGenerator->getLongDescription());
    }

    public function testTagGettersAndSetters()
    {
        $this->markTestIncomplete('Must refactor DocBlock like Reflecion tag first.');
//        $this->_docblock->setTag(array('name' => 'blah'));
//        $this->_docblock->setTag(new \Zend\Code\Generator\DocBlock\Tag\Param(array('datatype' => 'string')));
//        $this->_docblock->setTag(new \Zend\Code\Generator\DocBlock\Tag\Return(array('datatype' => 'int')));
//        $this->assertEquals(3, count($this->_docblock->getTags()));
//
//        $target = <<<EOS
///**
// * @blah 
// * @param string
// * @return int
// */
//
//EOS;
//
//        $this->assertEquals($target, $this->_docblock->generate());

    }

    public function testGenerationOfDocBlock()
    {
        $docblockGenerator = new DocBlockGenerator();
        $docblockGenerator->setShortDescription('@var Foo this is foo bar');

        $expected = '/**' . DocBlockGenerator::LINE_FEED . ' * @var Foo this is foo bar'
            . DocBlockGenerator::LINE_FEED . ' */' . DocBlockGenerator::LINE_FEED;
        $this->assertEquals($expected, $docblockGenerator->generate());
    }

}
