<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\PropertyValueGenerator;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class PropertyValueGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testPropertyValueAddsSemicolonToValueGenerator()
    {
        $value = new PropertyValueGenerator('foo');
        $this->assertEquals('\'foo\';', $value->generate());
    }
}
