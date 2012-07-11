<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\App;

/**
 * Overrides the traditional socket-based adapter class for Zend_Http_Client to
 * enable logging of requests.  All requests are logged to a location specified
 * in the config as $config['logfile'].  Requests and responses are logged after
 * they are sent and received/processed, thus an error could prevent logging.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
class LoggingHttpClientAdapterSocket extends \Zend\Http\Client\Adapter\Socket
{

    /**
     * The file handle for writing logs
     *
     * @var resource|null
     */
    protected $log_handle = null;

    /**
     * Log the given message to the log file.  The log file is configured
     * as the config param 'logfile'.  This method opens the file for
     * writing if necessary.
     *
     * @param string $message The message to log
     */
    protected function log($message)
    {
        if ($this->log_handle == null) {
            $this->log_handle = fopen($this->config['logfile'], 'a');
        }
        fwrite($this->log_handle, $message);
    }

    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param int     $port
     * @param boolean $secure
     * @param int     $timeout
     */
    public function connect($host, $port = 80, $secure = false)
    {
        $this->log("Connecting to: ${host}:${port}");
        return parent::connect($host, $port, $secure);
    }

    /**
     * Send request to the remote server
     *
     * @param string        $method
     * @param \Zend\Uri\Uri $uri
     * @param string        $http_ver
     * @param array         $headers
     * @param string        $body
     * @return string Request as string
     */
    public function write($method, $uri, $http_ver = '1.1', $headers = array(), $body = '')
    {
        $request = parent::write($method, $uri, $http_ver, $headers, $body);
        $this->log("\n\n" . $request);
        return $request;
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        $response = parent::read();
        $this->log("${response}\n\n");
        return $response;
    }

    /**
     * Close the connection to the server
     *
     */
    public function close()
    {
        $this->log("Closing socket\n\n");
        parent::close();
    }

}
