<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Resolver;

use Zend\View\Exception;
use Zend\View\Renderer\RendererInterface as Renderer;

class PrefixPathStackResolver implements ResolverInterface
{
    /**
     * Array containing prefix as key and "template path stack array" as value
     *
     * @var array
     */
    protected $prefixes = array();

    /**
     * Array containing prefix as key and TemplatePathStack as value
     *
     * @var ResolverInterface[]
     */
    protected $resolversByPrefix;

    /**
     * Constructor
     *
     * @param array               $prefixes          Set of prefix and their directories
     * @param ResolverInterface[] $resolvers         Resolvers to use for particular prefixes, indexed by prefix
     */
    public function __construct(
        array $prefixes = array(),
        array $resolvers = array()
    ) {
        $this->prefixes = $prefixes;

        $this->resolversByPrefix = $resolvers;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($name, Renderer $renderer = null)
    {
        foreach ($this->prefixes as $prefix => $paths) {
            if (strpos($name, $prefix) !== 0) {
                continue;
            }

            if (! isset($this->resolversByPrefix[$prefix])) {
                $resolver = new TemplatePathStack();

                $resolver->setPaths(
                    is_string($this->prefixes[$prefix]) ? (array) $this->prefixes[$prefix] : $this->prefixes[$prefix]
                );

                $this->resolversByPrefix[$prefix] = $resolver;
            }

            if ($result = $this->resolversByPrefix[$prefix]->resolve(substr($name, strlen($prefix)), $renderer)) {
                return $result;
            }
        }
    }
}
