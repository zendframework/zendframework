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
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\StrikeIron;
use Zend\Service\StrikeIron;

/**
 * Test helper
 */

/**
 * @see StrikeIron\BaseTest
 */


/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
