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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
