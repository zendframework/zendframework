<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Transport;

use Zend\Stdlib\AbstractOptions;

class Envelope extends AbstractOptions
{
    /**
     * @var string|null
     */
    protected $from;

    /**
     * @var string|null
     */
    protected $to;

    /**
     * Get MAIL FROM
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set MAIL FROM
     *
     * @param  string $from
     */
    public function setFrom($from)
    {
        $this->from = (string) $from;
    }

    /**
     * Get RCPT TO
     *
     * @return string|null
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set RCPT TO
     *
     * @param  string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }
}
