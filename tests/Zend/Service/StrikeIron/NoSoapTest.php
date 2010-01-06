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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Service_StrikeIron_BaseTest
 */
require_once 'Zend/Service/StrikeIron/BaseTest.php';


/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_StrikeIron
 */
class Zend_Service_StrikeIron_NoSoapTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->soapClient = new Zend_Service_StrikeIron_BaseTest_MockSoapClient;
        if (extension_loaded('soap')) {
            $this->markTestSkipped('SOAP extension is loaded, so cannot test for exception');
        }
    }

    public function testNoSoapException()
    {
        try {
            $base = new Zend_Service_StrikeIron_Base(array('client'   => $this->soapClient,
                                                             'username' => 'user',
                                                             'password' => 'pass'));
            $this->fail('Expecting exception of type Zend_Service_StrikeIron_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Service_StrikeIron_Exception', $e,
                'Expecting exception of type Zend_Service_StrikeIron_Exception, got '.get_class($e));
            $this->assertEquals('SOAP extension is not enabled', $e->getMessage());
        }
    }

}
