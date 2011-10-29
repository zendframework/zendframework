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
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mail\Transport;
use Zend\Mail\AbstractProtocol,
    Zend\Mail\AbstractTransport,
    Zend\Mail\Transport\Exception,
    Zend\Mail\Protocol\Smtp as SmtpProtocol,
    Zend\Mail\Protocol,
    Zend\Mime;

/**
 * SMTP connection object
 *
 * Loads an instance of \Zend\Mail\Protocol\Smtp and forwards smtp transactions
 *
 * @uses       \Zend\Loader
 * @uses       \Zend\Mail\Protocol\Smtp
 * @uses       \Zend\Mail\AbstractTransport
 * @uses       \Zend\Mail\Transport\Exception
 * @uses       \Zend\Mime\Mime
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Smtp extends AbstractTransport
{
    /**
     * EOL character string used by transport
     * @var string
     * @access public
     */
    public $EOL = "\n";

    /**
     * Remote smtp hostname or i.p.
     *
     * @var string
     */
    protected $_host;


    /**
     * Port number
     *
     * @var integer|null
     */
    protected $_port;


    /**
     * Local client hostname or i.p.
     *
     * @var string
     */
    protected $_name = 'localhost';


    /**
     * Authentication type OPTIONAL
     *
     * @var string
     */
    protected $_auth;


    /**
     * Config options for authentication
     *
     * @var array
     */
    protected $_config;


    /**
     * Instance of \Zend\Mail\Protocol\Smtp
     *
     * @var \Zend\Mail\Protocol\Smtp
     */
    protected $_connection;


    /**
     * Constructor.
     *
     * @param  string $host OPTIONAL (Default: 127.0.0.1)
     * @param  array|null $config OPTIONAL (Default: null)
     * @return void
     *
     * @todo Someone please make this compatible
     *       with the SendMail transport class.
     */
    public function __construct($host = '127.0.0.1', Array $config = array())
    {
        if ($host) {
            $config['host'] = $host;
        }

        $this->setConfig($config);
    }

    /**
     * Set configuration
     *
     * @param  array $config
     * @return \Zend\Mail\Transport\Smtp
     */
    public function setConfig(array $config)
    {
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }
        if (isset($config['port'])) {
            $this->_port = $config['port'];
        }
        if (isset($config['auth'])) {
            $this->_auth = $config['auth'];
        }
        if (isset($config['host'])) {
            $this->_host = $config['host'];
        }

        $this->_config = $config;

        return $this;
    }

    /**
     * Class destructor to ensure all open connections are closed
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->_connection instanceof SmtpProtocol) {
            try {
                $this->_connection->quit();
            } catch (Protocol\Exception $e) {
                // ignore
            }
            $this->_connection->disconnect();
        }
    }


    /**
     * Sets the connection protocol instance
     *
     * @param \Zend\Mail\Protocol\AbstractProtocol $client
     *
     * @return void
     */
    public function setConnection(AbstractProtocol $connection)
    {
        $this->_connection = $connection;
    }


    /**
     * Gets the connection protocol instance
     *
     * @return \Zend\Mail\Protocol|null
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Send an email via the SMTP connection protocol
     *
     * The connection via the protocol adapter is made just-in-time to allow a
     * developer to add a custom adapter if required before mail is sent.
     *
     * @return void
     * @todo Rename this to sendMail, it's a public method...
     */
    public function _sendMail()
    {
        // If sending multiple messages per session use existing adapter
        if (!($this->_connection instanceof SmtpProtocol)) {
            // Check if authentication is required and determine required class
            $connectionClass = '\Zend\Mail\Protocol\Smtp';
            if ($this->_auth) {
                $connectionClass .= '\Auth\\' . ucwords($this->_auth);
            }
            $this->setConnection(new $connectionClass($this->_host, $this->_port, $this->_config));
            $this->_connection->connect();
            $this->_connection->helo($this->_name);
        } else {
            // Reset connection to ensure reliable transaction
            $this->_connection->rset();
        }

        // Set sender email address
        $this->_connection->mail($this->_mail->getFrom());

        // Set recipient forward paths
        foreach ($this->_mail->getRecipients() as $recipient) {
            $this->_connection->rcpt($recipient);
        }

        // Issue DATA command to client
        $this->_connection->data($this->header . Mime\Mime::LINEEND . $this->body);
    }

    /**
     * Format and fix headers
     *
     * Some SMTP servers do not strip BCC headers. Most clients do it themselves as do we.
     *
     * @access  protected
     * @param   array $headers
     * @return  void
     * @throws  \Zend\Transport\Exception
     */
    protected function _prepareHeaders($headers)
    {
        if (!$this->_mail) {
            throw new Exception\RuntimeException('_prepareHeaders requires a registered \Zend\Mail\Mail object');
        }

        unset($headers['Bcc']);

        // Prepare headers
        parent::_prepareHeaders($headers);
    }
}
