<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Parser;

/**
 * Interface for all AMF serializers.
 *
 * @package    Zend_Amf
 * @subpackage Parser
 */
interface SerializerInterface
{
    /**
     * Constructor
     *
     * @param  OutputStream $stream
     * @return void
     */
    public function __construct(OutputStream $stream);

    /**
     * Find the PHP object type and convert it into an AMF object type
     *
     * @param  mixed $content
     * @param  int $markerType
     * @return void
     */
    public function writeTypeMarker(&$content, $markerType = null);
}
