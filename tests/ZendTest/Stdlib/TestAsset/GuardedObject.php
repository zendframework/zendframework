<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\Guard\AllGuardsTrait;

class GuardedObject
{
    use AllGuardsTrait;

    public function setArrayOrTraversable($value)
    {
        $this->guardForArrayOrTraversable($value);
    }

    public function setNotEmpty($value)
    {
        $this->guardAgainstEmpty($value);
    }

    public function setNotNull($value)
    {
        $this->guardAgainstNull($value);
    }
}
