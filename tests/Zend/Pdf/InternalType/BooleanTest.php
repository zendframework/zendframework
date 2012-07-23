<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendTest\Pdf\InternalType;

use Zend\Pdf\InternalType;

/**
 * \Zend\Pdf\InternalType\BooleanObject
 */

/**
 * PHPUnit Test Case
 */

/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @group      Zend_PDF
 */
class BooleanTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFBoolean()
    {
        $boolObj = new InternalType\BooleanObject(false);
        $this->assertTrue($boolObj instanceof InternalType\BooleanObject);
    }

    public function testPDFBooleanBadArgument()
    {
        $this->setExpectedException('\Zend\Pdf\Exception\RuntimeException', 'must be boolean');
        $boolObj = new InternalType\BooleanObject('some input');
    }

    public function testGetType()
    {
        $boolObj = new InternalType\BooleanObject((boolean) 100);
        $this->assertEquals($boolObj->getType(), InternalType\AbstractTypeObject::TYPE_BOOL);
    }

    public function testToString()
    {
        $boolObj = new InternalType\BooleanObject(true);
        $this->assertEquals($boolObj->toString(), 'true');
    }
}
