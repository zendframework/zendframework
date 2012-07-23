<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_TimeSync
 */

namespace Zend\TimeSync;

/**
 * SNTP Protocol handling class
 *
 * @category  Zend
 * @package   Zend_TimeSync
 */
class Sntp extends AbstractProtocol
{
    /**
     * Socket delay
     *
     * @var integer
     */
    private $delay;

    /**
     * Class constructor, sets the timeserver and port number
     *
     * @param string  $timeserver Timeserver to connect to
     * @param integer $port       (Optional) Port for this timeserver. By default 37
     */
    public function __construct($timeserver, $port = 37)
    {
        $this->timeserver = 'udp://' . $timeserver;
        $this->port = $port;
    }

    /**
     * Prepares the data that will be send to the timeserver
     *
     * @return string
     */
    protected function prepare()
    {
        return "\n";
    }

    /**
     * Reads the data returned from the timeserver
     *
     * @return string
     */
    protected function read()
    {
        $result       = fread($this->socket, 49);
        $this->delay = (($this->delay - time()) / 2);

        return $result;
    }

    /**
     * Writes data to to the timeserver
     *
     * @param  string $data Data to write to the timeserver
     * @return void
     */
    protected function write($data)
    {
        $this->connect();
        $this->delay = time();
        fwrite($this->socket, $data);
    }

    /**
     * Extracts the data returned from the timeserver
     *
     * @param  string $result Data to extract
     * @return integer
     */
    protected function extract($result)
    {
        $dec   = hexdec('7fffffff');
        $time  = abs(($dec - hexdec(bin2hex($result))) - $dec);
        $time -= 2208988800;
        // Socket delay
        $time -= $this->delay;

        $this->info['offset'] = $this->delay;

        return $time;
    }
}
