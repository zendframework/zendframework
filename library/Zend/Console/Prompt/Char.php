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

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage Prompt
 */
class Char extends AbstractPrompt
{
    /**
     * @var string
     */
    protected $promptText = 'Please select one option ';

    /**
     * @var bool
     */
    protected $allowEmpty = false;

    /**
     * @var string
     */
    protected $allowedChars = 'yn';

    /**
     * @var bool
     */
    protected $ignoreCase = true;

    /**
     * @var bool
     */
    protected $echo = true;

    /**
     * Ask the user for a single key stroke
     *
     * @param string  $promptText     The prompt text to display in console
     * @param string  $allowedChars   A list of allowed chars (i.e. "abc12345")
     * @param bool    $ignoreCase     If true, case will be ignored and prompt will always return lower-cased response
     * @param bool    $allowEmpty     Is empty response allowed?
     * @param bool    $echo           Display the selection after user presses key
     */
    public function __construct(
        $promptText = 'Please hit a key',
        $allowedChars = 'abc',
        $ignoreCase = true,
        $allowEmpty = false,
        $echo = true
    ) {

        if ($promptText !== null) {
            $this->setPromptText($promptText);
        }

        if ($allowEmpty !== null) {
            $this->setAllowEmpty($allowEmpty);
        }

        if ($ignoreCase !== null) {
            $this->setIgnoreCase($ignoreCase);
        }

        if ($allowedChars !== null) {
            if ($this->ignoreCase) {
                $this->setAllowedChars(strtolower($allowedChars));
            } else {
                $this->setAllowedChars($allowedChars);
            }
        }

        if ($echo !== null) {
            $this->setEcho($echo);
        }
    }

    /**
     * Show the prompt to user and return a single char.
     *
     * @return string
     */
    public function show()
    {
        $this->getConsole()->write($this->promptText);
        $mask = $this->getAllowedChars();

        /**
         * Normalize the mask if case is irrelevant
         */
        if ($this->ignoreCase) {
            $mask = strtolower($mask);   // lowercase all
            $mask .= strtoupper($mask);  // uppercase and append
            $mask = str_split($mask);    // convert to array
            $mask = array_unique($mask); // remove duplicates
            $mask = implode("",$mask);   // convert back to string
        }

        do {
            /**
             * Read char from console
             */
            $char = $this->getConsole()->readChar($mask);

            /**
             * Lowercase the response if case is irrelevant
             */
            if ($this->ignoreCase) {
                $char = strtolower($char);
            }

            /**
             * Check if it is an allowed char
             */
            if (stristr($this->allowedChars,$char) !== false) {
                if ($this->echo) {
                    echo trim($char)."\n";
                } else {
                    if ($this->promptText) {
                        echo "\n";  // skip to next line but only if we had any prompt text
                    }
                }
                break;
            }
        } while (true);

        return $this->lastResponse = $char;
    }

    /**
     * @param boolean $allowEmpty
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = $allowEmpty;
    }

    /**
     * @return boolean
     */
    public function getAllowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * @param string $promptText
     */
    public function setPromptText($promptText)
    {
        $this->promptText = $promptText;
    }

    /**
     * @return string
     */
    public function getPromptText()
    {
        return $this->promptText;
    }

    /**
     * @param string $allowedChars
     */
    public function setAllowedChars($allowedChars)
    {
        $this->allowedChars = $allowedChars;
    }

    /**
     * @return string
     */
    public function getAllowedChars()
    {
        return $this->allowedChars;
    }

    /**
     * @param boolean $ignoreCase
     */
    public function setIgnoreCase($ignoreCase)
    {
        $this->ignoreCase = $ignoreCase;
    }

    /**
     * @return boolean
     */
    public function getIgnoreCase()
    {
        return $this->ignoreCase;
    }

    /**
     * @param boolean $echo
     */
    public function setEcho($echo)
    {
        $this->echo = $echo;
    }

    /**
     * @return boolean
     */
    public function getEcho()
    {
        return $this->echo;
    }

}
