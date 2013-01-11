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

use DomainException;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class Model implements ArraySerializableInterface
{
    protected $foo;
    protected $bar;
    protected $foobar;

    public function __set($name, $value)
    {
        throw new DomainException('Overloading to set values is not allowed');
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new DomainException('Unknown attribute');
    }

    public function exchangeArray(array $array)
    {
        foreach ($array as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }
            $this->$key = $value;
        }
    }

    public function getArrayCopy()
    {
        return array(
            'foo'    => $this->foo,
            'bar'    => $this->bar,
            'foobar' => $this->foobar,
        );
    }
}
