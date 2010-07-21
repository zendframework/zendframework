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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        try {
            $boolObj = new InternalType\BooleanObject('some input');
        } catch (\Zend\Pdf\Exception $e) {
            $this->assertContains('must be boolean', $e->getMessage());
            return;
        }
        $this->fail('Expected \Zend\Pdf\Exception to be thrown');
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
