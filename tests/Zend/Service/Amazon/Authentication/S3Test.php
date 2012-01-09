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
 * @package    Zend_Service_Amazon_Authentication
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 11973 2008-10-15 16:00:56Z matthew $
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Amazon\Authentication;

use Zend\Service\Amazon\Authentication,
    Zend\Service\Amazon\Authentication\Exception;

/**
 * S3 authentication test case
 *
 * @category   Zend
 * @package    Zend_Service_Amazon_Authentication
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class S3Test extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var Zend\Service\Amazon\Authentication\S3
     */
    private $_amazon;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // TODO Auto-generated Zend_Service_Amazon_Authentication_S3Test::setUp()
        

        $this->_amazon = new Authentication\S3('0PN5J17HBGZHT7JJ3X82', 'uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o', '2006-03-01');
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated Zend_Service_Amazon_Authentication_S3Test::tearDown()

        $this->_amazon = null;
        
        parent::tearDown();
    }

    
    public function testGetGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 19:36:42 +0000";
        
        $ret = $this->_amazon->generateSignature('GET', 'http://s3.amazonaws.com/johnsmith/photos/puppy.jpg', $headers);

        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:soqB4L9flQ6AHG4d5FVnKj26D2s=', $headers['Authorization']);
        $this->assertEquals($ret, "GET


Tue, 27 Mar 2007 19:36:42 +0000
//johnsmith/photos/puppy.jpg");
    }
    
    public function testPutGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 21:15:45 +0000";
        $headers['Content-Type'] = "image/jpeg";
        $headers['Content-Length'] = 94328;
        
        $ret = $this->_amazon->generateSignature('PUT', 'http://s3.amazonaws.com/johnsmith/photos/puppy.jpg', $headers);

        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:88cf7BdpjrBlCsIiWWLn8wLpWzI=', $headers['Authorization']);
        $this->assertEquals($ret, "PUT

image/jpeg
Tue, 27 Mar 2007 21:15:45 +0000
//johnsmith/photos/puppy.jpg");
    }
    
    public function testListGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 19:42:41 +0000";
        
        $ret = $this->_amazon->generateSignature('GET', 'http://s3.amazonaws.com/johnsmith/?prefix=photos&max-keys=50&marker=puppy', $headers);

        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:pm3Adv2BIFCCJiUSikcLcGYFtiA=', $headers['Authorization']);
        $this->assertEquals($ret, "GET


Tue, 27 Mar 2007 19:42:41 +0000
//johnsmith/");
    }
    
    public function testFetchGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 19:44:46 +0000";
        
        $ret = $this->_amazon->generateSignature('GET', 'http://s3.amazonaws.com/johnsmith/?acl', $headers);

        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:TCNlZPuxY41veihZbxjnjw8P93w=', $headers['Authorization']);
        $this->assertEquals($ret, "GET


Tue, 27 Mar 2007 19:44:46 +0000
//johnsmith/?acl");
    }
    
    public function testDeleteGeneratesCorrectSignature()
    {
        
        $headers = array();
        $headers['x-amz-date'] = "Tue, 27 Mar 2007 21:20:26 +0000";
        
        $ret = $this->_amazon->generateSignature('DELETE', 'http://s3.amazonaws.com/johnsmith/photos/puppy.jpg', $headers);
        
        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:O9AsSXUIowhjTiJC5escAqjsAyk=', $headers['Authorization']);
        $this->assertEquals($ret, "DELETE



x-amz-date:Tue, 27 Mar 2007 21:20:26 +0000
//johnsmith/photos/puppy.jpg");
    }
    
    public function testUploadGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 21:06:08 +0000";
        $headers['x-amz-acl'] = "public-read";
        $headers['content-type'] = "application/x-download";
        $headers['Content-MD5'] = "4gJE4saaMU4BqNR0kLY+lw==";
        $headers['X-Amz-Meta-ReviewedBy'][] = "joe@johnsmith.net";
        $headers['X-Amz-Meta-ReviewedBy'][] = "jane@johnsmith.net";
        $headers['X-Amz-Meta-FileChecksum'] = "0x02661779";
        $headers['X-Amz-Meta-ChecksumAlgorithm'] = "crc32";
        $headers['Content-Disposition'] = "attachment; filename=database.dat";
        $headers['Content-Encoding'] = "gzip";
        $headers['Content-Length'] = "5913339";
        
        
        $ret = $this->_amazon->generateSignature('PUT', 'http://s3.amazonaws.com/static.johnsmith.net/db-backup.dat.gz', $headers);
        
        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:IQh2zwCpX2xqRgP2rbIkXL/GVbA=', $headers['Authorization']);
        $this->assertEquals($ret, "PUT
4gJE4saaMU4BqNR0kLY+lw==
application/x-download
Tue, 27 Mar 2007 21:06:08 +0000
x-amz-acl:public-read
x-amz-meta-checksumalgorithm:crc32
x-amz-meta-filechecksum:0x02661779
x-amz-meta-reviewedby:joe@johnsmith.net,jane@johnsmith.net
//static.johnsmith.net/db-backup.dat.gz");
    }

}

