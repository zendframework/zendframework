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

use FB;
use FirePhp as FirePhpInstance;
use Zend\Log\Formatter\FirePhp as FirePhpFormatter;
use Zend\Log\Logger;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 */
class FirePhp extends AbstractWriter
{
    /**
     * The instance of FirePhp that is used to log messages to.
     * 
     * @var FB
     */
    private $firephp;

    /**
     * Initializes a new instance of this class.
     */
    public function __construct(FirePhpInstance $instance = null)
    {
        if ($instance === null) {
            $this->firephp = FirePhpInstance::getInstance(true);
        } else {
            $this->firephp = $instance;
        }
        
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
        if (!$this->firephp->getEnabled()) {
            return;
        }

        $line = $this->formatter->format($event);

        switch ($event['priority']) {
            case Logger::EMERG:
            case Logger::ALERT:
            case Logger::CRIT:
            case Logger::ERR:
                $this->firephp->error($line);
                break;
            case Logger::WARN:
                $this->firephp->warn($line);
                break;
            case Logger::NOTICE:
            case Logger::INFO:
                $this->firephp->info($line);
                break;
            case Logger::DEBUG:
                $this->firephp->trace($line);
                break;
            default:
                $this->firephp->log($line);
                break;
        }
    }

    /**
     * Gets the FirePhp instance that is used for logging.
     * 
     * @return FirePhpInstance
     */
    public function getFirePhp()
    {
        return $this->firephp;
    }

    /**
     * Sets the FirePhp instance that is used for logging.
     * 
     * @param FirePhpInstance $instance The FirePhp instance to set.
     * @return FirePhp
     */
    public function setFirePhp(FirePhpInstance $instance)
    {
        $this->firephp = $instance;
        return $this;
    }

}
