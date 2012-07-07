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
 * @package    Zend_I18n
 * @subpackage Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\Translator\Plural;

use Closure;
use Zend\I18n\Exception;

/**
 * Parser symbol.
 *
 * All properties in the symbol are defined as public for easier and faster
 * access from the applied closures. An exception are the closure properties
 * themself, as they have to be accessed via the appropriate getter and
 * setter methods.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Symbol
{
    /**
     * Parser instance.
     *
     * @var Parser
     */
    public $parser;

    /**
     * Node or token type name.
     *
     * @var string
     */
    public $id;

    /**
     * Left binding power (precedence).
     *
     * @var integer
     */
    public $leftBindingPower;

    /**
     * Getter for null denotation.
     *
     * @var callable
     */
    protected $nullDenotationGetter;

    /**
     * Getter for left denotation.
     *
     * @var callable
     */
    protected $leftDenotationGetter;

    /**
     * Value used by literals.
     *
     * @var mixed
     */
    public $value;

    /**
     * First node value.
     *
     * @var Symbol
     */
    public $first;

    /**
     * Second node value.
     *
     * @var Symbol
     */
    public $second;

    /**
     * Third node value.
     *
     * @var Symbol
     */
    public $third;

    /**
     * Create a new symbol.
     *
     * @param  Parser  $parser
     * @param  string  $id
     * @param  integer $leftBindingPower
     * @return void
     */
    public function __construct(Parser $parser, $id, $leftBindingPower)
    {
        $this->parser               = $parser;
        $this->id                   = $id;
        $this->leftBindingPower     = $leftBindingPower;
    }

    /**
     * Set the null denotation getter.
     *
     * @param  Closure $getter
     * @return Symbol
     */
    public function setNullDenotationGetter(Closure $getter)
    {
        $this->nullDenotationGetter = $getter;
        return $this;
    }

    /**
     * Set the left denotation getter.
     *
     * @param  Closure $getter
     * @return Symbol
     */
    public function setLeftDenotationGetter(Closure $getter)
    {
        $this->leftDenotationGetter = $getter;
        return $this;
    }

    /**
     * Get null denotation.
     *
     * @return Symbol
     */
    public function getNullDenotation()
    {
        if ($this->nullDenotationGetter === null) {
            throw new Exception\ParseException(sprintf(
                'Syntax error: %s', $this->id
            ));
        }

        /** @var callable $function  */
        $function = $this->nullDenotationGetter;
        return $function($this);
    }

    /**
     * Get left denotation.
     *
     * @param  Symbol $left
     * @return Symbol
     */
    public function getLeftDenotation($left)
    {
        if ($this->leftDenotationGetter === null) {
            throw new Exception\ParseException(sprintf(
                'Unknown operator: %s', $this->id
            ));
        }

        /** @var callable $function  */
        $function = $this->leftDenotationGetter;
        return $function($this, $left);
    }
}
