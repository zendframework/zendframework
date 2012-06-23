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

namespace ZendTest\Service\Technorati;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\KeyInfoResult
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
class KeyInfoResultTest extends TestCase
{
    const TEST_API_KEY = 'avalidapikey';

    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestKeyInfoResult.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\KeyInfoResult', array($this->dom, self::TEST_API_KEY));
    }

    public function testKeyInfoResult()
    {
        $object = new Technorati\KeyInfoResult($this->dom, self::TEST_API_KEY);

        $this->assertInternalType('string', $object->getApiKey());
        $this->assertEquals(self::TEST_API_KEY, $object->getApiKey());
        $this->assertInternalType('integer', $object->getApiQueries());
        $this->assertEquals(27, $object->getApiQueries());
        $this->assertInternalType('integer', $object->getMaxQueries());
        $this->assertEquals(1500, $object->getMaxQueries());
    }

    public function testApiKeyIsNullByDefault()
    {
        $object = new Technorati\KeyInfoResult($this->dom);
        $this->assertEquals(null, $object->getApiKey());
    }

    public function testSetGet()
    {
        $object = new Technorati\KeyInfoResult($this->dom, self::TEST_API_KEY);

        $set = 'anewapikey';
        $get = $object->setApiKey($set)->getApiKey();
        $this->assertInternalType('string', $get);
        $this->assertEquals($set, $get);
    }
}
