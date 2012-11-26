<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log\Writer;

use Zend\Log\Writer\ChromePhp\ChromePhpBridge;
use Zend\Log\Writer\ChromePhp\ChromePhpInterface;
use Zend\Log\Formatter\ChromePhp as ChromePhpFormatter;
use Zend\Log\Logger;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 */
class ChromePhp extends AbstractWriter
{
    /**
     * The instance of ChromePhpInterface that is used to log messages to.
     *
     * @var ChromePhpInterface
     */
    protected $chromephp;

    /**
     * Initializes a new instance of this class.
     *
     * @param null|ChromePhpInterface $instance An instance of ChromePhpInterface
     *        that should be used for logging
     */
    public function __construct(ChromePhpInterface $instance = null)
    {
        $this->chromephp = $instance === null ? $this->getChromePhp() : $instance;
        $this->formatter = new ChromePhpFormatter();
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        $line = $this->formatter->format($event);

        switch ($event['priority']) {
            case Logger::EMERG:
            case Logger::ALERT:
            case Logger::CRIT:
            case Logger::ERR:
                $this->chromephp->error($line);
                break;
            case Logger::WARN:
                $this->chromephp->warn($line);
                break;
            case Logger::NOTICE:
            case Logger::INFO:
                $this->chromephp->info($line);
                break;
            case Logger::DEBUG:
                $this->chromephp->trace($line);
                break;
            default:
                $this->chromephp->log($line);
                break;
        }
    }

    /**
     * Gets the ChromePhpInterface instance that is used for logging.
     *
     * @return ChromePhpInterface
     */
    public function getChromePhp()
    {
        // Remember: class names in strings are absolute; thus the class_exists
        // here references the canonical name for the ChromePhp class
        if (!$this->chromephp instanceof ChromePhpInterface
            && class_exists('ChromePhp')
        ) {
            $this->setChromePhp(new ChromePhpBridge());
        }
        return $this->chromephp;
    }

    /**
     * Sets the ChromePhpInterface instance that is used for logging.
     *
     * @param  ChromePhpInterface $instance The instance to set.
     * @return ChromePhp
     */
    public function setChromePhp(ChromePhpInterface $instance)
    {
        $this->chromephp = $instance;
        return $this;
    }
}
