<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Console\Prompt;

class Password extends AbstractPrompt
{
    /**
     * @var string
     */
    protected $promptText = 'Password: ';

    /**
     * @var bool
     */
    protected $echo = true;

    /**
     * @var string
     */
    protected $password = '';

    /**
     * Ask the user for a password
     *
     * @param string $promptText   The prompt text to display in console
     * @param bool   $echo         Display the selection after user presses key
     */
    public function __construct(
        $promptText = 'Password: ',
        $echo = true
    ) {

        $this->setPromptText($promptText);
        $this->setEcho($echo);
    }

    /**
     * Show the prompt to user and return a string.
     *
     * @return string
     */
    public function show()
    {
        $this->getConsole()->write($this->promptText);

        /**
         * Read characters from console
         */
        while (true) {
            $char = $this->getConsole()->readChar();

            if (PHP_EOL == $char) {
                break;
            }

            if ($this->echo) {
                echo "*";
            }
            $this->password .= $char;
        }

        return $this->password;
    }

    /**
     * @param string $promptText
     */
    public function setPromptText($promptText)
    {
        $this->promptText = (string) $promptText;
    }

    /**
     * @return string
     */
    public function getPromptText()
    {
        return $this->promptText;
    }

    /**
     * @param bool $echo
     */
    public function setEcho($echo)
    {
        $this->echo = (bool) $echo;
    }

    /**
     * @return bool
     */
    public function getEcho()
    {
        return $this->echo;
    }
}
