<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */
namespace Zend\Log\Writer\FingersCrossed;

use Zend\Log\Logger;

/**
 *
 * @category Zend
 * @package Zend_Log
 * @subpackage Writer
 */
class ErrorLevelActivationStrategy implements ActivationStrategyInterface
{

    protected $priority;

    /**
     * Constructor
     *
     * @param int $priority any event priority equals or severe than this will deactivate buffering
     */
    public function __construct($priority = Logger::WARN)
    {
        $this->priority = $priority;
    }

    /**
     * Returns whether the given record activates the writer
     *
     * @param array event data
     * @return bool
     */
    public function isWriterActivated(array $event)
    {
        return $event['priority'] <= $this->priority;
    }
}