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
 * @see Zend_Service_Nirvanix
 */
require_once 'Zend/Service/Nirvanix.php';

/**
 * @see Zend_Http_Client_Adapter_Test
 */
require_once 'Zend/Http/Client/Adapter/Test.php';
 
/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Nirvanix_FunctionalTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->httpAdapter = new Zend_Http_Client_Adapter_Test();
        $this->httpClient = new Zend_Http_Client('http://foo', 
                                    array('adapter' => $this->httpAdapter));
        
        $this->auth = array('username' => 'foo', 'password' => 'bar', 'appKey' => 'baz');
        $this->options = array('httpClient' => $this->httpClient);

        // set first nirvanix response to successful login
        $this->httpAdapter->setResponse(
            $this->makeNirvanixResponse(array('ResponseCode' => '0',
                                              'SessionToken' => 'foo'))
        );

        $this->nirvanix = new Zend_Service_Nirvanix($this->auth, $this->options);
    }    
    
    public function makeNirvanixResponse($hash)
    {
        $xml = "<?xml version='1.0'?><Response>";
        foreach ($hash as $k => $v) { $xml .= "<$k>$v</$k>"; }
        $xml .= "</Response>";

        $resp = $this->makeHttpResponseFrom($xml);
        return $resp;
    }
    
    public function makeHttpResponseFrom($data, $status=200, $message='OK') 
    {
        $headers = array("HTTP/1.1 $status $message",
                         "Status: $status",
                         'Content_Type: text/xml; charset=utf-8',
                         'Content-Length: ' . strlen($data)
                         );
        return implode("\r\n", $headers) . "\r\n\r\n$data\r\n\r\n";
    }        
}
