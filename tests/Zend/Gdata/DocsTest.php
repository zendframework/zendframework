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
 * @package    Zend_Gdata_Docs
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Docs.php';
require_once 'Zend/Gdata/HttpClient.php';
require_once 'Zend/Gdata/TestUtility/MockHttpClient.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Docs
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Docs
 */
class Zend_Gdata_DocsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->adapter = new Test_Zend_Gdata_MockHttpClient();
        $this->client = new Zend_Gdata_HttpClient();
        $this->client->setAdapter($this->adapter);
        $this->gdata = new Zend_Gdata_Docs($this->client);
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
