<?php
namespace Zend\Console;

use Zend\Console\AdapterInterface as ConsoleAdapter;

interface PromptInterface {

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
     * @return \Zend\Console\AdapterInterface
     */
    public function getConsole();

    /**
     * Set console adapter to use when showing prompt.
     *
     * @param \Zend\Console\AdapterInterface    $adapter
     */
    public function setConsole(ConsoleAdapter $adapter);

}
