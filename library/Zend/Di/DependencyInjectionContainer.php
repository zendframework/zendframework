<?php
namespace Zend\Di;

class DependencyInjectionContainer 
    extends ServiceLocator 
    implements DependencyEnabled
{
    /**
     * DI manager
     * @var DependencyInjection
     */
    protected $injector;

    /**
     * Set DI manager for this service locator
     * 
     * @param  DependencyInjection $di 
     * @return DependencyInjectionContainer
     */
    public function setInjector(DependencyInjection $di)
    {
        $this->injector = $di;
        return $this;
    }

    /**
     * Get DI manager for this service locator
     *
     * If none has been injected, injects an instance of DependencyInjector.
     * 
     * @return DependencyInjection
     */
    public function getInjector()
    {
        if (null === $this->injector) {
            $this->setInjector(new DependencyInjector());
        }
        return $this->injector;
    }

    /**
     * Retrieve a registered service
     *
     * Attempts to retrieve a registered service. If none matching is found, it
     * then looks in the dependency injector to see if it can find it; if so,
     * it returns it.
     * 
     * @param  string $name 
     * @param  array $params 
     * @return mixed
     */
    public function get($name, array $params = array())
    {
        $service = parent::get($name, $params);
        if (null !== $service) {
            return $service;
        }

        return $this->getInjector()->get($name, $params);
    }
}
