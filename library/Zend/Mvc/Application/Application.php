<?php

namespace Zend\Mvc\Application;

class Application
{
    const ENVIRONMENT_PRODUCTION = 'production';
    const ENVIRONMENT_DEVELOPMENT = 'development';
    
    protected $environment = self::ENVIRONMENT_DEVELOPMENT;
    protected $bootstrapperClass = 'Application\Bootstrap';
    protected $bootstrapper = null;
    protected $serviceLocator = null;
    protected $runnerClass = null;
    protected $runner = null;
    
    public function __construct($environment = self::ENVIRONMENT_DEVELOPMENT)
    {
        $this->environment = $environment;
    }
    
    public function setBootstrapperClass($bootstrapperClass)
    {
        $this->bootstrapperClass = $bootstrapperClass;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function bootstrap()
    {
        $this->bootstrapper = new $this->bootstrapperClass;
        if (!$this->bootstrapper instanceof BootstrapInterface) {
            throw new \RuntimeException('Bootstrap class must implement Zend\Mvc\Application\BootstrapInterface');
        }
        $this->bootstrapper->bootstrap($this);
    }
    
    public function setRunnerClass($runnerClass)
    {
        $this->runnerClass = $runnerClass;
    }
    
    public function run()
    {
        $runner = new $this->runnerClass;
        
        if (!$runner instanceof RunnerInterface) {
            throw new \RuntimeException('Runner class must implement Zend\Mvc\Application\RunnerInterface');
        }
        
        if ($this->serviceLocator && $runner instanceof ServiceLocatorAwareInterface) {
            $runner->setServiceLocator($this->serviceLocator);
        }
        
        $runner->run($this);
    }
    
}
