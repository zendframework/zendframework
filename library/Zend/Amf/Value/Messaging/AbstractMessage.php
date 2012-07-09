<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Value\Messaging;

/**
 * This is the default Implementation of Message, which provides
 * a convenient base for behavior and association of common endpoints
 *
 * @package    Zend_Amf
 * @subpackage Value
 */
class AbstractMessage
{
    /**
     * @var string Client identifier
     */
    public $clientId;

    /**
     * @var string Destination
     */
    public $destination;

    /**
     * @var string Message identifier
     */
    public $messageId;

    /**
     * @var int Message timestamp
     */
    public $timestamp;

    /**
     * @var int Message TTL
     */
    public $timeToLive;

    /**
     * @var object Message headers
     */
    public $headers;

    /**
     * @var string Message body
     */
    public $body;

    /**
     * generate a unique id
     *
     * Format is: ########-####-####-####-############
     * Where # is an uppercase letter or number
     * example: 6D9DC7EC-A273-83A9-ABE3-00005FD752D6
     *
     * @return string
     */
    public function generateId()
    {
        return sprintf(
            '%08X-%04X-%04X-%02X%02X-%012X',
            mt_rand(),
            mt_rand(0, 65535),
            bindec(substr_replace(
                sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)
            ),
            bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
            mt_rand(0, 255),
            mt_rand()
        );
    }
}
