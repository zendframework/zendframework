<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace Zend\Console\Prompt;

use Zend\Console\PromptInterface;
use Zend\Console\Console;
use Zend\Console\AdapterInterface as ConsoleAdapter;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage Prompt
 */
abstract class AbstractPrompt implements PromptInterface
{
    /**
     * @var Zend\Console\AdapterInterface
     */
    protected $console;

    /**
     * @var mixed
     */
    protected $lastResponse;

    public function show()
    {

    }

    /**
     * Return last answer to this prompt.
     *
     * @return mixed
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Return console adapter to use when showing prompt.
     *
     * @return \Zend\Console\AdapterInterface
     */
    public function getConsole()
    {
        if (!$this->console) {
            $this->console = Console::getInstance();
        }

        return $this->console;
    }

    /**
     * Set console adapter to use when showing prompt.
     *
     * @param \Zend\Console\AdapterInterface $adapter
     */
    public function setConsole(ConsoleAdapter $adapter)
    {
        $this->console = $adapter;
    }

}
