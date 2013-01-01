<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\View\Helper\TestAsset;

use Zend\View\Helper\AbstractHelper;

class ConcreteHelper extends AbstractHelper
{
    public function __invoke($output)
    {
        return $output;
    }
}
