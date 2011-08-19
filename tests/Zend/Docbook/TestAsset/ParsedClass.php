<?php

namespace ZendTest\Docbook\TestAsset;

use Zend\Loader\PluginClassLoader;

/**
 * Class parsed by tests to get metadata for docbook generation
 */
class ParsedClass
{
    /**
     * short action1 method description 
     *
     * Long description for action1
     * 
     * @param  string $arg1 
     * @param  bool $arg2 
     * @param  null|array $arg3 
     * @return float
     */
    public function action1($arg1, $arg2, $arg3 = null)
    {}

    /**
     * action2 
     *
     * Long description for action2
     * 
     * @param  null|PluginClassLoader $loader 
     * @return ParsedClass
     */
    public function action2(PluginClassLoader $loader = null)
    {}

    /**
     * A camel-cased method name
     * 
     * @return void
     */
    public function camelCasedMethod()
    {}

    /**
     * A method that the class parser should not aggregate
     * 
     * @return void
     */
    protected function shouldNotAggregate()
    {}
}
