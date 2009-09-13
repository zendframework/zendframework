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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';
require_once 'Zend/Service/Amazon/Ec2/Securitygroups.php';

/**
 * Zend_Service_Amazon_Ec2_Securitygroups test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class Zend_Service_Amazon_Ec2_SecuritygroupsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_Securitygroups
     */
    private $Zend_Service_Amazon_Ec2_Securitygroups;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->Zend_Service_Amazon_Ec2_Securitygroups = new Zend_Service_Amazon_Ec2_Securitygroups('access_key', 'secret_access_key');

        $adapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Zend_Service_Amazon_Ec2_Securitygroups::setHttpClient($client);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);

        $this->Zend_Service_Amazon_Ec2_Securitygroups = null;

        parent::tearDown();
    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Securitygroups->authorize()
     */
    public function testAuthorizeSinglePort()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<AuthorizeSecurityGroupIngressResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</AuthorizeSecurityGroupIngressResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->authorizeIp('MyGroup', 'tcp', '80', '80', '0.0.0.0/0');
        $this->assertTrue($return);

    }

    public function testAuthorizeRangeOfPorts()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<AuthorizeSecurityGroupIngressResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</AuthorizeSecurityGroupIngressResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->authorizeIp('MyGroup', 'tcp', '6000', '7000', '0.0.0.0/0');
        $this->assertTrue($return);

    }

    public function testAuthorizeSecurityGroupName()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<AuthorizeSecurityGroupIngressResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</AuthorizeSecurityGroupIngressResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->authorizeGroup('MyGroup', 'groupname', '15333848');
        $this->assertTrue($return);

    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Securitygroups->create()
     */
    public function testCreate()
    {

        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<CreateSecurityGroupResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</CreateSecurityGroupResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->create('MyGroup', 'My Security Grup');

        $this->assertTrue($return);

    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Securitygroups->delete()
     */
    public function testDelete()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<DeleteSecurityGroupResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</DeleteSecurityGroupResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->delete('MyGroup');

        $this->assertTrue($return);

    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Securitygroups->describe()
     */
    public function testDescribeMultipleSecruityGroups()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<DescribeSecurityGroupsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <securityGroupInfo>\r\n"
                    . "    <item>\r\n"
                    . "      <ownerId>UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM</ownerId>\r\n"
                    . "      <groupName>WebServers</groupName>\r\n"
                    . "      <groupDescription>Web</groupDescription>\r\n"
                    . "      <ipPermissions>\r\n"
                    . "        <item>\r\n"
                    . "       <ipProtocol>tcp</ipProtocol>\r\n"
                    . "   <fromPort>80</fromPort>\r\n"
                    . "   <toPort>80</toPort>\r\n"
                    . "   <groups/>\r\n"
                    . "   <ipRanges>\r\n"
                    . "     <item>\r\n"
                    . "       <cidrIp>0.0.0.0/0</cidrIp>\r\n"
                    . "     </item>\r\n"
                    . "   </ipRanges>\r\n"
                    . "         </item>\r\n"
                    . "      </ipPermissions>\r\n"
                    . "    </item>\r\n"
                    . "    <item>\r\n"
                    . "      <ownerId>UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM</ownerId>\r\n"
                    . "      <groupName>RangedPortsBySource</groupName>\r\n"
                    . "      <groupDescription>A</groupDescription>\r\n"
                    . "      <ipPermissions>\r\n"
                    . "     <item>\r\n"
                    . "   <ipProtocol>tcp</ipProtocol>\r\n"
                    . "   <fromPort>6000</fromPort>\r\n"
                    . "   <toPort>7000</toPort>\r\n"
                    . "   <groups/>\r\n"
                    . "   <ipRanges>\r\n"
                    . "     <item>\r\n"
                    . "       <cidrIp>0.0.0.0/0</cidrIp>\r\n"
                    . "     </item>\r\n"
                    . "   </ipRanges>\r\n"
                    . " </item>\r\n"
                    . "      </ipPermissions>\r\n"
                    . "    </item>\r\n"
                    . "  </securityGroupInfo>\r\n"
                    . "</DescribeSecurityGroupsResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->describe(array('WebServers','RangedPortsBySource'));

        $this->assertEquals(2, count($return));

        $arrGroups = array(
                array(
                    'ownerId'   => 'UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM',
                    'groupName' => 'WebServers',
                    'groupDescription' => 'Web',
                    'ipPermissions' => array(0 => array(
                        'ipProtocol' => 'tcp',
                        'fromPort'  => '80',
                        'toPort'    => '80',
                        'ipRanges'  => '0.0.0.0/0'
                    ))
                ),
                array(
                    'ownerId'   => 'UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM',
                    'groupName' => 'RangedPortsBySource',
                    'groupDescription' => 'A',
                    'ipPermissions' => array(0 => array(
                        'ipProtocol' => 'tcp',
                        'fromPort'  => '6000',
                        'toPort'    => '7000',
                        'ipRanges'  => '0.0.0.0/0'
                    ))
                )
            );
        foreach($return as $k => $r) {
            $this->assertSame($arrGroups[$k], $r);
        }
    }

    public function testDescribeSingleSecruityGroup()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<DescribeSecurityGroupsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <securityGroupInfo>\r\n"
                    . "    <item>\r\n"
                    . "      <ownerId>UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM</ownerId>\r\n"
                    . "      <groupName>WebServers</groupName>\r\n"
                    . "      <groupDescription>Web</groupDescription>\r\n"
                    . "      <ipPermissions>\r\n"
                    . "        <item>\r\n"
                    . "         <ipProtocol>tcp</ipProtocol>\r\n"
                    . "          <fromPort>80</fromPort>\r\n"
                    . "          <toPort>80</toPort>\r\n"
                    . "          <groups/>\r\n"
                    . "          <ipRanges>\r\n"
                    . "            <item>\r\n"
                    . "              <cidrIp>0.0.0.0/0</cidrIp>\r\n"
                    . "            </item>\r\n"
                    . "          </ipRanges>\r\n"
                    . "         </item>\r\n"
                    . "      </ipPermissions>\r\n"
                    . "    </item>\r\n"
                    . "  </securityGroupInfo>\r\n"
                    . "</DescribeSecurityGroupsResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->describe('WebServers');

        $this->assertEquals(1, count($return));

        $arrGroups = array(
                array(
                    'ownerId'   => 'UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM',
                    'groupName' => 'WebServers',
                    'groupDescription' => 'Web',
                    'ipPermissions' => array(0 => array(
                        'ipProtocol' => 'tcp',
                        'fromPort'  => '80',
                        'toPort'    => '80',
                        'ipRanges'  => '0.0.0.0/0'
                    ))
                )
            );
        foreach($return as $k => $r) {
            $this->assertSame($arrGroups[$k], $r);
        }
    }

    public function testDescribeSingleSecruityGroupWithMultipleIpsSamePort()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<DescribeSecurityGroupsResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <securityGroupInfo>\r\n"
                    . "    <item>\r\n"
                    . "      <ownerId>UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM</ownerId>\r\n"
                    . "      <groupName>WebServers</groupName>\r\n"
                    . "      <groupDescription>Web</groupDescription>\r\n"
                    . "      <ipPermissions>\r\n"
                    . "        <item>\r\n"
                    . "         <ipProtocol>tcp</ipProtocol>\r\n"
                    . "          <fromPort>80</fromPort>\r\n"
                    . "          <toPort>80</toPort>\r\n"
                    . "          <groups/>\r\n"
                    . "          <ipRanges>\r\n"
                    . "            <item>\r\n"
                    . "              <cidrIp>0.0.0.0/0</cidrIp>\r\n"
                    . "            </item>\r\n"
                    . "            <item>\r\n"
                    . "              <cidrIp>1.1.1.1/0</cidrIp>\r\n"
                    . "            </item>\r\n"
                    . "          </ipRanges>\r\n"
                    . "         </item>\r\n"
                    . "      </ipPermissions>\r\n"
                    . "    </item>\r\n"
                    . "  </securityGroupInfo>\r\n"
                    . "</DescribeSecurityGroupsResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->describe('WebServers');

        $this->assertEquals(1, count($return));

        $arrGroups = array(
                array(
                    'ownerId'   => 'UYY3TLBUXIEON5NQVUUX6OMPWBZIQNFM',
                    'groupName' => 'WebServers',
                    'groupDescription' => 'Web',
                    'ipPermissions' => array(0 => array(
                        'ipProtocol' => 'tcp',
                        'fromPort'  => '80',
                        'toPort'    => '80',
                        'ipRanges'  => array(
                        	'0.0.0.0/0',
                            '1.1.1.1/0'
                            )
                    ))
                )
            );
        foreach($return as $k => $r) {
            $this->assertSame($arrGroups[$k], $r);
        }
    }

    /**
     * Tests Zend_Service_Amazon_Ec2_Securitygroups->revoke()
     */
    public function testRevokeSinglePort()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<RevokeSecurityGroupIngressResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</RevokeSecurityGroupIngressResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->revokeIp('MyGroup', 'tcp', '80', '80', '0.0.0.0/0');
        $this->assertTrue($return);

    }

    public function testRevokePortRange()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<RevokeSecurityGroupIngressResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</RevokeSecurityGroupIngressResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->revokeIp('MyGroup', 'tcp', '6000', '7000', '0.0.0.0/0');
        $this->assertTrue($return);

    }


    public function testRevokeSecurityGroupName()
    {
        $rawHttpResponse = "HTTP/1.1 200 OK\r\n"
                    . "Date: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Server: hi\r\n"
                    . "Last-modified: Fri, 24 Oct 2008 17:24:52 GMT\r\n"
                    . "Status: 200 OK\r\n"
                    . "Content-type: application/xml; charset=utf-8\r\n"
                    . "Expires: Tue, 31 Mar 1981 05:00:00 GMT\r\n"
                    . "Connection: close\r\n"
                    . "\r\n"
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"
                    . "<RevokeSecurityGroupIngressResponse xmlns=\"http://ec2.amazonaws.com/doc/2009-04-04/\">\r\n"
                    . "  <return>true</return>\r\n"
                    . "</RevokeSecurityGroupIngressResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_Securitygroups->revokeGroup('MyGroup', 'groupname', '15333848');
        $this->assertTrue($return);

    }

}


