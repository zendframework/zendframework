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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Amazon\Ec2;
use Zend\Service\Amazon\Ec2;

/**
 * Zend\Service\Amazon\Ec2\CloudWatch test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class CloudWatchTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend\Service\Amazon\Ec2\CloudWatch
     */
    private $cloudWatchInstance;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->cloudWatchInstance = new Ec2\CloudWatch('access_key', 'secret_access_key');
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $client = new \Zend\Http\Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Ec2\CloudWatch::setDefaultHTTPClient($client);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->adapter);
        $this->cloudWatchInstance = null;
    }

    /**
     * Tests Zend\Service\Amazon\Ec2\CloudWatch->getMetricStatistics()
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

        $return = $this->cloudWatchInstance->getMetricStatistics(array('MeasureName' => 'NetworkIn', 'Statistics' => array('Average')));

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
     * Tests Zend\Service\Amazon\Ec2\CloudWatch->listMetrics()
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

        $return = $this->cloudWatchInstance->listMetrics();

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
    
    public function testZF8149()
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
                    ."        <Timestamp>2009-11-19T21:52:00Z</Timestamp>\r\n"
                    ."        <Unit>Percent</Unit>\r\n"
                    ."        <Samples>1.0</Samples>\r\n"
                    ."        <Average>0.09</Average>\r\n"
                    ."      </member>\r\n"
                    ."      <member>\r\n"
                    ."        <Timestamp>2009-11-19T21:55:00Z</Timestamp>\r\n"
                    ."        <Unit>Percent</Unit>\r\n"
                    ."        <Samples>1.0</Samples>\r\n"
                    ."        <Average>0.18</Average>\r\n"
                    ."      </member>\r\n"
                    ."      <member>\r\n"
                    ."        <Timestamp>2009-11-19T21:54:00Z</Timestamp>\r\n"
                    ."        <Unit>Percent</Unit>\r\n"
                    ."        <Samples>1.0</Samples>\r\n"
                    ."        <Average>0.09</Average>\r\n"
                    ."      </member>\r\n"
                    ."      <member>\r\n"
                    ."        <Timestamp>2009-11-19T21:51:00Z</Timestamp>\r\n"
                    ."        <Unit>Percent</Unit>\r\n"
                    ."        <Samples>1.0</Samples>\r\n"
                    ."        <Average>0.18</Average>\r\n"
                    ."      </member>\r\n"
                    ."      <member>\r\n"
                    ."        <Timestamp>2009-11-19T21:53:00Z</Timestamp>\r\n"
                    ."        <Unit>Percent</Unit>\r\n"
                    ."        <Samples>1.0</Samples>\r\n"
                    ."        <Average>0.09</Average>\r\n"
                    ."      </member>\r\n"
                    ."    </Datapoints>\r\n"
                    ."    <Label>CPUUtilization</Label>\r\n"
                    ."  </GetMetricStatisticsResult>\r\n"
                    ."  <ResponseMetadata>\r\n"
                    ."    <RequestId>6fb864fd-d557-11de-ac37-475775222f21</RequestId>\r\n"
                    ."  </ResponseMetadata>\r\n"
                    ."</GetMetricStatisticsResponse>";
        $this->adapter->setResponse($rawHttpResponse);
        
        $return = $this->cloudWatchInstance->getMetricStatistics(
            array(
                'MeasureName' => 'CPUUtilization',
                'Statistics' =>  array('Average'),
                'Dimensions'=>   array('InstanceId'=>'i-93ba31fa'),
                'StartTime'=>    '2009-11-19T21:51:57+00:00',
                'EndTime'=>      '2009-11-19T21:56:57+00:00'
           )
        );
        
        $arrReturn = array (
          'label' => 'CPUUtilization',
          'datapoints' => 
          array (
            0 => 
            array (
              'Timestamp' => '2009-11-19T21:52:00Z',
              'Unit' => 'Percent',
              'Samples' => '1.0',
              'Average' => '0.09',
            ),
            1 => 
            array (
              'Timestamp' => '2009-11-19T21:55:00Z',
              'Unit' => 'Percent',
              'Samples' => '1.0',
              'Average' => '0.18',
            ),
            2 => 
            array (
              'Timestamp' => '2009-11-19T21:54:00Z',
              'Unit' => 'Percent',
              'Samples' => '1.0',
              'Average' => '0.09',
            ),
            3 => 
            array (
              'Timestamp' => '2009-11-19T21:51:00Z',
              'Unit' => 'Percent',
              'Samples' => '1.0',
              'Average' => '0.18',
            ),
            4 => 
            array (
              'Timestamp' => '2009-11-19T21:53:00Z',
              'Unit' => 'Percent',
              'Samples' => '1.0',
              'Average' => '0.09',
            ),
          ),
        );
        
        $this->assertSame($arrReturn, $return);
    }

}

