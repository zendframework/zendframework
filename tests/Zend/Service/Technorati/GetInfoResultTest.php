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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Technorati;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\GetInfoResult
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class GetInfoResultTest extends TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestGetInfoResult.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\GetInfoResult', array($this->dom));
    }

    public function testGetInfoResult()
    {
        $object = new Technorati\GetInfoResult($this->dom);

        // check author
        $author = $object->getAuthor();
        $this->assertInstanceOf('Zend\Service\Technorati\Author', $author);
        $this->assertEquals('weppos', $author->getUsername());

        // check weblogs
        $weblogs = $object->getWeblogs();
        $this->assertInternalType('array', $weblogs);
        $this->assertEquals(2, count($weblogs));
        $this->assertInstanceOf('Zend\Service\Technorati\Weblog', $weblogs[0]);
    }
}
