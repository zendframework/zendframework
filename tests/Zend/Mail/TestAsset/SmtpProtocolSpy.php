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
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mail\TestAsset;

use Zend\Mail\Protocol\Smtp;

/**
 * Test spy to use when testing SMTP protocol
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SmtpProtocolSpy extends Smtp
{
    protected $connect = false;
    protected $helo;
    protected $mail;
    protected $rcpt = array();
    protected $_sess = true;

    public function connect()
    {
        $this->connect = true;
        return true;
    }

    public function helo($serverName = '127.0.0.1')
    {
        parent::helo($serverName);
        $this->helo = $serverName;
    }

    public function quit()
    {
        $this->helo = null;
        $this->rset();
    }

    public function disconnect()
    {
        $this->helo    = null;
        $this->connect = false;
        $this->rset();
    }

    public function rset()
    {
        parent::rset();
        $this->rcpt = array();
    }

    public function mail($from)
    {
        parent::mail($from);
        $this->mail = $from;
    }

    public function rcpt($to)
    {
        $this->_rcpt = true;
        $this->rcpt[] = $to;
    }

    protected function _send($request)
    {
        // Save request to internal log
        $this->_addLog($request . self::EOL);
    }

    protected function _expect($code, $timeout = null) {
        return '';
    }

    /**
     * Are we connected?
     * 
     * @return bool
     */
    public function isConnected()
    {
        return $this->connect;
    }

    /**
     * Get server name we opened a connection with
     * 
     * @return null|string
     */
    public function getHelo()
    {
        return $this->helo;
    }

    /**
     * Get value of mail property
     * 
     * @return null|string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Get recipients
     * 
     * @return array
     */
    public function getRecipients()
    {
        return $this->rcpt;
    }
}