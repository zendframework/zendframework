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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ean8 extends AbstractAdapter
{
    /**
     * Allowed barcode lengths
     * @var integer
     */
    protected $_length = array(7, 8);

    /**
     * Allowed barcode characters
     * @var string
     */
    protected $_characters = '0123456789';

    /**
     * Checksum function
     * @var string
     */
    protected $_checksum = '_gtin';

    /**
     * Overrides parent checkLength
     *
     * @param string $value Value
     * @return boolean
     */
    public function checkLength($value)
    {
        if (strlen($value) == 7) {
            $this->setCheck(false);
        } else {
            $this->setCheck(true);
        }

        return parent::checkLength($value);
    }
}
