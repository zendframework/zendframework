<?php
namespace Zend\Cli\Prompt;

use Zend\Cli\Prompt;

class Number extends Line implements Prompt
{
    /**
     * @var string
     */
    protected $promptText = 'Please enter number: ';

    /**
     * @var bool
     */
    protected $allowFloat = false;

    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    /**
     * Ask the user for a number.
     *
     * @param string    $promptText     The prompt text to display in console
     * @param bool      $allowEmpty     Is empty response allowed?
     * @param bool      $allowFloat     Are floating (non-decimal) numbers allowed?
     * @param integer   $min            Minimum value (inclusive)
     * @param integer   $max            Maximum value (inclusive)
     */
    public function __construct($promptText = null, $allowEmpty = null, $allowFloat = null, $min = null, $max = null)
    {
        if($promptText !== null){
            $this->setPromptText($promptText);
        }

        if($allowEmpty !== null){
            $this->setAllowEmpty($allowEmpty);
        }

        if($min !== null){
            $this->setMin($min);
        }

        if($max !== null){
            $this->setMax($max);
        }

        if($allowFloat !== null){
            $this->setAllowFloat($allowFloat);
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
            $line = trim(stream_get_line($f,$this->maxLength,"\n"));
        }while(!$this->allowEmpty && !$line);

        return $this->lastResponse = $line;
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
     * @param int $maxLength
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
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
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param boolean $allowFloat
     */
    public function setAllowFloat($allowFloat)
    {
        $this->allowFloat = $allowFloat;
    }

    /**
     * @return boolean
     */
    public function getAllowFloat()
    {
        return $this->allowFloat;
    }

}