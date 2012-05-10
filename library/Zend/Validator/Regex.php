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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator;

use Traversable,
    Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Regex extends AbstractValidator
{
    const INVALID   = 'regexInvalid';
    const NOT_MATCH = 'regexNotMatch';
    const ERROROUS  = 'regexErrorous';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Invalid type given. String, integer or float expected",
        self::NOT_MATCH => "'%value%' does not match against pattern '%pattern%'",
        self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'pattern' => 'pattern'
    );

    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * Sets validator options
     *
     * @param  string|Traversable $pattern
     * @throws \Zend\Validator\Exception On missing 'pattern' parameter
     * @return void
     */
    public function __construct($pattern)
    {
        if (is_string($pattern)) {
            $this->setPattern($pattern);
            parent::__construct(array());
            return;
        }

        if ($pattern instanceof Traversable) {
            $pattern = ArrayUtils::iteratorToArray($pattern);
        }

        if (!is_array($pattern)) {
            throw new Exception\InvalidArgumentException('Invalid options provided to constructor');
        }

        if (!array_key_exists('pattern', $pattern)) {
            throw new Exception\InvalidArgumentException("Missing option 'pattern'");
        }

        $this->setPattern($pattern['pattern']);
        unset($pattern['pattern']);
        parent::__construct($pattern);
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Sets the pattern option
     *
     * @param  string $pattern
     * @throws \Zend\Validator\Exception if there is a fatal error in pattern matching
     * @return \Zend\Validator\Regex Provides a fluent interface
     */
    public function setPattern($pattern)
    {
        $this->pattern = (string) $pattern;
        $status        = @preg_match($this->pattern, "Test");

        if (false === $status) {
             throw new Exception\InvalidArgumentException("Internal error parsing the pattern '{$this->pattern}'");
        }

        return $this;
    }

    /**
     * Returns true if and only if $value matches against the pattern option
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        $status = @preg_match($this->pattern, $value);
        if (false === $status) {
            $this->error(self::ERROROUS);
            return false;
        }

        if (!$status) {
            $this->error(self::NOT_MATCH);
            return false;
        }

        return true;
    }
}
