<?php
namespace Zend\Di;

class Configuration
{
    /**
     * @var DependencyInjection
     */
    protected $injector;

    /**
     * Constructor
     *
     * Sets internal injector property
     * 
     * @param  DependencyInjection $injector 
     * @return void
     */
    public function __construct(DependencyInjection $injector)
    {
        $this->injector = $injector;
    }

    /**
     * Update injector based on array configuration
     * 
     * @param  array $config 
     * @return void
     */
    public function fromArray(array $config)
    {
        if (isset($config['definitions']) && is_array($config['definitions'])) {
            $this->buildDefinitions($config['definitions']);
        }
        if (isset($config['aliases']) && is_array($config['aliases'])) {
            $this->buildAliases($config['aliases']);
        }
    }

    /**
     * Load definitions from a configuration object
     *
     * Accepts an array, an object with a toArray() method, or a Traversable 
     * object.
     * 
     * @param  array|object $config 
     * @return void
     */
    public function fromConfig($config)
    {
        if (!is_object($config)) {
            if (is_array($config)) {
                return $this->fromArray($config);
            }
            throw new Exception\InvalidArgumentException(sprintf(
                'Configuration must be provided as either an array or Traversable object; "%s" was provided',
                gettype($config)
            ));
        }
        if (method_exists($config, 'toArray')) {
            return $this->fromArray($config->toArray());
        }
        if (!$config instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Configuration must be provided as either an array or Traversable object; "%s" was provided',
                get_class($config)
            ));
        }
        $array = array();
        foreach ($config as $key => $value) {
            $array[$key] = $value;
        }
        return $this->fromArray($array);
    }

    /**
     * Create definitions and inject into dependency injector
     *
     * @param  array $definitions 
     * @return void
     */
    protected function buildDefinitions(array $definitions)
    {
        foreach ($definitions as $definition) {
            $this->buildDefinition($definition);
        }
    }

    /**
     * Build a definition to add to the injector
     * 
     * @param  array $values 
     * @return void
     */
    protected function buildDefinition(array $values)
    {
        if (!isset($values['class']) 
            || !is_string($values['class']) 
            || empty($values['class'])
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Cannot create definition; provided definition contains no class key (%s)',
                var_export($values, 1)
            ));
        }

        $definition = new Definition($values['class']);

        foreach ($values as $key => $value) {
            switch (strtolower($key)) {
                case 'class':
                    break;
                case 'constructor_callback':
                    $callback = $value;
                    if (is_array($value) 
                        && (isset($value['class']) && isset($value['method']))
                    ) {
                        $callback = array($value['class'], $value['method']);
                    }
                    $definition->setConstructorCallback($callback);
                    break;
                case 'params':
                    if (!is_array($value)) {
                        break;
                    }
                    $params = $this->resolveReferences($value);
                    $definition->setParams($params);
                    break;
                case 'param_map':
                    $definition->setParamMap($value);
                    break;
                case 'shared':
                    $definition->setShared((bool) $value);
                    break;
                case 'methods':
                    $this->buildMethods($definition, $value);
                    break;
                default:
                    // ignore all other options
                    break;
            }
        }

        $this->injector->setDefinition($definition);
    }

    /**
     * Build injectible methods for a definition
     * 
     * @param  DependencyDefinition $definition 
     * @param  array $methods 
     * @return void
     */
    protected function buildMethods(DependencyDefinition $definition, array $methods)
    {
        foreach ($methods as $methodDefinition) {
            if (!is_array($methodDefinition)) {
                continue;
            }
            if (!isset($methodDefinition['name'])) {
                continue;
            }
            $method = $methodDefinition['name'];
            $params   = array();
            if (isset($methodDefinition['params']) && is_array($methodDefinition['params'])) {
                $params = $this->resolveReferences($methodDefinition['params']);
            }
            $definition->addMethodCall($method, $params);
        }
    }

    /**
     * Resolve parameters that are references
     *
     * If a parameter value is an array containing a key "__reference", replace
     * it with a Reference object that is seeded with the value of that key.
     * 
     * @param  array $params 
     * @return array
     */
    protected function resolveReferences(array $params)
    {
        foreach ($params as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            if (!isset($value['__reference'])) {
                continue;
            }
            $params[$key] = new Reference($value['__reference']);
        }
        return $params;
    }

    /**
     * Build aliases from provided configuration
     *
     * $aliases should be array of $alias => $target pairs.
     * 
     * @param  array $aliases 
     * @return void
     */
    protected function buildAliases(array $aliases)
    {
        foreach ($aliases as $alias => $target) {
            $this->injector->setAlias($alias, $target);
        }
    }
}
