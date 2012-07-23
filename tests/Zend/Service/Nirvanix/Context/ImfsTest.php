<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Nirvanix\Context;

use Zend\Service\Nirvanix\Context\Imfs as ImfsContext;
use ZendTest\Service\Nirvanix\FunctionalTestCase;

/**
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Nirvanix
 */
class ImfsTest extends FunctionalTestCase
{
    public function testInheritsFromNirvanixBase()
    {
        $imfs = new ImfsContext();
        $this->assertInstanceOf('Zend\Service\Nirvanix\Context\Base', $imfs);
    }

    // putContents()

    public function testPutContents()
    {
        $imfs = $this->nirvanix->getService('IMFS');

        // response for call to GetStorageNode
        $this->httpAdapter->addResponse(
           $this->makeNirvanixResponse(
                array('ResponseCode'   => '0',
                      'GetStorageNode' => '<UploadHost>node1.nirvanix.com</UploadHost>
                                           <UploadToken>bar</UploadToken>'))
        );

        $imfs->putContents('/foo', 'contents for foo');
    }

    // getContents()

    public function testGetContents()
    {
        $imfs = $this->nirvanix->getService('IMFS');

        // response for call to GetOptimalUrlss
        $this->httpAdapter->addResponse(
           $this->makeNirvanixResponse(
                array('ResponseCode' => '0',
                      'Download' => '<DownloadURL>http://get-it-here</DownloadURL>'))
        );

        // response for file download
        $this->httpAdapter->addResponse(
            $this->makeHttpResponseFrom('contents for foo')
        );

        $actual   = $imfs->getContents('/foo.txt');
        $expected = $this->httpClient->getResponse()->getBody();
        $this->assertEquals($expected, $actual);
    }

    // unlink()

    public function testUnlink()
    {
        $imfs = $this->nirvanix->getService('IMFS');

        // response for call to DeleteFiles
        $this->httpAdapter->addResponse(
            $this->makeNirvanixResponse(array('ResponseCode' => '0'))
        );

        $imfs->unlink('foo');
    }

    /**
     * @issue ZF-6860
     */
    public function testDestinationPathFormatSentToServiceAsParameterUsesUnixConvention()
    {
        $imfs = $this->nirvanix->getService('IMFS');
        $this->httpAdapter->addResponse(
           $this->makeNirvanixResponse(
                array('ResponseCode'   => '0',
                      'GetStorageNode' => '<UploadHost>node1.nirvanix.com</UploadHost>
                                           <UploadToken>bar</UploadToken>'))
        );
        // little unix cheat to force a backslash into the IFS path
        $imfs->putContents('.\foo/bar', 'contents for foo');
        $this->assertContains('./foo', $imfs->getHttpClient()->getLastRawRequest());
    }

}
