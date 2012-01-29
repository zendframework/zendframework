<?php
namespace Zend\Cli\Prompt;

use Zend\Cli\Prompt,
Zend\Console\Console,
Zend\Console\Adapter as ConsoleAdapter
;

abstract class AbstractPrompt implements Prompt
{
    /**
     * @var \Zend\Console\Adapter
     */
    protected $console;

    /**
     * @var mixed
     */
    protected $lastResponse;

    public function show(){}

    /**
     * Return last answer to this prompt.
     *
     * @return mixed
     */
    public function getLastResponse(){
        return $this->lastResponse;
    }

    /**
     * Return console adapter to use when showing prompt.
     *
     * @return \Zend\Console\Adapter
     */
    public function getConsole(){
        if(!$this->console){
            $this->console = Console::getInstance();
        }

        return $this->console;
    }

    /**
     * Set console adapter to use when showing prompt.
     *
     * @param \Zend\Console\Adapter    $adapter
     */
    public function setConsole(ConsoleAdapter $adapter){
        $this->console = $adapter;
    }

}