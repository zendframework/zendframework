<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\AbstractMemberGenerator;
use Zend\Code\Generator\Exception\InvalidArgumentException;

class AbstractMemberGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractMemberGenerator
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = $this->getMockForAbstractClass('Zend\Code\Generator\AbstractMemberGenerator');
    }

    public function testSetFlagsWithArray()
    {
        $this->fixture->setFlags(
            array(
                AbstractMemberGenerator::FLAG_FINAL,
                AbstractMemberGenerator::FLAG_PUBLIC,
            )
        );

        $this->assertEquals(AbstractMemberGenerator::VISIBILITY_PUBLIC, $this->fixture->getVisibility());
        $this->assertEquals(true, $this->fixture->isFinal());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetDocBlockThrowsExceptionWithInvalidType()
    {
        $this->fixture->setDocBlock(new \stdClass());
    }
}
