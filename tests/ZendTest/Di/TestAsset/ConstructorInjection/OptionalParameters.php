<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di\TestAsset\ConstructorInjection;

/**
 * Test asset used to verify that default parameters in __construct are used correctly
 */
class OptionalParameters
{
    /**
     * @var mixed
     */
    public $a = 'default';

    /**
     * @var mixed
     */
    public $b = 'default';

    /**
     * @var mixed
     */
    public $c = 'default';

    /**
     * @param mixed $a
     * @param mixed $b
     * @param mixed $c
     */
    public function __construct($a = null, $b = 'defaultConstruct', $c = array())
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }
}
