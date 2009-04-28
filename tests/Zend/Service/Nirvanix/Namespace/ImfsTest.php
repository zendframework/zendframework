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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * @see Zend_Service_Nirvanix_Namespace_Imfs
 */
require_once 'Zend/Service/Nirvanix/Namespace/Imfs.php';

/**
 * @see Zend_Service_Nirvanix_FunctionalTestCase
 */
require_once 'Zend/Service/Nirvanix/FunctionalTestCase.php';

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Nirvanix_Namespace_ImfsTest extends Zend_Service_Nirvanix_FunctionalTestCase
{
    public function testInheritsFromNirvanixBase()
    {
        $imfs = new Zend_Service_Nirvanix_Namespace_Imfs();
        $this->assertType('Zend_Service_Nirvanix_Namespace_Base', $imfs);
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

        $actual = $imfs->getContents('/foo.txt');
        $expected = $this->httpClient->getLastResponse()->getBody();
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

}






