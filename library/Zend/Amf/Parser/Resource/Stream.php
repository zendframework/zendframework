<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Parser\Resource;

/**
 * This class will convert stream resource to string by just reading it
 *
 * @package    Zend_Amf
 * @subpackage Parser
 */
class Stream
{
    /**
     * Parse resource into string
     *
     * @param resource $resource Stream resource
     * @return array
     */
    public function parse($resource)
    {
        return stream_get_contents($resource);
    }
}
