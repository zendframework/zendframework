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
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'BaseProxy.php';

/**
 * @see Zend_Http_Client_Adapter_Test
 */
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @category   Zend
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Simpy_OfflineProxy extends Zend_Service_Simpy_BaseProxy
{
    /**
     * Test adapter, stored because Zend_Http_Client provides no accessor
     * method or public property for it
     *
     * @var Zend_Http_Client_Adapter
     */
    protected $_adapter;

    /**
     * Initialize the HTTP client test adapter.
     *
     * @return void
     */
    public function init()
    {
        $this->_adapter = new Zend_Http_Client_Adapter_Test;
        $this->_simpy->getHttpClient()->setAdapter($this->_adapter);
    }

    /**
     * Proxy all method calls to the service consumer object using a test
     * HTTP client adapter and reading responses from local files.
     *
     * @param string $name Name of the method called
     * @param array $args Arguments passed in the method call
     * @return mixed Return value of the called method
     */
    public function __call($name, array $args)
    {
        $file = $this->_getFilePath($name);
        $body = file_get_contents($file);

        $this->_adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Content-Type: text/xml' . "\r\n" .
            "\r\n" .
            $body
        );

        $return = call_user_func_array(
            array($this->_simpy, $name),
            $args
        );

        return $return;
    }
}
