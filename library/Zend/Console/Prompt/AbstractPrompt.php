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
use Zend\Console\Exception\BadMethodCallException;

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

    abstract public function show();

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

    /**
     * Create an instance of this prompt, show it and return response.
     * 
     * This is a convenience method for creating statically creating prompts, i.e.:
     *
     *      $name = Zend\Console\Prompt\Line::prompt("Enter your name: ");
     *
     * @throws \Zend\Console\Exception\BadMethodCallException
     * @return mixed
     */
    public static function prompt(){
        if (get_called_class() === __CLASS__) {
            throw new BadMethodCallException(
                'Cannot call prompt() on AbstractPrompt class. Use one of Console\Prompt\ subclasses.'
            );
        }

        $refl     = new \ReflectionClass(get_called_class());
        $instance = $refl->newInstanceArgs(func_get_args());
        return $instance->show();
    }

}
