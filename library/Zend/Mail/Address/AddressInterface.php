<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace Zend\Mail\Address;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Address
 */
interface AddressInterface
{
    public function getEmail();
    public function getName();
    public function toString();
}
