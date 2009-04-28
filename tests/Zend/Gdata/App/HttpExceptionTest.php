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
 * @package      Zend_Gdata_App
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Gdata/App.php';
require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/ClientLogin.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_App_HttpExceptionTest extends PHPUnit_Framework_TestCase
{
 
    public function setUp() 
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->sprKey = constant('TESTS_ZEND_GDATA_SPREADSHEETS_SPREADSHEETKEY');
        $this->wksId = constant('TESTS_ZEND_GDATA_SPREADSHEETS_WORKSHEETID');
        $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Zend_Gdata_Spreadsheets($client);
    }

    public function testGetRawResponseBody() 
    {
        try {
            $rowData = array();
            $entry = $this->gdata->insertRow($rowData, $this->sprKey);
            $this->fail('Expecting Zend_Gdata_App_HttpException');
        } catch (Zend_Gdata_App_HttpException $hExc) {
            $this->assertThat($hExc, 
                $this->isInstanceOf('Zend_Gdata_App_HttpException'), 
                'Expecting Zend_Gdata_App_HttpException, got '
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
