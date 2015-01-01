<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Console\Prompt;

use Zend\Console\Exception;

class Checkbox extends Char
{

    /**
     * @var string
     */
    protected $promptText = 'Please select an option (Enter to finish) ';

    /**
     * @var bool
     */
    protected $ignoreCase = true;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Ask the user to select any number of pre-defined options
     *
     * @param string    $promptText     The prompt text to display in console
     * @param array     $options        Allowed options
     * @param bool      $allowEmpty     Allow empty (no) selection?
     * @param bool      $echo           True to display selected option?
     * @throws Exception\BadMethodCallException if no options available
     */
    public function __construct($promptText = 'Please select one option (Enter to finish) ', $options = array(), $allowEmpty = false, $echo = false)
    {
        if ($promptText !== null) {
            $this->setPromptText($promptText);
        }

        if (! count($options)) {
            throw new Exception\BadMethodCallException('Cannot construct a "checkbox" prompt without any options');
        }

        $this->setOptions($options);

        if ($allowEmpty !== null) {
            $this->setAllowEmpty($allowEmpty);
        }

        if ($echo !== null) {
            $this->setEcho($echo);
        }
    }

    /**
     * Show a list of options and prompt the user to select any number of them.
     *
     * @return array       Checked options
     */
    public function show()
    {
        $console = $this->getConsole();
        $checked = array();
        do {
            // Show prompt text and available options
            $console->writeLine($this->promptText);
            foreach ($this->options as $k => $v) {
                $console->writeLine('  ' . $k . ') ' . (in_array($v, $checked) ? '[X] ' : '[ ] ') . $v);
            }

            // Prepare mask
            $mask = implode("", array_keys($this->options));
            $mask .= "\r\n";

            // Prepare other params for parent class
            $this->setAllowedChars($mask);
            $oldPrompt = $this->promptText;
            $oldEcho = $this->echo;
            $this->echo = false;
            $this->promptText = null;

            // Retrieve a single character
            $response = parent::show();

            // Restore old params
            $this->promptText = $oldPrompt;
            $this->echo = $oldEcho;

            // Display selected option if echo is enabled
            if ($this->echo) {
                if (isset($this->options[$response])) {
                    $console->writeLine($this->options[$response]);
                } else {
                    $console->writeLine();
                }
            }

            if ($response != "\r" && $response != "\n" && isset($this->options[$response])) {
                $pos = array_search($this->options[$response], $checked);
                if ($pos === false) {
                    $checked[] = $this->options[$response];
                } else {
                    array_splice($checked, $pos, 1);
                }
            }
        } while ($response != "\r" && $response != "\n");

        $this->lastResponse = $checked;
        return $checked;
    }

    /**
     * Set allowed options
     *
     * @param array|\Traversable $options
     * @throws Exception\BadMethodCallException
     */
    public function setOptions($options)
    {
        if (! is_array($options) && ! $options instanceof \Traversable) {
            throw new Exception\BadMethodCallException('Please specify an array or Traversable object as options');
        }

        if (! is_array($options)) {
            $this->options = array();
            foreach ($options as $k => $v) {
                $this->options[$k] = $v;
            }
        } else {
            $this->options = $options;
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
