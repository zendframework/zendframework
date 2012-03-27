<?php

/**
 * @namespace
 */
namespace Zend\Application\Exception;

/**
 * Exception class for Zend_Application
 *
 * @uses      \Zend\Application\Exception
 * @category  Zend
 * @package   Zend_Application
 * @uses      \Zend\Application\Exception
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class RuntimeException
    extends \RuntimeException
    implements \Zend\Application\Exception
{
}
