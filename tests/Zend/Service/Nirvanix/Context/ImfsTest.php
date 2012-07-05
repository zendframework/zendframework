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
 * @package    Zend_Service
 * @subpackage Nirvanix
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Nirvanix\Context;

use Zend\Service\Nirvanix\Context\Imfs as ImfsContext;
use ZendTest\Service\Nirvanix\FunctionalTestCase;

/**
 * @see        Zend\Service\Nirvanix\Context\Imfs
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
