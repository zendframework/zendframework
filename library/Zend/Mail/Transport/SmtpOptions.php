<?php

namespace Zend\Mail\Transport;

use Zend\Mail\Exception,
    Zend\Stdlib\Options;

class SmtpOptions extends Options
{
    /**
     * @var string Local client hostname
     */
    protected $name = 'localhost';

    /**
     * @var string
     */
    protected $auth;

    /**
     * Connection configuration (passed to the underlying Protocol class)
     * 
     * @var array
     */
    protected $connectionConfig = array();

    /**
     * @var string Remote SMTP hostname or IP
     */
    protected $host = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 25;

    /**
     * Return the local client hostname
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }
 
    /**
     * Set the local client hostname or IP
     *
     * @todo   hostname/IP validation
     * @param  string $name
     * @return SmtpOptions
     */
    public function setName($name)
    {
        if (!is_string($name) && !is_null($name)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Name must be a string or null; argument of type "%s" provided',
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Get authentication class
     *
     * This should be the classname of a class in the Zend\Mail\Protocol\Smtp\Auth 
     * namespace.
     *
     * @return null|string
     */
    public function getAuth() 
    {
        return $this->auth;
    }

    /**
     * Set authentication class 
     *
     * @param  string $authClass the value to be set
     * @return SmtpOptions
     */
    public function setAuth($authClass) 
    {
        if (!is_string($credentials) && !is_null($credentials)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Authentication class must be a string or null; argument of type "%s" provided',
                (is_object($authClass) ? get_class($authClass) : gettype($authClass))
            ));
        }
        $this->auth = $authClass;
        return $this;
    }

    /**
     * Get connection configuration array
     * 
     * @return array
     */
    public function getConnectionConfig()
    {
        return $this->connectionConfig;
    }

    /**
     * Set connection configuration array
     * 
     * @param  array $config 
     * @return SmtpOptions
     */
    public function setConnectionConfig(array $config)
    {
        $this->connectionConfig = $config;
        return $this;
    }

    /**
     * Get the host name
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the SMTP host
     * 
     * @todo   hostname/IP validation
     * @param  string $host 
     * @return SmtpOptions
     */
    public function setHost($host)
    {
        $this->host = (string) $host;
    }

    /**
     * Get the port the SMTP server runs on
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
 
    /**
     * Set the port the SMTP server runs on
     *
     * @param  int $port
     * @return SmtpOptions
     */
    public function setPort($port)
    {
        $port = (int) $port;
        if ($port < 1) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Port must be greater than 1; received "%d"',
                $port
            ));
        }
        $this->port = $port;
        return $this;
    }
}
