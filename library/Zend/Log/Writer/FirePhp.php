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
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Log\Writer;

use Zend\Log\Formatter\FirePhp as FirePhpFormatter;
use Zend\Log\Logger;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FirePhp extends AbstractWriter
{
    /**
     * Whether or not the writer is enabled.
     * 
     * @var bool
     */
    private $enabled;

    /**
     * Initializes a new instance of this class.
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->formatter = new FirePhpFormatter();
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $line = $this->formatter->format($event);

        switch ($event['priority']) {
            case Logger::EMERG:
                \FB::log($line);
                break;
            case Logger::ALERT:
                \FB::log($line);
                break;
            case Logger::CRIT:
                \FB::log($line);
                break;
            case Logger::ERR:
                \FB::error($line);
                break;
            case Logger::WARN:
                \FB::warn($line);
                break;
            case Logger::NOTICE:
                \FB::log($line);
                break;
            case Logger::INFO:
                \FB::info($line);
                break;
            case Logger::DEBUG:
                \FB::trace($line);
                break;
            default:
                \FB::log($line);
                break;
        }
    }

    /**
     * Checks whether or not the writer is enabled.
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Enables or disables the writer.
     * 
     * @param bool $enabled The flag to set.
     * @return FirePhp
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

}
