<?php
namespace Zend\Cli\Prompt;

use Zend\Cli\Prompt;

class Char extends AbstractPrompt implements Prompt
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
     * Ask the user for a single key stroke
     *
     * @param string  $promptText     The prompt text to display in console
     * @param string  $allowedChars   A list of allowed chars (i.e. "abc12345")
     * @param bool    $ignoreCase     If true, case will be ignored and prompt will always return lower-cased response
     * @param bool    $allowEmpty     Is empty response allowed?
     */
    public function __construct(
        $promptText = 'Please hit a key', $allowedChars = 'abc', $ignoreCase = true, $allowEmpty = false
    ){
        if($promptText !== null){
            $this->setPromptText($promptText);
        }

        if($allowEmpty !== null){
            $this->setAllowEmpty($allowEmpty);
        }

        if($ignoreCase !== null){
            $this->setIgnoreCase($ignoreCase);
        }

        if($allowedChars !== null){
            if($this->ignoreCase){
                $this->setAllowedChars(strtolower($allowedChars));
            }else{
                $this->setAllowedChars($allowedChars);
            }
        }
    }

    /**
     * Show the prompt to user and return the answer.
     *
     * @return mixed
     */
    public function show()
    {
        $f = fopen('php://stdin','r');
        do{
            $this->getConsole()->write($this->promptText);
            $char = fread($f,1);

            /**
             * Lowercase the response if case is irrelevant
             */
            if($this->ignoreCase){
                $char = strtolower($char);
            }

            /**
             * Check if a valid char
             */
            if(stristr($this->allowedChars,$char)){
                echo "\n";
                break;
            }
        }while(true);
        fclose($f);

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

}