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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mail\Transport;

use Zend\Loader\Pluggable,
    Zend\Mail,
    Zend\Mail\Headers,
    Zend\Mail\Protocol,
    Zend\Mail\Protocol\Exception as ProtocolException;

/**
 * SMTP connection object
 *
 * Loads an instance of \Zend\Mail\Protocol\Smtp and forwards smtp transactions
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Smtp implements TransportInterface, Pluggable
{
    /**
     * @var SmtpOptions
     */
    protected $options;

    /**
     * @var Protocol\Smtp
     */
    protected $connection;
    
    /**
     * @var boolean
     */
    protected $autoDisconnect = true;

    /**
     * @var Protocol\SmtpBroker
     */
    protected $broker;

    /**
     * Constructor.
     *
     * @param  SmtpOptions $options Optional
     */
    public function __construct(SmtpOptions $options = null)
    {
        if (!$options instanceof SmtpOptions) {
            $options = new SmtpOptions();
        }
        $this->setOptions($options);
    }

    /**
     * Set options
     *
     * @param  SmtpOptions $options
     * @return Smtp
     */
    public function setOptions(SmtpOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get options
     * 
     * @return SmtpOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set broker for obtaining SMTP protocol connection
     *
     * @param  Protocol\SmtpBroker $broker
     * @throws Exception\InvalidArgumentException
     * @return Smtp
     */
    public function setBroker($broker)
    {
        if (!$broker instanceof Protocol\SmtpBroker) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an SmtpBroker argument; received "%s"',
                __METHOD__,
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        $this->broker = $broker;
        return $this;
    }
    
    /**
     * Get broker for loading SMTP protocol connection
     *
     * @return Protocol\SmtpBroker
     */
    public function getBroker()
    {
        if (null === $this->broker) {
            $this->setBroker(new Protocol\SmtpBroker());
        }
        return $this->broker;
    }
    /**
     * Set the automatic disconnection when destruct
     * 
     * @param  boolean $flag
     * @return Smtp
     */
    public function setAutoDisconnect($flag) 
    {
        $this->autoDisconnect = (bool) $flag;
        return $this;
    }
    /**
     * Get the automatic disconnection value
     * 
     * @return boolean
     */
    public function getAutoDisconnect()
    {
        return $this->autoDisconnect;
    }
    /**
     * Return an SMTP connection
     * 
     * @param  string $name 
     * @param  array|null $options 
     * @return Protocol\Smtp
     */
    public function plugin($name, array $options = null)
    {
        return $this->getBroker()->load($name, $options);
    }

    /**
     * Class destructor to ensure all open connections are closed
     */
    public function __destruct()
    {
        if ($this->connection instanceof Protocol\Smtp) {
            try {
                $this->connection->quit();
            } catch (ProtocolException\ExceptionInterface $e) {
                // ignore
            }
            if ($this->autoDisconnect) {
                $this->connection->disconnect();
            }    
        }
    }

    /**
     * Sets the connection protocol instance
     *
     * @param Protocol\AbstractProtocol $connection
     */
    public function setConnection(Protocol\AbstractProtocol $connection)
    {
        $this->connection = $connection;
    }


    /**
     * Gets the connection protocol instance
     *
     * @return Protocol\Smtp
     */
    public function getConnection()
    {
        return $this->connection;
    }
    /**
     * Disconnect the connection protocol instance
     * 
     * @return void
     */
    public function disconnect()
    {
        if (!empty($this->connection) && ($this->connection instanceof Protocol\Smtp)) {
            $this->connection->disconnect();
        }
    }
    /**
     * Send an email via the SMTP connection protocol
     *
     * The connection via the protocol adapter is made just-in-time to allow a
     * developer to add a custom adapter if required before mail is sent.
     *
     * @param Mail\Message $message
     */
    public function send(Mail\Message $message)
    {
        // If sending multiple messages per session use existing adapter
        $connection = $this->getConnection();

        if (!($connection instanceof Protocol\Smtp)) {
            // First time connecting
            $connection = $this->lazyLoadConnection();
        } else {
            // Reset connection to ensure reliable transaction
            $connection->rset();
        }

        // Prepare message
        $from       = $this->prepareFromAddress($message);
        $recipients = $this->prepareRecipients($message);
        $headers    = $this->prepareHeaders($message);
        $body       = $this->prepareBody($message);

        // Set sender email address
        $connection->mail($from);

        // Set recipient forward paths
        foreach ($recipients as $recipient) {
            $connection->rcpt($recipient);
        }

        // Issue DATA command to client
        $connection->data($headers . "\r\n" . $body);
    }

    /**
     * Retrieve email address for envelope FROM
     *
     * @param  Mail\Message $message
     * @throws Exception\RuntimeException
     * @return string
     */
    protected function prepareFromAddress(Mail\Message $message)
    {
        $sender = $message->getSender();
        if ($sender instanceof Mail\Address\AddressInterface) {
            return $sender->getEmail();
        }

        $from = $message->from();
        if (!count($from)) {
            throw new Exception\RuntimeException(sprintf(
                '%s transport expects either a Sender or at least one From address in the Message; none provided',
                __CLASS__
            ));
        }

        $from->rewind();
        $sender = $from->current();
        return $sender->getEmail();
    }

    /**
     * Prepare array of email address recipients
     * 
     * @param  Mail\Message $message
     * @return array
     */
    protected function prepareRecipients(Mail\Message $message)
    {
        $recipients = array();
        foreach ($message->to() as $address) {
            $recipients[] = $address->getEmail();
        }
        foreach ($message->cc() as $address) {
            $recipients[] = $address->getEmail();
        }
        foreach ($message->bcc() as $address) {
            $recipients[] = $address->getEmail();
        }
        $recipients = array_unique($recipients);
        return $recipients;
    }

    /**
     * Prepare header string from message
     * 
     * @param  Mail\Message $message
     * @return string
     */
    protected function prepareHeaders(Mail\Message $message)
    {
        $headers = new Headers();
        foreach ($message->headers() as $header) {
            if ('Bcc' == $header->getFieldName()) {
                continue;
            }
            $headers->addHeader($header);
        }
        return $headers->toString();
    }

    /**
     * Prepare body string from message
     * 
     * @param  Mail\Message $message
     * @return string
     */
    protected function prepareBody(Mail\Message $message)
    {
        return $message->getBodyText();
    }

    /**
     * Lazy load the connection, and pass it helo
     * 
     * @return Mail\Protocol\Smtp
     */
    protected function lazyLoadConnection()
    {
        // Check if authentication is required and determine required class
        $options    = $this->getOptions();
        $host       = $options->getHost();
        $port       = $options->getPort();
        $config     = $options->getConnectionConfig();
        $connection = $this->plugin($options->getConnectionClass(), array($host, $port, $config));
        $this->connection = $connection;
        $this->connection->connect();
        $this->connection->helo($options->getName());
        return $this->connection;
    }
}
