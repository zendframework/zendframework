<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Value\Messaging;

/**
 * This type of message contains information necessary to perform
 * point-to-point or publish-subscribe messaging.
 *
 * @package    Zend_Amf
 * @subpackage Value
 */
class AsyncMessage extends AbstractMessage
{
    /**
     * The message id to be responded to.
     * @var String
     */
    public $correlationId;
}
