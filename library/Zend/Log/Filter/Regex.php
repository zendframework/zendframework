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

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Filter
 */
class Regex implements FilterInterface
{
    /**
     * Regex to match
     *
     * @var string
     */
    protected $regex;

    /**
     * Filter out any log messages not matching the pattern
     *
     * @param string $regex Regular expression to test the log message
     * @return Regex
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($regex)
    {
        if (@preg_match($regex, '') === false) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid regular expression "%s"',
                $regex
            ));
        }
        $this->regex = $regex;
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param array $event event data
     * @return boolean accepted?
     */
    public function filter(array $event)
    {
        $message = $event['message'];
        if (is_array($event['message'])) {
            $message = var_export($message, TRUE); 
        }
        return preg_match($this->regex, $message) > 0;
    }
}
