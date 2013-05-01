<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
* @package   Zend_Form
*/

namespace ZendTest\Form\TestAsset;

use Zend\Stdlib\Hydrator\HydratorAwareInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class HydratorAwareModel implements HydratorAwareInterface
{
    protected $hydrator = null;

    protected $foo = null;
    protected $bar = null;

    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new ClassMethods();
        }
        return $this->hydrator;
    }

    public function setFoo($value)
    {
        $this->foo = $value;
        return $this;
    }

    public function setBar($value)
    {
       $this->bar = $value;
       return $this;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
