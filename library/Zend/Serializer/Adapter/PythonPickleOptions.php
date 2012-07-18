<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
class PythonPickleOptions extends AdapterOptions
{
    /**
     * @var int
     */
    protected $protocol = 0;

    /**
     * @var bool
     */
    protected $isBinary = null;

    /**
     * @param  int $protocol
     * @return PythonPickleOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setProtocol($protocol)
    {
        $protocol = (int) $protocol;
        if ($protocol < 0 || $protocol > 3) {
            throw new Exception\InvalidArgumentException(
                "Invalid or unknown protocol version '{$protocol}'"
            );
        }

        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @return int
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}
