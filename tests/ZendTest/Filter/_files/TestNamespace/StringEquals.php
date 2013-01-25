<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace TestNamespace;

use Zend\Validator\AbstractValidator;

/**
 * Mock file for testbed
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 */
class StringEquals extends AbstractValidator
{

    const NOT_EQUALS = 'stringNotEquals';

    /**
     * Array with message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_EQUALS => 'Not all strings in the argument are equal'
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if all the elements of the array argument
     * are equal to one another with string comparison.
     *
     * @param  array $value Value to validate
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $initial = (string) current((array) $value);
        foreach ((array) $value as $element) {
            if ((string) $element !== $initial) {
                $this->error(self::NOT_EQUALS);
                return false;
            }
        }

        return true;
    }

}
