<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Technorati;

use Zend\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
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
