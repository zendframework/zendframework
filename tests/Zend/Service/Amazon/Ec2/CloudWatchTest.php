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
require_once 'Zend/Service/Amazon/Ec2/CloudWatch.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * Zend_Service_Amazon_Ec2_CloudWatch test case.
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
class Zend_Service_Amazon_Ec2_CloudWatchTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_CloudWatch
     */
    private $Zend_Service_Amazon_Ec2_CloudWatch;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->Zend_Service_Amazon_Ec2_CloudWatch = new Zend_Service_Amazon_Ec2_CloudWatch('access_key', 'secret_access_key');
        $adapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Zend_Service_Amazon_Ec2_CloudWatch::setHttpClient($client);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);
        $this->Zend_Service_Amazon_Ec2_CloudWatch = null;

        parent::tearDown();
    }

    /**
     * Tests Zend_Service_Amazon_Ec2_CloudWatch->getMetricStatistics()
     */
    public function testGetMetricStatistics()
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
                    ."<GetMetricStatisticsResponse xmlns=\"http://monitoring.amazonaws.com/doc/2009-05-15/\">\r\n"
                    ."  <GetMetricStatisticsResult>\r\n"
                    ."    <Datapoints>\r\n"
                    ."      <member>\r\n"
                    ."        <Timestamp>2009-06-16T23:57:00Z</Timestamp>\r\n"
                    ."        <Unit>Bytes</Unit>\r\n"
                    ."        <Samples>1.0</Samples>\r\n"
                    ."        <Average>14838.0</Average>\r\n"
                    ."      </member>\r\n"
                    ."      <member>\r\n"
                    ."        <Timestamp>2009-06-17T00:16:00Z</Timestamp>\r\n"
                    ."        <Unit>Bytes</Unit>\r\n"
                    ."        <Samples>1.0</Samples>\r\n"
                    ."        <Average>18251.0</Average>\r\n"
                    ."      </member>\r\n"
                    ."    </Datapoints>\r\n"
                    ."    <Label>NetworkIn</Label>"
                    ."  </GetMetricStatisticsResult>\r\n"
                    ."</GetMetricStatisticsResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_CloudWatch->getMetricStatistics(array('MeasureName' => 'NetworkIn', 'Statistics' => array('Average')));

        $arrReturn = array(
            'label'         => 'NetworkIn',
            'datapoints'    => array(
                array(
                    'Timestamp'     => '2009-06-16T23:57:00Z',
                    'Unit'          => 'Bytes',
                    'Samples'       => '1.0',
                    'Average'       => '14838.0',
                ),
                array(
                    'Timestamp'     => '2009-06-17T00:16:00Z',
                    'Unit'          => 'Bytes',
                    'Samples'       => '1.0',
                    'Average'       => '18251.0',
                )
            )
        );

        $this->assertSame($arrReturn, $return);

    }

    /**
     * Tests Zend_Service_Amazon_Ec2_CloudWatch->listMetrics()
     */
    public function testListMetrics()
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
                    ."<ListMetricsResponse xmlns=\"http://monitoring.amazonaws.com/doc/2009-05-15/\">\r\n"
                    ."  <ListMetricsResult>\r\n"
                    ."    <Metrics>\r\n"
                    ."      <member>\r\n"
                    ."        <Dimensions>\r\n"
                    ."          <member>\r\n"
                    ."            <Name>InstanceId</Name>\r\n"
                    ."            <Value>i-bec576d7</Value>\r\n"
                    ."          </member>\r\n"
                    ."        </Dimensions>\r\n"
                    ."        <MeasureName>NetworkIn</MeasureName>\r\n"
                    ."        <Namespace>AWS/EC2</Namespace>\r\n"
                    ."      </member>\r\n"
                    ."      <member>\r\n"
                    ."        <Dimensions>\r\n"
                    ."          <member>\r\n"
                    ."            <Name>InstanceId</Name>\r\n"
                    ."            <Value>i-bec576d7</Value>\r\n"
                    ."          </member>\r\n"
                    ."        </Dimensions>\r\n"
                    ."        <MeasureName>CPUUtilization</MeasureName>\r\n"
                    ."        <Namespace>AWS/EC2</Namespace>\r\n"
                    ."      </member>\r\n"
                    ."      <member>\r\n"
                    ."        <Dimensions/>\r\n"
                    ."        <MeasureName>NetworkIn</MeasureName>\r\n"
                    ."        <Namespace>AWS/EC2</Namespace>\r\n"
                    ."      </member>\r\n"
                    ."    </Metrics>\r\n"
                    ."  </ListMetricsResult>\r\n"
                    ."</ListMetricsResponse>\r\n";
        $this->adapter->setResponse($rawHttpResponse);

        $return = $this->Zend_Service_Amazon_Ec2_CloudWatch->listMetrics();

        $arrReturn = array(
            array(
                'MeasureName'   => 'NetworkIn',
                'Namespace'     => 'AWS/EC2',
                'Deminsions'    => array(
                    'name'      => 'InstanceId',
                    'value'     => 'i-bec576d7'
                )
            ),
            array(
                'MeasureName'   => 'CPUUtilization',
                'Namespace'     => 'AWS/EC2',
                'Deminsions'    => array(
                    'name'      => 'InstanceId',
                    'value'     => 'i-bec576d7'
                )
            ),
            array(
                'MeasureName'   => 'NetworkIn',
                'Namespace'     => 'AWS/EC2',
                'Deminsions'    => array()
            )
        );

        $this->assertSame($arrReturn, $return);


    }

}

