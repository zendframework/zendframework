<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log\Filter;

use Zend\Log\Exception;
use Zend\Validator\ValidatorInterface as ZendValidator;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Filter
 */
class Validator implements FilterInterface
{
    /**
     * Regex to match
     *
     * @var ZendValidator
     */
    protected $validator;

    /**
     * Filter out any log messages not matching the validator
     *
     * @param ZendValidator $validator
     * @return Validator
     */
    public function __construct(ZendValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param array $event event data
     * @return boolean
     */
    public function filter(array $event)
    {
        return $this->validator->isValid($event['message']);
    }
}
