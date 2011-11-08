<?php

namespace Zend\Module\Listener;

class AbstractListener
{
    /**
     * @var ListenerOptions
     */
    protected $options;

    public function __construct($options = null)
    {
        if (null === $options) {
            $this->setOptions(new ListenerOptions);
        } else {
            $this->setOptions($options);
        }
    }
 
    /**
     * Get options.
     *
     * @return options
     */
    public function getOptions()
    {
        return $this->options;
    }
 
    /**
     * Set options.
     *
     * @param $options the value to be set
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }
}
