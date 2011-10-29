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
 * @package    Zend_Soap
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Soap\Client;

use Zend\Soap\Client as SOAPClient,
    Zend\Soap\Server as SOAPServer;

/**
 * \Zend\Soap\Client\Local
 *
 * Class is intended to be used as local SOAP client which works
 * with a provided Server object.
 *
 * Could be used for development or testing purposes.
 *
 * @uses       \Zend\Soap\Client
 * @uses       \Zend\Soap\Server
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Client
 */
class Local extends SOAPClient
{
    /**
     * Server object
     *
     * @var \Zend\Soap\Server
     */
    protected $_server;

    /**
     * Local client constructor
     *
     * @param \Zend\Soap\Server $server
     * @param string $wsdl
     * @param array $options
     */
    function __construct(SOAPServer $server, $wsdl, $options = null)
    {
        $this->_server = $server;

        // Use Server specified SOAP version as default
        $this->setSoapVersion($server->getSoapVersion());

        parent::__construct($wsdl, $options);
    }

    /**
     * Actual "do request" method.
     *
     * @internal
     * @param \Zend\Soap\Client\Common $client
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $one_way
     * @return mixed
     */
    public function _doRequest(Common $client, $request, $location, $action, $version, $one_way = null)
    {
        // Perform request as is
        ob_start();
        $this->_server->handle($request);
        $response = ob_get_clean();

        return $response;
    }
}
