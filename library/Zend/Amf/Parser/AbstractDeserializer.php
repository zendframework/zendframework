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
 * Abstract class from which deserializers may descend.
 *
 * Logic for deserialization of the AMF envelop is based on resources supplied
 * by Adobe Blaze DS. For and example of deserialization please review the BlazeDS
 * source tree.
 *
 * @see        http://opensource.adobe.com/svn/opensource/blazeds/trunk/modules/core/src/java/flex/messaging/io/amf/
 * @package    Zend_Amf
 * @subpackage Parser
 */
abstract class AbstractDeserializer implements DeserializerInterface
{
    /**
     * The raw string that represents the AMF request.
     *
     * @var InputStream
     */
    protected $_stream;

    /**
     * Constructor
     *
     * @param  InputStream $stream
     * @return void
     */
    public function __construct(InputStream $stream)
    {
        $this->_stream = $stream;
    }
}
