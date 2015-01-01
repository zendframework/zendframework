<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Barcode\Object\TestAsset;

/**
 * @group      Zend_Barcode
 */
class Code128Test extends \Zend\Barcode\Object\Code128
{
    public function convertToBarcodeChars($string)
    {
        return parent::convertToBarcodeChars($string);
    }
}
