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
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2007 Pádraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 102 2007-07-13 15:16:01Z padraic $
 */


/**
 * Zend_Crypt_Exception
 */
require_once 'Zend/Crypt/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2007 Pádraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Crypt_Hmac_Exception extends Zend_Crypt_Exception
{}
