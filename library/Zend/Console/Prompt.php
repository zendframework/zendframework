<?php
namespace Zend\Console;

use Zend\Console\Adapter as ConsoleAdapter;

interface Prompt {

    /**
     * Show the prompt to user and return the answer.
     *
     * @abstract
     * @return mixed
     */
    public function show();

    /**
     * Return last answer to this prompt.
     *
     * @abstract
     * @return mixed
     */
    public function getLastResponse();

    /**
     * Return console adapter to use when showing prompt.
     *
     * @abstract
     * @return \Zend\Console\Adapter
     */
    public function getConsole();

    /**
     * Set console adapter to use when showing prompt.
     *
     * @abstract
     * @param \Zend\Console\Adapter    $adapter
     */
    public function setConsole(ConsoleAdapter $adapter);

}
