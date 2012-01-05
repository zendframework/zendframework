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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator\Barcode;

/**
 * @uses       \Zend\Validator\Barcode\AbstractAdapter
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Codabar extends AbstractAdapter
{
    /**
     * Constructor for this barcode adapter
     *
     * @return void
     */
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters('0123456789-$:/.+ABCDTN*E');
        $this->useChecksum(false);
    }

    /**
     * Checks for allowed characters
     * @see Zend\Validator\Barcode.AbstractAdapter::checkChars()
     */
    public function hasValidCharacters($value)
    {
        $first = $value[0];
        if (strpbrk($value, 'ABCD')) {
            $first = $value[0];
            if (!strpbrk($first, 'ABCD')) {
                // Missing start char
                return false;
            }

            $last = substr($value, -1, 1);
            if (!strpbrk($last, 'ABCD')) {
                // Missing stop char
                return false;
            }

            $value = substr($value, 1, -1);
        } elseif (strpbrk($value, 'TN*E')) {
            $first = $value[0];
            if (!strpbrk($first, 'TN*E')) {
                // Missing start char
                return false;
            }

            $last = substr($value, -1, 1);
            if (!strpbrk($last, 'TN*E')) {
                // Missing stop char
                return false;
            }

            $value = substr($value, 1, -1);
        }

        $chars  = $this->getCharacters();
        $this->setCharacters('0123456789-$:/.+');
        $result = parent::hasValidCharacters($value);
        $this->setCharacters($chars);
        return $result;
    }
}
