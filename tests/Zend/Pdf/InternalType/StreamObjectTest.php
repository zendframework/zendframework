<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendTest\Pdf\InternalType\Object;

use Zend\Pdf\InternalType;
use Zend\Pdf\ObjectFactory;

/**
 * \Zend\Pdf\InternalType\StreamObject
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
class StreamObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFStreamObject()
    {
        $obj = new InternalType\StreamObject('some data', 1, 0, new ObjectFactory(1));
        $this->assertTrue($obj instanceof InternalType\StreamObject);
    }

    public function testGetType()
    {
        $obj = new InternalType\StreamObject('some data', 1, 0, new ObjectFactory(1));
        $this->assertEquals($obj->getType(), InternalType\AbstractTypeObject::TYPE_STREAM);
    }

    public function testDump()
    {
        $factory = new ObjectFactory(1);

        $obj = new InternalType\StreamObject('some data', 55, 3, $factory);
        $this->assertEquals($obj->dump($factory), "55 3 obj \n<</Length 9 >>\nstream\nsome data\nendstream\nendobj\n");
    }
}
