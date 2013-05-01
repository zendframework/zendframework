<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\Hydrator\Strategy\DefaultStrategy;

class HydratorStrategyContextAware extends DefaultStrategy
{
    public $object;
    public $data;

    public function extract($value, $object = null)
    {
        $this->object = $object;
        return $value;
    }

    public function hydrate($value, $data = null)
    {
        $this->data = $data;
        return $value;
    }
}
