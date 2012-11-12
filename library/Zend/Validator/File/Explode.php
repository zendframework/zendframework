<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace Zend\Validator\File;

use Zend\Validator\Exception;
use Zend\Validator\Explode as BaseExplode;

/**
 * @category   Zend
 * @package    Zend_Validate
 */
class Explode extends BaseExplode
{
    const INVALID = 'fileExplodeInvalid';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given. File array expected",
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if all values validate true
     *
     * @param  array $value
     * @return boolean
     * @throws Exception\RuntimeException
     */
    public function isValid($value)
    {
        if (!is_array($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $values = $value;
        $this->setValue($value);

        $retval    = true;
        $messages  = array();
        $validator = $this->getValidator();

        if (!$validator) {
            throw new Exception\RuntimeException(sprintf(
                '%s expects a validator to be set; none given',
                __METHOD__
            ));
        }

        foreach ($values as $value) {
            if (!$validator->isValid($value)) {
                $messages[] = $validator->getMessages();
                $retval = false;

                if ($this->isBreakOnFirstFailure()) {
                    break;
                }
            }
        }

        $this->abstractOptions['messages'] = $messages;

        return $retval;
    }
}
