<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log\Filter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 */
class Mock implements FilterInterface
{
    /**
     * array of log events
     *
     * @var array
     */
    public $events = array();

    /**
     * Returns TRUE to accept the message
     *
     * @param array $event event data
     * @return bool
     */
    public function filter(array $event)
    {
        $this->events[] = $event;
        return true;
    }
}
