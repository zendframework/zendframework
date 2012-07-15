<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace Zend\Mail\Protocol;

use Zend\Validator;

/**
 * Provides low-level methods for concrete adapters to communicate with a remote mail server and track requests and responses.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Protocol
 * @todo Implement proxy settings
 */
abstract class AbstractProtocol
{
    /**
     * Mail default EOL string
     */
    const EOL = "\r\n";


    /**
     * Default timeout in seconds for initiating session
     */
    const TIMEOUT_CONNECTION = 30;

    /**
     * Maximum of the transaction log
     * @var integer
     */
    protected $_maximumLog = 64;


    /**
     * Hostname or IP address of remote server
     * @var string
     */
    protected $_host;


    /**
     * Port number of connection
     * @var integer
     */
    protected $_port;


    /**
     * Instance of Zend\Validator\ValidatorChain to check hostnames
     * @var \Zend\Validator\ValidatorChain
     */
    protected $_validHost;


    /**
     * Socket connection resource
     * @var resource
     */
    protected $_socket;


    /**
     * Last request sent to server
     * @var string
     */
    protected $_request;


    /**
     * Array of server responses to last request
     * @var array
     */
    protected $_response;


    /**
     * Log of mail requests and server responses for a session
     * @var array
     */
    private $_log = array();


    /**
     * Constructor.
     *
     * @param  string  $host OPTIONAL Hostname of remote connection (default: 127.0.0.1)
     * @param  integer $port OPTIONAL Port number (default: null)
     * @throws Exception\RuntimeException
     */
    public function __construct($host = '127.0.0.1', $port = null)
    {
        $this->_validHost = new Validator\ValidatorChain();
        $this->_validHost->addValidator(new Validator\Hostname(Validator\Hostname::ALLOW_ALL));

        if (!$this->_validHost->isValid($host)) {
            throw new Exception\RuntimeException(implode(', ', $this->_validHost->getMessages()));
        }

        $this->_host = $host;
        $this->_port = $port;
    }


    /**
     * Class destructor to cleanup open resources
     *
     */
    public function __destruct()
    {
        $this->_disconnect();
    }

    /**
     * Set the maximum log size
     *
     * @param integer $maximumLog Maximum log size
     */
    public function setMaximumLog($maximumLog)
    {
        $this->_maximumLog = (int) $maximumLog;
    }


    /**
     * Get the maximum log size
     *
     * @return int the maximum log size
     */
    public function getMaximumLog()
    {
        return $this->_maximumLog;
    }


    /**
     * Create a connection to the remote host
     *
     * Concrete adapters for this class will implement their own unique connect scripts, using the _connect() method to create the socket resource.
     */
    abstract public function connect();


    /**
     * Retrieve the last client request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->_request;
    }


    /**
     * Retrieve the last server response
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->_response;
    }


    /**
     * Retrieve the transaction log
     *
     * @return string
     */
    public function getLog()
    {
        return implode('', $this->_log);
    }


    /**
     * Reset the transaction log
     *
     */
    public function resetLog()
    {
        $this->_log = array();
    }

    /**
     * Add the transaction log
     *
     * @param  string $value new transaction
     */
    protected function _addLog($value)
    {
        if ($this->_maximumLog >= 0 && count($this->_log) >= $this->_maximumLog) {
            array_shift($this->_log);
        }

        $this->_log[] = $value;
    }

    /**
     * Connect to the server using the supplied transport and target
     *
     * An example $remote string may be 'tcp://mail.example.com:25' or 'ssh://hostname.com:2222'
     *
     * @param  string $remote Remote
     * @throws Exception\RuntimeException
     * @return boolean
     */
    protected function _connect($remote)
    {
        $errorNum = 0;
        $errorStr = '';

        // open connection
        $this->_socket = @stream_socket_client($remote, $errorNum, $errorStr, self::TIMEOUT_CONNECTION);

        if ($this->_socket === false) {
            if ($errorNum == 0) {
                $errorStr = 'Could not open socket';
            }
            throw new Exception\RuntimeException($errorStr);
        }

        if (($result = stream_set_timeout($this->_socket, self::TIMEOUT_CONNECTION)) === false) {
            throw new Exception\RuntimeException('Could not set stream timeout');
        }

        return $result;
    }


    /**
     * Disconnect from remote host and free resource
     *
     */
    protected function _disconnect()
    {
        if (is_resource($this->_socket)) {
            fclose($this->_socket);
        }
    }


    /**
     * Send the given request followed by a LINEEND to the server.
     *
     * @param  string $request
     * @throws Exception\RuntimeException
     * @return integer|boolean Number of bytes written to remote host
     */
    protected function _send($request)
    {
        if (!is_resource($this->_socket)) {
            throw new Exception\RuntimeException('No connection has been established to ' . $this->_host);
        }

        $this->_request = $request;

        $result = fwrite($this->_socket, $request . self::EOL);

        // Save request to internal log
        $this->_addLog($request . self::EOL);

        if ($result === false) {
            throw new Exception\RuntimeException('Could not send request to ' . $this->_host);
        }

        return $result;
    }


    /**
     * Get a line from the stream.
     *
     * @param  integer $timeout Per-request timeout value if applicable
     * @throws Exception\RuntimeException
     * @return string
     */
    protected function _receive($timeout = null)
    {
        if (!is_resource($this->_socket)) {
            throw new Exception\RuntimeException('No connection has been established to ' . $this->_host);
        }

        // Adapters may wish to supply per-commend timeouts according to appropriate RFC
        if ($timeout !== null) {
           stream_set_timeout($this->_socket, $timeout);
        }

        // Retrieve response
        $response = fgets($this->_socket, 1024);

        // Save request to internal log
        $this->_addLog($response);

        // Check meta data to ensure connection is still valid
        $info = stream_get_meta_data($this->_socket);

        if (!empty($info['timed_out'])) {
            throw new Exception\RuntimeException($this->_host . ' has timed out');
        }

        if ($response === false) {
            throw new Exception\RuntimeException('Could not read from ' . $this->_host);
        }

        return $response;
    }


    /**
     * Parse server response for successful codes
     *
     * Read the response from the stream and check for expected return code.
     * Throws a Zend_Mail_Protocol_Exception if an unexpected code is returned.
     *
     * @param  string|array $code One or more codes that indicate a successful response
     * @param  integer $timeout Per-request timeout value if applicable
     * @throws Exception\RuntimeException
     * @return string Last line of response string
     */
    protected function _expect($code, $timeout = null)
    {
        $this->_response = array();
        $cmd  = '';
        $more = '';
        $msg  = '';
        $errMsg = '';

        if (!is_array($code)) {
            $code = array($code);
        }

        do {
            $this->_response[] = $result = $this->_receive($timeout);
            list($cmd, $more, $msg) = preg_split('/([\s-]+)/', $result, 2, PREG_SPLIT_DELIM_CAPTURE);

            if ($errMsg !== '') {
                $errMsg .= ' ' . $msg;
            } elseif ($cmd === null || !in_array($cmd, $code)) {
                $errMsg =  $msg;
            }

        } while (strpos($more, '-') === 0); // The '-' message prefix indicates an information string instead of a response string.

        if ($errMsg !== '') {
            throw new Exception\RuntimeException($errMsg);
        }

        return $msg;
    }
}
