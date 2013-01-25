<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\TestAsset;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 */
class VariableFunctor
{
    public $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function __invoke()
    {
        return $this->value;
    }
}
