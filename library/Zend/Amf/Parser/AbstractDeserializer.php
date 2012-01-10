<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractDeserializer implements Deserializer
{
    /**
     * The raw string that represents the AMF request.
     *
     * @var Zend\Amf\Parser\InputStream
     */
    protected $_stream;

    /**
     * Constructor
     *
     * @param  Zend\Amf\Parser\InputStream $stream
     * @return void
     */
    public function __construct(InputStream $stream)
    {
        $this->_stream = $stream;
    }
}
