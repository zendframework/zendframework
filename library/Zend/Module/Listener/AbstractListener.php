<?php

namespace Zend\Module\Listener;

abstract class AbstractListener
{
    /**
     * @var ListenerOptions
     */
    protected $options;

    /**
     * __construct
     *
     * @param ListenerOptions $options
     * @return void
     */
    public function __construct(ListenerOptions $options = null)
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
     * @return AbstractListener
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Write a simple array of scalars to a file
     *
     * @param string $filePath
     * @param array $array
     * @return AbstractListener
     */
    protected function writeArrayToFile($filePath, $array)
    {
        $content = "<?php\nreturn " . var_export($array, 1) . ';';
        file_put_contents($filePath, $content);
        return $this;
    }
}
