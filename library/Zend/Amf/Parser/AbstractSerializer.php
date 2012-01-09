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
 * Base abstract class from which AMF serializers may descend.
 *
 * @package    Zend_Amf
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractSerializer implements Serializer
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
     * @param  Zend\Amf\Parser\OutputStream $stream
     * @return void
     */
    public function __construct(OutputStream $stream)
    {
        $this->_stream = $stream;
    }
}
