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
        foreach ($prefixes as $prefix => $paths) {
            $this->set($prefix, $paths);
        }

        $this->resolversByPrefix = $resolvers;
    }

    /**
     * Registers a set of directories for a given prefix,
     * replacing any others previously set for this prefix.
     *
     * @param string       $prefix The prefix
     * @param array|string $paths  The base directories
     */
    public function set($prefix, $paths)
    {
        $this->prefixes[$prefix] = (array) $paths;
    }

    /**
     * Registers a set of directories for a given prefix, either
     * appending or prepending to the ones previously set for this prefix.
     *
     * @param string       $prefix  The prefix
     * @param array|string $paths   Directories
     * @param bool         $prepend Whether to prepend the directories
     */
    public function add($prefix, $paths, $prepend = false)
    {
        // to avoid merge error
        if (!isset($this->prefixes[$prefix])) {
            $this->set($prefix, $paths);
            return ;
        }

        if ($prepend) {
            $this->prefixes[$prefix] = array_merge(
                (array) $paths,
                $this->prefixes[$prefix]
            );
        } else {
            $this->prefixes[$prefix] = array_merge(
                $this->prefixes[$prefix],
                (array) $paths
            );
        }
    }

    /**
     * Set template path stack resolver for a prefix
     *
     * @param  string            $prefix
     * @param  TemplatePathStack $resolver
     * @return self
     */
    public function setTemplatePathStackResolver($prefix, TemplatePathStack $resolver)
    {
        $this->resolversByPrefix[$prefix] = $resolver;

        return $this;
    }

    /**
     * Get template path stack resolver for a prefix
     *
     * @return TemplatePathStack
     */
    public function getTemplatePathStackResolver($prefix)
    {
        if (!isset($this->resolversByPrefix[$prefix])) {
            if (!isset($this->prefixes[$prefix])) {
                throw new Exception\InvalidArgumentException(sprintf('Prefix %s does not exists.', $prefix));
            }
            $this->resolversByPrefix[$prefix] = new TemplatePathStack;
        }

        return $this->resolversByPrefix[$prefix];
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

            $resolver = & $this->resolversByPrefix[$prefix];

            if (! isset($resolver)) {
                $resolver = $this->getTemplatePathStackResolver($prefix);

                $resolver->setPaths($paths);
            }

            if ($result = $resolver->resolve(substr($name, strlen($prefix)), $renderer)) {
                return $result;
            }
        }
    }
}
