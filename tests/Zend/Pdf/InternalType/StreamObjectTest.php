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
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
