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
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mail\Storage;
use Zend\Mail\AbstractStorage,
    Zend\Mail\Storage\Exception,
    Zend\Mail\Protocol,
    Zend\Mail,
    Zend\Mime;

/**
 * @uses       \Zend\Mail\Message\Message
 * @uses       \Zend\Mail\Protocol\Pop3
 * @uses       \Zend\Mail\Storage\AbstractStorage
 * @uses       \Zend\Mail\Storage\Exception
 * @uses       \Zend\Mime\Decode
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Pop3 extends AbstractStorage
{
    /**
     * protocol handler
     * @var null|\Zend\Mail\Protocol\Pop3
     */
    protected $_protocol;


    /**
     * Count messages all messages in current box
     *
     * @return int number of messages
     * @throws \Zend\Mail\Storage\Exception
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function countMessages()
    {
        $count  = null; // "Declare" variable before first usage.
        $octets = null; // "Declare" variable since it's passed by reference
        $this->_protocol->status($count, $octets);
        return (int)$count;
    }

    /**
     * get a list of messages with number and size
     *
     * @param int $id number of message
     * @return int|array size of given message of list with all messages as array(num => size)
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function getSize($id = 0)
    {
        $id = $id ? $id : null;
        return $this->_protocol->getList($id);
    }

    /**
     * Fetch a message
     *
     * @param int $id number of message
     * @return \Zend\Mail\Message\Message
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function getMessage($id)
    {
        $bodyLines = 0;
        $message = $this->_protocol->top($id, $bodyLines, true);

        return new $this->_messageClass(array('handler' => $this, 'id' => $id, 'headers' => $message,
                                              'noToplines' => $bodyLines < 1));
    }

    /*
     * Get raw header of message or part
     *
     * @param  int               $id       number of message
     * @param  null|array|string $part     path to part or null for messsage header
     * @param  int               $topLines include this many lines with header (after an empty line)
     * @return string raw header
     * @throws \Zend\Mail\Protocol\Exception
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getRawHeader($id, $part = null, $topLines = 0)
    {
        if ($part !== null) {
            // TODO: implement
            throw new Exception\RuntimeException('not implemented');
        }

        return $this->_protocol->top($id, 0, true);
    }

    /*
     * Get raw content of message or part
     *
     * @param  int               $id   number of message
     * @param  null|array|string $part path to part or null for messsage content
     * @return string raw content
     * @throws \Zend\Mail\Protocol\Exception
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getRawContent($id, $part = null)
    {
        if ($part !== null) {
            // TODO: implement
            throw new Exception\RuntimeException('not implemented');
        }

        $content = $this->_protocol->retrieve($id);
        // TODO: find a way to avoid decoding the headers
        $headers = null; // "Declare" variable since it's passed by reference
        $body    = null; // "Declare" variable before first usage.
        Mime\Decode::splitMessage($content, $headers, $body);
        return $body;
    }

    /**
     * create instance with parameters
     * Supported paramters are
     *   - host hostname or ip address of POP3 server
     *   - user username
     *   - password password for user 'username' [optional, default = '']
     *   - port port for POP3 server [optional, default = 110]
     *   - ssl 'SSL' or 'TLS' for secure sockets
     *
     * @param  $params array  mail reader specific parameters
     * @throws \Zend\Mail\Storage\Exception
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function __construct($params)
    {
        if (is_array($params)) {
            $params = (object)$params;
        }

        $this->_has['fetchPart'] = false;
        $this->_has['top']       = null;
        $this->_has['uniqueid']  = null;

        if ($params instanceof Protocol\Pop3) {
            $this->_protocol = $params;
            return;
        }

        if (!isset($params->user)) {
            throw new Exception\InvalidArgumentException('need at least user in params');
        }

        $host     = isset($params->host)     ? $params->host     : 'localhost';
        $password = isset($params->password) ? $params->password : '';
        $port     = isset($params->port)     ? $params->port     : null;
        $ssl      = isset($params->ssl)      ? $params->ssl      : false;

        $this->_protocol = new Protocol\Pop3();
        $this->_protocol->connect($host, $port, $ssl);
        $this->_protocol->login($params->user, $password);
    }

    /**
     * Close resource for mail lib. If you need to control, when the resource
     * is closed. Otherwise the destructor would call this.
     *
     * @return null
     */
    public function close()
    {
        $this->_protocol->logout();
    }

    /**
     * Keep the server busy.
     *
     * @return null
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function noop()
    {
        return $this->_protocol->noop();
    }

    /**
     * Remove a message from server. If you're doing that from a web enviroment
     * you should be careful and use a uniqueid as parameter if possible to
     * identify the message.
     *
     * @param  int $id number of message
     * @return null
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function removeMessage($id)
    {
        $this->_protocol->delete($id);
    }

    /**
     * get unique id for one or all messages
     *
     * if storage does not support unique ids it's the same as the message number
     *
     * @param int|null $id message number
     * @return array|string message number for given message or all messages as array
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getUniqueId($id = null)
    {
        if (!$this->hasUniqueid) {
            if ($id) {
                return $id;
            }
            $count = $this->countMessages();
            if ($count < 1) {
                return array();
            }
            $range = range(1, $count);
            return array_combine($range, $range);
        }

        return $this->_protocol->uniqueid($id);
    }

    /**
     * get a message number from a unique id
     *
     * I.e. if you have a webmailer that supports deleting messages you should use unique ids
     * as parameter and use this method to translate it to message number right before calling removeMessage()
     *
     * @param string $id unique id
     * @return int message number
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getNumberByUniqueId($id)
    {
        if (!$this->hasUniqueid) {
            return $id;
        }

        $ids = $this->getUniqueId();
        foreach ($ids as $k => $v) {
            if ($v == $id) {
                return $k;
            }
        }

        throw new Exception\InvalidArgumentException('unique id not found');
    }

    /**
     * Special handling for hasTop and hasUniqueid. The headers of the first message is
     * retrieved if Top wasn't needed/tried yet.
     *
     * @see \Zend\Mail\Storage\Abstract::__get()
     * @param  string $var
     * @return string
     * @throws \Zend\Mail\Storage\Exception
     */
    public function __get($var)
    {
        $result = parent::__get($var);
        if ($result !== null) {
            return $result;
        }

        if (strtolower($var) == 'hastop') {
            if ($this->_protocol->hasTop === null) {
                // need to make a real call, because not all server are honest in their capas
                try {
                    $this->_protocol->top(1, 0, false);
                } catch(Mail\Exception $e) {
                    // ignoring error
                }
            }
            $this->_has['top'] = $this->_protocol->hasTop;
            return $this->_protocol->hasTop;
        }

        if (strtolower($var) == 'hasuniqueid') {
            $id = null;
            try {
                $id = $this->_protocol->uniqueid(1);
            } catch(Mail\Exception $e) {
                // ignoring error
            }
            $this->_has['uniqueid'] = $id ? true : false;
            return $this->_has['uniqueid'];
        }

        return $result;
    }
}
