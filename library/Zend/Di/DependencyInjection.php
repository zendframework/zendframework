<?php
namespace Zend\Di;

interface DependencyInjection
{
    /**
     * Lazy-load a class
     *
     * Attempts to load the class (or service alias) provided. If it has been 
     * loaded before, the previous instance will be returned (unless the service
     * definition indicates shared instances should not be used).
     * 
     * @param  string $name Class name or service alias
     * @param  null|array $params Parameters to pass to the constructor
     * @return object|null
     */
    public function get($name, array $params = null);

    /**
     * Retrieve a new instance of a class
     *
     * Forces retrieval of a discrete instance of the given class, using the
     * constructor parameters provided.
     * 
     * @param  mixed $name Class name or service alias
     * @param  array $params Parameters to pass to the constructor
     * @return object|null
     */
    public function newInstance($name, array $params = null);
    
    /**
     * @param  array|Traversable $definitions Iterable Definition objects
     */
    public function setDefinitions($definitions);
    
    public function setDefinition(DependencyDefinition $definition, $serviceName = null);
    public function setAlias($alias, $serviceName);

    /**@+
     * Methods for introspection; used for building locators from DI definitions
     */
    public function getDefinitions();
    public function getAliases();
    /**@-*/
}
