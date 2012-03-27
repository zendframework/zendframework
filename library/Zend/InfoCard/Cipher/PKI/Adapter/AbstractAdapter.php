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
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Cipher
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\InfoCard\Cipher\PKI\Adapter;

/**
 * An abstract class for public-key ciphers
 *
 * @uses       \Zend\InfoCard\Cipher\Exception
 * @uses       \Zend\InfoCard\Cipher\PKI
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Cipher
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAdapter implements \Zend\InfoCard\Cipher\PKI
{
    /**
     * OAEP Padding public key encryption
     */
    const OAEP_PADDING = 1;

    /**
     * No padding public key encryption
     */
    const NO_PADDING = 2;

    /**
     * The type of padding to use
     *
     * @var integer one of the padding constants in this class
     */
    protected $_padding;

    /**
     * Set the padding of the public key encryption
     *
     * @throws \Zend\InfoCard\Cipher\Exception
     * @param integer $padding One of the constnats in this class
     * @return Zend_InfoCard_Pki_Adapter_Abstract
     */
    public function setPadding($padding)
    {
        switch($padding) {
            case self::OAEP_PADDING:
            case self::NO_PADDING:
                $this->_padding = $padding;
                break;
            default:
                throw new \Zend\InfoCard\Cipher\Exception\InvalidArgumentException("Invalid Padding Type Provided");
                break;
        }

        return $this;
    }

    /**
     * Retruns the public-key padding used
     *
     * @return integer One of the padding constants in this class
     */
    public function getPadding()
    {
        return $this->_padding;
    }
}
