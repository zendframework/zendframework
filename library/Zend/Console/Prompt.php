<?php
namespace Zend\Console;

use Zend\Console\Adapter as ConsoleAdapter;

interface Prompt {

    /**
     * Show the prompt to user and return the answer.
     *
     * @return mixed
     */
    public function show();

    /**
     * Return last answer to this prompt.
     *
     * @return mixed
     */
    public function getLastResponse();

    /**
     * Return console adapter to use when showing prompt.
     *
     * @return \Zend\Console\Adapter
     */
    public function getConsole();

    /**
     * Set console adapter to use when showing prompt.
     *
     * @param \Zend\Console\Adapter    $adapter
     */
    public function setConsole(ConsoleAdapter $adapter);

}
