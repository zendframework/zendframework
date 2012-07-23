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
 * \Zend\Pdf\InternalType\NullObject
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
class NullTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFNull()
    {
        $nullObj = new InternalType\NullObject();
        $this->assertTrue($nullObj instanceof InternalType\NullObject);
    }

    public function testGetType()
    {
        $nullObj = new InternalType\NullObject();
        $this->assertEquals($nullObj->getType(), InternalType\AbstractTypeObject::TYPE_NULL);
    }

    public function testToString()
    {
        $nullObj = new InternalType\NullObject();
        $this->assertEquals($nullObj->toString(), 'null');
    }
}
