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
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\App;
use Zend\GData\Spreadsheets;

/**
 * @category   Zend
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_App
 */
class HttpExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend\GData online tests are not enabled');
        }
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->sprKey = constant('TESTS_ZEND_GDATA_SPREADSHEETS_SPREADSHEETKEY');
        $this->wksId = constant('TESTS_ZEND_GDATA_SPREADSHEETS_WORKSHEETID');
        $service = Spreadsheets::AUTH_SERVICE_NAME;
        $client = \Zend\GData\ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Spreadsheets($client);
    }

    public function testGetRawResponseBody()
    {
        try {
            $rowData = array();
            $entry = $this->gdata->insertRow($rowData, $this->sprKey);
            $this->fail('Expecting Zend\GData\App\HttpException');
        } catch (\Zend\GData\App\HttpException $hExc) {
            $this->assertThat($hExc,
                $this->isInstanceOf('Zend\GData\App\HttpException'),
                'Expecting Zend\GData\App\HttpException, got '
                . get_class($hExc));

            $message = $hExc->getMessage();
            $this->assertEquals($message, 'Expected response code 200, got 400');
            $body = $hExc->getRawResponseBody();
            $this->assertNotNull($body);
            $this->assertNotEquals(stripos($body,
                'Blank rows cannot be written; use delete instead.'), false);
        }
    }
}
