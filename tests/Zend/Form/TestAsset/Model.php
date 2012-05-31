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
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\TestAsset;

use DomainException;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
