<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\StrikeIron;

use Zend\Service\StrikeIron;

/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_StrikeIron
 */
class NoSoapTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->soapClient = new BaseTest\MockSoapClient;
        if (extension_loaded('soap')) {
            $this->markTestSkipped('SOAP extension is loaded, so cannot test for exception');
        }
    }

    public function testNoSoapException()
    {
        try {
            $base = new StrikeIron\Base(array('client'   => $this->soapClient,
                                                             'username' => 'user',
                                                             'password' => 'pass'));
            $this->fail('Expecting exception of type Zend\Service\StrikeIron\Exception\RuntimeException');
        } catch (StrikeIron\Exception\RuntimeException $e) {
            $this->assertInstanceOf('Zend\Service\StrikeIron\Exception\RuntimeException', $e,
                'Expecting exception of type Zend\Service\StrikeIron\Exception\RuntimeException, got '
                . get_class($e));
            $this->assertEquals('SOAP extension is not enabled', $e->getMessage());
        }
    }

}
