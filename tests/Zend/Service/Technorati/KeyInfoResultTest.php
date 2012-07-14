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
