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
    protected $data;

    /**
     * "Connect" to server
     * 
     * @return void
     */
    public function connect()
    {
        $this->connect = true;
    }

    /**
     * Set server name we're talking to
     * 
     * @param  string $serverName 
     * @return void
     */
    public function helo($serverName = '127.0.0.1')
    {
        $this->helo = $serverName;
    }

    /**
     * quit implementation
     *
     * Resets helo value and calls rset
     * 
     * @return void
     */
    public function quit()
    {
        $this->helo = null;
        $this->rset();
    }

    /**
     * Disconnect implementation
     *
     * Resets connect flag and calls rset
     * 
     * @return void
     */
    public function disconnect()
    {
        $this->helo    = null;
        $this->connect = false;
        $this->rset();
    }

    /**
     * "Reset" connection
     *
     * Resets state of mail, rcpt, and data properties
     * 
     * @return void
     */
    public function rset()
    {
        $this->mail = null;
        $this->rcpt = array();
        $this->data = null;
    }

    /**
     * Set envelope FROM
     * 
     * @param  string $from 
     * @return void
     */
    public function mail($from)
    {
        $this->mail = $from;
    }

    /**
     * Add recipient
     * 
     * @param  string $to 
     * @return void
     */
    public function rcpt($to)
    {
        $this->rcpt[] = $to;
    }

    /**
     * Set data
     * 
     * @param  string $data 
     * @return void
     */
    public function data($data)
    {
        $this->data = $data;
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

    /**
     * Get data value
     * 
     * @return null|string
     */
    public function getData()
    {
        return $this->data;
    }
}
