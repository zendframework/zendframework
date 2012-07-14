<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData;

use Zend\GData\YouTube;

/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class YouTubeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        // These tests shouldn't be doing anything online, so we can use
        // bogus authentication credentials.
        $this->gdata = new YouTube(null);
        $this->responseText = file_get_contents(
                'Zend/GData/YouTube/_files/FormUploadTokenResponseSample.xml',
                true);
    }

    public function testGetFormUploadTokenResponseHandler()
    {
        $responseArray = YouTube::parseFormUploadTokenResponse($this->responseText);
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
        $httpClient = new \Zend\Http\Client();
        $yt = new YouTube($httpClient, $applicationId, $clientId, $developerKey);

        $this->assertTrue($yt instanceOf YouTube);
        $client = $yt->getHttpClient();

        $this->assertEquals($client->getHeader('User-Agent'),
                            $applicationId . ' Zend_Framework_Gdata/' . \Zend\Version::VERSION);
        $this->assertEquals($client->getHeader('X-GData-Key'), 'key='. $developerKey);
        $this->assertEquals($client->getHeader('X-GData-Client'), $clientId);
    }
}
