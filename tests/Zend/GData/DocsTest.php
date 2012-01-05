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
 * @package    Zend_GData_Docs
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData;

require_once 'Zend/GData/TestAsset/MockHttpClient.php';

/**
 * @category   Zend
 * @package    Zend_GData_Docs
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Docs
 */
class DocsTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->adapter = new \ZendTest\GData\TestAsset\MockHttpClient();
        $this->client = new \Zend\GData\HttpClient();
        $this->client->setAdapter($this->adapter);
        $this->gdata = new \Zend\GData\Docs($this->client);
    }

    public function testCreateFolder()
    {
        $this->adapter->setResponse(array('HTTP/1.1 200 OK\r\n\r\n'));
        $this->gdata->createFolder("Test Folder");
        $request = $this->adapter->popRequest();
        
        // Check to make sure the correct URI is in use
        $this->assertEquals(
                "docs.google.com",
                $request->uri->getHost());
        $this->assertEquals(
                "/feeds/documents/private/full",
                $request->uri->getPath());
        
        // Check to make sure that this is a folder
        $this->assertNotEquals( false, strpos($request->body, 
                "<atom:category term=\"http://schemas.google.com/docs/2007#folder\" scheme=\"http://schemas.google.com/g/2005#kind\""));
        
        // Check to make sure the title is set
        $this->assertNotEquals(false, strpos($request->body,
                "<atom:title type=\"text\">Test Folder</atom:title>"));
    }

    public function testCreateSubfolder()
    {
        $subfolderName = "MySubfolder";
        $this->adapter->setResponse(array('HTTP/1.1 200 OK\r\n\r\n'));
        $this->gdata->createFolder("Test Folder", $subfolderName);
        $request = $this->adapter->popRequest();
        
        // Check to make sure the correct URI is in use
        $this->assertEquals(
                "docs.google.com",
                $request->uri->getHost());
        $this->assertEquals(
                "/feeds/folders/private/full/" . $subfolderName,
                $request->uri->getPath());
        
        // Check to make sure that this is a folder
        $this->assertNotEquals( false, strpos($request->body, 
                "<atom:category term=\"http://schemas.google.com/docs/2007#folder\" scheme=\"http://schemas.google.com/g/2005#kind\""));
        
        // Check to make sure the title is set
        $this->assertNotEquals(false, strpos($request->body,
                "<atom:title type=\"text\">Test Folder</atom:title>"));
    }


}
