<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace ZendTest\Soap\TestAsset;

/**
 * MyCalculatorService
 *
 * Class used in DocumentLiteralWrapperTest
 */
class MyCalculatorService
{
    /**
     * @param int $x
     * @param int $y
     * @return int
     */
    public function add($x, $y)
    {
        return $x+$y;
    }
}
