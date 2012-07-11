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
 * InputStream is used to iterate at a binary level through the AMF request.
 *
 * InputStream extends BinaryStream as eventually BinaryStream could be placed
 * outside of Zend_Amf in order to allow other packages to use the class.
 *
 * @package    Zend_Amf
 * @subpackage Parser
 */
class InputStream extends \Zend\Amf\Util\BinaryStream
{
}
