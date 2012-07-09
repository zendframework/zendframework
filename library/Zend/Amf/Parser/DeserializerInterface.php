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
 * Interface that all deserializers must implement.
 *
 * @see        http://opensource.adobe.com/svn/opensource/blazeds/trunk/modules/core/src/java/flex/messaging/io/amf/
 * @package    Zend_Amf
 * @subpackage Parser
 */
interface DeserializerInterface
{
    /**
     * Constructor
     *
     * @param  InputStream $stream
     * @return void
     */
    public function __construct(InputStream $stream);

    /**
     * Checks for AMF marker types and calls the appropriate methods
     * for deserializing those marker types. Markers are the data type of
     * the following value.
     *
     * @param  int $typeMarker
     * @return mixed Whatever the data type is of the marker in php
     */
    public function readTypeMarker($markerType = null);
}
