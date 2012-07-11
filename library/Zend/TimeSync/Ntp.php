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

use Zend\TimeSync\Exception;

/**
 * NTP Protocol handling class
 *
 * @category  Zend
 * @package   Zend_TimeSync
 */
class Ntp extends AbstractProtocol
{
    /**
     * NTP class constructor, sets the timeserver and port number
     *
     * @param string  $timeserver Address of the timeserver to connect to
     * @param integer $port       (Optional) Port for this timeserver. By default 123
     */
    public function __construct($timeserver, $port = 123)
    {
        $this->timeserver = 'udp://' . $timeserver;
        $this->port = $port;
    }

    /**
     * Prepare local timestamp for transmission in our request packet
     *
     * NTP timestamps are represented as a 64-bit fixed-point number, in
     * seconds relative to 0000 UT on 1 January 1900.  The integer part is
     * in the first 32 bits and the fraction part in the last 32 bits
     *
     * @return string
     */
    protected function prepare()
    {
        $frac   = microtime();
        $fracba = ($frac & 0xff000000) >> 24;
        $fracbb = ($frac & 0x00ff0000) >> 16;
        $fracbc = ($frac & 0x0000ff00) >> 8;
        $fracbd = ($frac & 0x000000ff);

        $sec   = (time() + 2208988800);
        $secba = ($sec & 0xff000000) >> 24;
        $secbb = ($sec & 0x00ff0000) >> 16;
        $secbc = ($sec & 0x0000ff00) >> 8;
        $secbd = ($sec & 0x000000ff);

        // Flags
        $nul       = chr(0x00);
        $nulbyte   = $nul . $nul . $nul . $nul;
        $ntppacket = chr(0xd9) . $nul . chr(0x0a) . chr(0xfa);

        /*
         * Root delay
         *
         * Indicates the total roundtrip delay to the primary reference
         * source at the root of the synchronization subnet, in seconds
         */
        $ntppacket .= $nul . $nul . chr(0x1c) . chr(0x9b);

        /*
         * Clock Dispersion
         *
         * Indicates the maximum error relative to the primary reference source at the
         * root of the synchronization subnet, in seconds
         */
        $ntppacket .= $nul . chr(0x08) . chr(0xd7) . chr(0xff);

        /*
         * ReferenceClockID
         *
         * Identifying the particular reference clock
         */
        $ntppacket .= $nulbyte;

        /*
         * The local time, in timestamp format, at the peer when its latest NTP message
         * was sent. Contains an integer and a fractional part
         */
        $ntppacket .= chr($secba)  . chr($secbb)  . chr($secbc)  . chr($secbd);
        $ntppacket .= chr($fracba) . chr($fracbb) . chr($fracbc) . chr($fracbd);

        /*
         * The local time, in timestamp format, at the peer. Contains an integer
         * and a fractional part.
         */
        $ntppacket .= $nulbyte;
        $ntppacket .= $nulbyte;

        /*
         * This is the local time, in timestamp format, when the latest NTP message from
         * the peer arrived. Contains an integer and a fractional part.
         */
        $ntppacket .= $nulbyte;
        $ntppacket .= $nulbyte;

        /*
         * The local time, in timestamp format, at which the
         * NTP message departed the sender. Contains an integer
         * and a fractional part.
         */
        $ntppacket .= chr($secba)  . chr($secbb)  . chr($secbc)  . chr($secbd);
        $ntppacket .= chr($fracba) . chr($fracbb) . chr($fracbc) . chr($fracbd);

        return $ntppacket;
    }

    /**
     * Calculates a 32bit integer
     *
     * @param string $input
     * @return integer
     */
    protected function getInteger($input)
    {
        $f1  = str_pad(ord($input[0]), 2, '0', STR_PAD_LEFT);
        $f1 .= str_pad(ord($input[1]), 2, '0', STR_PAD_LEFT);
        $f1 .= str_pad(ord($input[2]), 2, '0', STR_PAD_LEFT);
        $f1 .= str_pad(ord($input[3]), 2, '0', STR_PAD_LEFT);

        return (int) $f1;
    }

    /**
     * Calculates a 32bit signed fixed point number
     *
     * @param string $input
     * @return float
     */
    protected function getFloat($input)
    {
        $f1  = str_pad(ord($input[0]), 2, '0', STR_PAD_LEFT);
        $f1 .= str_pad(ord($input[1]), 2, '0', STR_PAD_LEFT);
        $f1 .= str_pad(ord($input[2]), 2, '0', STR_PAD_LEFT);
        $f1 .= str_pad(ord($input[3]), 2, '0', STR_PAD_LEFT);
        $f2  = $f1 >> 17;
        $f3  = ($f1 & 0x0001FFFF);
        $f1  = $f2 . '.' . $f3;

        return (float) $f1;
    }

    /**
     * Calculates a 64bit timestamp
     *
     * @param string $input
     * @return float
     */
    protected function getTimestamp($input)
    {
        $f1  = (ord($input[0]) * pow(256, 3));
        $f1 += (ord($input[1]) * pow(256, 2));
        $f1 += (ord($input[2]) * pow(256, 1));
        $f1 += (ord($input[3]));
        $f1 -= 2208988800;

        $f2  = (ord($input[4]) * pow(256, 3));
        $f2 += (ord($input[5]) * pow(256, 2));
        $f2 += (ord($input[6]) * pow(256, 1));
        $f2 += (ord($input[7]));

        return (float) ($f1 . "." . $f2);
    }

    /**
     * Reads the data returned from the timeserver
     *
     * This will return an array with binary data listing:
     *
     * @return array
     * @throws Exception\RuntimeException When timeserver can not be connected
     */
    protected function read()
    {
        $flags = ord(fread($this->socket, 1));
        $info  = stream_get_meta_data($this->socket);

        if ($info['timed_out'] === true) {
            fclose($this->socket);
            throw new Exception\RuntimeException('could not connect to ' .
                "'$this->timeserver' on port '$this->port', reason: 'server timed out'");
        }

        $result = array(
            'flags'          => $flags,
            'stratum'        => ord(fread($this->socket, 1)),
            'poll'           => ord(fread($this->socket, 1)),
            'precision'      => ord(fread($this->socket, 1)),
            'rootdelay'      => $this->getFloat(fread($this->socket, 4)),
            'rootdispersion' => $this->getFloat(fread($this->socket, 4)),
            'referenceid'    => fread($this->socket, 4),
            'referencestamp' => $this->getTimestamp(fread($this->socket, 8)),
            'originatestamp' => $this->getTimestamp(fread($this->socket, 8)),
            'receivestamp'   => $this->getTimestamp(fread($this->socket, 8)),
            'transmitstamp'  => $this->getTimestamp(fread($this->socket, 8)),
            'clientreceived' => microtime(true)
        );

        $this->disconnect();

        return $result;
    }

    /**
     * Sends the NTP packet to the server
     *
     * @param  string $data Data to send to the timeserver
     * @return void
     */
    protected function write($data)
    {
        $this->connect();

        fwrite($this->socket, $data);
        stream_set_timeout($this->socket, TimeSync::$options['timeout']);
    }

    /**
     * Extracts the binary data returned from the timeserver
     *
     * @param  string|array $binary Data returned from the timeserver
     * @return integer Difference in seconds
     */
    protected function extract($binary)
    {
        /*
         * Leap Indicator bit 1100 0000
         *
         * Code warning of impending leap-second to be inserted at the end of
         * the last day of the current month.
         */
        $leap = ($binary['flags'] & 0xc0) >> 6;
        switch ($leap) {
            case 0:
                $this->info['leap'] = '0 - no warning';
                break;

            case 1:
                $this->info['leap'] = '1 - last minute has 61 seconds';
                break;

            case 2:
                $this->info['leap'] = '2 - last minute has 59 seconds';
                break;

            default:
                $this->info['leap'] = '3 - not syncronised';
                break;
        }

        /*
         * Version Number bit 0011 1000
         *
         * This should be 3 (RFC 1305)
         */
        $this->info['version'] = ($binary['flags'] & 0x38) >> 3;

        /*
         * Mode bit 0000 0111
         *
         * Except in broadcast mode, an NTP association is formed when two peers
         * exchange messages and one or both of them create and maintain an
         * instantiation of the protocol machine, called an association.
         */
        $mode = ($binary['flags'] & 0x07);
        switch ($mode) {
            case 1:
                $this->info['mode'] = 'symmetric active';
                break;

            case 2:
                $this->info['mode'] = 'symmetric passive';
                break;

            case 3:
                $this->info['mode'] = 'client';
                break;

            case 4:
                $this->info['mode'] = 'server';
                break;

            case 5:
                $this->info['mode'] = 'broadcast';
                break;

            default:
                $this->info['mode'] = 'reserved';
                break;
        }

        $ntpserviceid = 'Unknown Stratum ' . $binary['stratum'] . ' Service';

        /*
         * Reference Clock Identifier
         *
         * Identifies the particular reference clock.
         */
        $refid = strtoupper($binary['referenceid']);
        switch ($binary['stratum']) {
            case 0:
                if (substr($refid, 0, 3) === 'DCN') {
                    $ntpserviceid = 'DCN routing protocol';
                } elseif (substr($refid, 0, 4) === 'NIST') {
                    $ntpserviceid = 'NIST public modem';
                } elseif (substr($refid, 0, 3) === 'TSP') {
                    $ntpserviceid = 'TSP time protocol';
                } elseif (substr($refid, 0, 3) === 'DTS') {
                    $ntpserviceid = 'Digital Time Service';
                }
                break;

            case 1:
                if (substr($refid, 0, 4) === 'ATOM') {
                    $ntpserviceid = 'Atomic Clock (calibrated)';
                } elseif (substr($refid, 0, 3) === 'VLF') {
                    $ntpserviceid = 'VLF radio';
                } elseif ($refid === 'CALLSIGN') {
                    $ntpserviceid = 'Generic radio';
                } elseif (substr($refid, 0, 4) === 'LORC') {
                    $ntpserviceid = 'LORAN-C radionavigation';
                } elseif (substr($refid, 0, 4) === 'GOES') {
                    $ntpserviceid = 'GOES UHF environment satellite';
                } elseif (substr($refid, 0, 3) === 'GPS') {
                    $ntpserviceid = 'GPS UHF satellite positioning';
                }
                break;

            default:
                $ntpserviceid  = ord(substr($binary['referenceid'], 0, 1));
                $ntpserviceid .= '.';
                $ntpserviceid .= ord(substr($binary['referenceid'], 1, 1));
                $ntpserviceid .= '.';
                $ntpserviceid .= ord(substr($binary['referenceid'], 2, 1));
                $ntpserviceid .= '.';
                $ntpserviceid .= ord(substr($binary['referenceid'], 3, 1));
                break;
        }

        $this->info['ntpid'] = $ntpserviceid;

        /*
         * Stratum
         *
         * Indicates the stratum level of the local clock
         */
        switch ($binary['stratum']) {
            case 0:
                $this->info['stratum'] = 'undefined';
                break;

            case 1:
                $this->info['stratum'] = 'primary reference';
                break;

            default:
                $this->info['stratum'] = 'secondary reference';
                break;
        }

        /*
         * Indicates the total roundtrip delay to the primary reference source at the
         * root of the synchronization subnet, in seconds.
         *
         * Both positive and negative values, depending on clock precision and skew, are
         * possible.
         */
        $this->info['rootdelay'] = $binary['rootdelay'];

        /*
         * Indicates the maximum error relative to the primary reference source at the
         * root of the synchronization subnet, in seconds.
         *
         * Only positive values greater than zero are possible.
         */
        $this->info['rootdispersion'] = $binary['rootdispersion'];

        /*
         * The roundtrip delay of the peer clock relative to the local clock
         * over the network path between them, in seconds.
         *
         * Note that this variable can take on both positive and negative values,
         * depending on clock precision and skew-error accumulation.
         */
        $this->info['roundtrip']  = $binary['receivestamp'];
        $this->info['roundtrip'] -= $binary['originatestamp'];
        $this->info['roundtrip'] -= $binary['transmitstamp'];
        $this->info['roundtrip'] += $binary['clientreceived'];
        $this->info['roundtrip'] /= 2;

        // The offset of the peer clock relative to the local clock, in seconds.
        $this->info['offset']  = $binary['receivestamp'];
        $this->info['offset'] -= $binary['originatestamp'];
        $this->info['offset'] += $binary['transmitstamp'];
        $this->info['offset'] -= $binary['clientreceived'];
        $this->info['offset'] /= 2;
        $time = (time() - $this->info['offset']);

        return $time;
    }
}
