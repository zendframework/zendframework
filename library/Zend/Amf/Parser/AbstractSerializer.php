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
 * Base abstract class from which AMF serializers may descend.
 *
 * @package    Zend_Amf
 * @subpackage Parser
 */
abstract class AbstractSerializer implements SerializerInterface
{
    /**
     * Reference to the current output stream being constructed
     *
     * @var string
     */
    protected $_stream;

    /**
     * Constructor
     *
     * @param  OutputStream $stream
     * @return void
     */
    public function __construct(OutputStream $stream)
    {
        $this->_stream = $stream;
    }
}
