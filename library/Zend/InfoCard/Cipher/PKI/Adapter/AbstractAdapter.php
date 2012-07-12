<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\Cipher\PKI\Adapter;

use Zend\InfoCard\Cipher;

/**
 * An abstract class for public-key ciphers
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Cipher
 */
abstract class AbstractAdapter implements Cipher\PKI\PKIInterface
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
     * @throws Cipher\Exception\InvalidArgumentException
     * @param integer $padding One of the constnats in this class
     * @return AbstractAdapter
     */
    public function setPadding($padding)
    {
        switch($padding) {
            case self::OAEP_PADDING:
            case self::NO_PADDING:
                $this->_padding = $padding;
                break;
            default:
                throw new Cipher\Exception\InvalidArgumentException("Invalid Padding Type Provided");
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
