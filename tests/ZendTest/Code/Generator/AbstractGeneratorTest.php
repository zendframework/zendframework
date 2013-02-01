<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Generator;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class AbstractGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $generator = $this->getMockForAbstractClass('Zend\Code\Generator\AbstractGenerator', array(
            array(
                'indentation' => 'foo',
            )
        ));

        $this->assertInstanceOf('Zend\Code\Generator\GeneratorInterface', $generator);
        $this->assertEquals('foo', $generator->getIndentation());
    }
}
