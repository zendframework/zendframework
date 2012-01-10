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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Amazon;
use Zend\Service\Amazon;

/**
 * @todo: Rename class to Zend_Service_Amazon_AbstractTest
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 */
class AmamzonAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testNoKeysThrowException()
    {
        try {
            $class = new TestAmamzonAbstract();
            $this->fail('Exception should be thrown when no keys are passed in.');
        } catch(Amazon\Exception $zsae) {}
    }

    public function testConstructorWithKeysDoesNotThrowException()
    {
        try {
            $class = new TestAmamzonAbstract('TestAccessKey', 'TestSecretKey');
        } catch(Amazon\Exception $zsae) {
            $this->fail('Exception should be thrown when no keys are passed in.');
        }
    }

    public function testSetStaticKeys()
    {
        TestAmamzonAbstract::setKeys('TestAccessKey', 'TestSecretKey');
        $class = new TestAmamzonAbstract();

        $this->assertEquals('TestAccessKey', $class->returnAccessKey());
        $this->assertEquals('TestSecretKey', $class->returnSecretKey());
    }

    public function testPassKeysIntoConstructor()
    {
        $class = new TestAmamzonAbstract('TestAccessKey', 'TestSecretKey');

        $this->assertEquals('TestAccessKey', $class->returnAccessKey());
        $this->assertEquals('TestSecretKey', $class->returnSecretKey());
    }

    public function testPassedInKeysOverrideStaticSetKeys()
    {
        TestAmamzonAbstract::setKeys('TestStaticAccessKey', 'TestStaticSecretKey');
        $class = new TestAmamzonAbstract('TestAccessKey', 'TestSecretKey');

        $this->assertEquals('TestAccessKey', $class->returnAccessKey());
        $this->assertEquals('TestSecretKey', $class->returnSecretKey());
    }
}

class TestAmamzonAbstract extends Amazon\AbstractAmazon
{
    public function returnAccessKey()
    {
        return $this->_accessKey;
    }

    public function returnSecretKey()
    {
        return $this->_secretKey;
    }

}

