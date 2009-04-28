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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'TestHelper.php';
require_once 'Zend/Gdata/YouTube.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTubeTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        // These tests shouldn't be doing anything online, so we can use
        // bogus authentication credentials.
        $this->gdata = new Zend_Gdata_YouTube(null);
        $this->responseText = file_get_contents(
                'Zend/Gdata/YouTube/_files/FormUploadTokenResponseSample.xml',
                true);
    }

    public function testGetFormUploadTokenResponseHandler()
    {
        $responseArray = Zend_Gdata_YouTube::parseFormUploadTokenResponse($this->responseText);
        $this->assertEquals('http://uploads.gdata.youtube.com/action/FormDataUpload/AIwbF1_JjEQ9cGTjEAd5FKwV42SeNWJexmc5y7XR-eFj24uqbqU6NRcxKJW_4R-sYISLxQ', 
                            $responseArray['url']);
        $this->assertEquals('AIwbFAQ21fImpR2iYPaFnfuCvfbCB3qBxl5qXiZlpH3lfkungiSPoyw1iOM1gFB6Nx-wmY-kjprNT3qtdp7LJCLfngn11Ne_X9Jd44Vz8AzygtEtaDGyib5tnri0O0-V5pwcAPCHIJurOMsOpA2zInW8V8qHk2S2LheXfTXVbqc0Li9iCBpsoBGbykYU0moNoyGAaKRbSBD0oPnCv6v9Rll5Zjvivi2hQt-Br2JDb9wVeLv3qyAFaeyN6X6k32RyaAHs_n8d8d_oSriQmvS8g1HxSCS4dnoGL7tafQ4SBqnrQEb-hxFeu1ZrAwCLv',
                            $responseArray['token']);
    }

    public function testSetClientIDAndDeveloperKeyHeader() 
    {
        $applicationId = 'MyTestCompany-MyTestApp-0.1';
        $clientId = 'MyClientId';
        $developerKey = 'MyDeveloperKey';
        $httpClient = new Zend_Http_Client();
        $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);

        $this->assertTrue($yt instanceOf Zend_Gdata_YouTube);
        $client = $yt->getHttpClient();

        $this->assertEquals($client->getHeader('X-Gdata-Key'), 'key='. $developerKey);
        $this->assertEquals($client->getHeader('X-Gdata-Client'), $clientId);
    }
}
