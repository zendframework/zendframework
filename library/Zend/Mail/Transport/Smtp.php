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

/**
 * @namespace
 */
namespace Zend\Mail\Transport;

use Zend\Loader\Pluggable,
    Zend\Mail\AddressDescription,
    Zend\Mail\Headers,
    Zend\Mail\Message,
    Zend\Mail\Transport,
    Zend\Mail\Protocol,
    Zend\Mail\Protocol\AbstractProtocol,
    Zend\Mail\Protocol\Smtp as SmtpProtocol,
    Zend\Mail\Protocol\SmtpBroker;

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
class Smtp implements Transport, Pluggable
{
    /**
     * @var SmtpOptions
     */
    protected $options;

    /**
     * @var SmtpProtocol
     */
    protected $connection;

    /**
     * @var SmtpBroker
     */
    protected $broker;

    /**
     * Constructor.
     *
     * @param  null|SmtpOptions $options
     * @return void
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
     * @param  SmtpBroker $value
     * @return $this
     */
    public function setBroker($broker)
    {
        if (!$broker instanceof SmtpBroker) {
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
     * @return SmtpBroker
     */
    public function getBroker()
    {
        if (null === $this->broker) {
            $this->setBroker(new SmtpBroker());
        }
        return $this->broker;
    }

    /**
     * Return an SMTP connection
     * 
     * @param  string $name 
     * @param  array|null $options 
     * @return \Zend\Mail\Protocol\Smtp
     */
    public function plugin($name, array $options = null)
    {
        return $this->getBroker()->load($name, $options);
    }

    /**
     * Class destructor to ensure all open connections are closed
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->connection instanceof SmtpProtocol) {
            try {
                $this->connection->quit();
            } catch (Protocol\Exception $e) {
                // ignore
            }
            $this->connection->disconnect();
        }
    }


    /**
     * Sets the connection protocol instance
     *
     * @param AbstractProtocol $client
     *
     * @return void
     */
    public function setConnection(AbstractProtocol $connection)
    {
        $this->connection = $connection;
    }


    /**
     * Gets the connection protocol instance
     *
     * @return Protocol|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Send an email via the SMTP connection protocol
     *
     * The connection via the protocol adapter is made just-in-time to allow a
     * developer to add a custom adapter if required before mail is sent.
     *
     * @return void
     */
    public function send(Message $message)
    {
        // If sending multiple messages per session use existing adapter
        $connection = $this->getConnection();

        if (!($connection instanceof SmtpProtocol)) {
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
     * @param  Message $message 
     * @return string
     */
    protected function prepareFromAddress(Message $message)
    {
        $sender = $message->getSender();
        if ($sender instanceof AddressDescription) {
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
     * @param  Message $message 
     * @return array
     */
    protected function prepareRecipients(Message $message)
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
     * @param  Message $message 
     * @return string
     */
    protected function prepareHeaders(Message $message)
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
     * @param  Message $message 
     * @return string
     */
    protected function prepareBody(Message $message)
    {
        return $message->getBodyText();
    }

    /**
     * Lazy load the connection, and pass it helo
     * 
     * @return SmtpProtocol
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
