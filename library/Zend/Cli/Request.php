<?php

namespace Zend\Cli;

use Zend\Stdlib\RequestDescription,
    Zend\Stdlib\Message,
    Zend\Stdlib\ParametersDescription,
    Zend\Stdlib\Parameters
;

class Request extends Message implements RequestDescription
{
    /**
     * @var \Zend\Stdlib\ParametersDescription
     */
    protected $params = null;

    /**
     * @var \Zend\Stdlib\ParametersDescription
     */
    protected $envParams = null;

    /**
     * Create a new CLI request
     *
     * @param array|null $args     Cli arguments. If not supplied, $_SERVER['argv'] will be used
     */
    public function __construct(array $args = null, array $env = null)
    {
        if($args === null){
            if (!isset($_SERVER['argv'])) {
                $errorDescription = (ini_get('register_argc_argv') == false)
                    ? "Cannot create Cli\\Request because PHP ini option 'register_argc_argv' is set Off"
                    : 'Cannot create Cli\\Request because $_SERVER["argv"] is not set for unknown reason.';
                throw new Exception\RuntimeException($errorDescription);
            }
            $args = $_SERVER['argv'];
        }

        if($env === null){
            $env = $_ENV;
        }

        $this->params()->fromArray($args);
        $this->env()->fromArray($env);
    }

    /**
     * Exchange parameters object
     *
     * @param \Zend\Stdlib\ParametersDescription $params
     * @return Request
     */
    public function setParams(ParametersDescription $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Return the container responsible for parameters
     *
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function getParams()
    {
        if ($this->params === null) {
            $this->params = new Parameters();
        }

        return $this->params;
    }

    /**
     * Return a single parameter.
     * Shortcut for $request->params()->get()
     *
     * @param string    $name       Parameter name
     * @param string    $default    (optional) default value in case the parameter does not exist
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        return $this->params()->get($name, $default);
    }

    /**
     * Return the container responsible for parameters
     *
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function params()
    {
        if ($this->params === null) {
            $this->params = new Parameters();
        }

        return $this->params;
    }

    /**
     * Provide an alternate Parameter Container implementation for env parameters in this object, (this is NOT the
     * primary API for value setting, for that see env())
     *
     * @param \Zend\Stdlib\ParametersDescription $env
     * @return \Zend\Cli\Request
     */
    public function setEnv(ParametersDescription $env)
    {
        $this->envParams = $env;
        return $this;
    }

    /**
     * Return the parameter container responsible for env parameters
     *
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function env()
    {
        if ($this->envParams === null) {
            $this->envParams = new Parameters();
        }

        return $this->envParams;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return trim(implode(' ',$this->params()->toArray()));
    }

    /**
     * Allow PHP casting of this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

}
