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
    const DEFAULT_SUFFIX = 'phtml';

    /**
     * Array containing prefix as key and "template path stack array" as value
     *
     * @var array
     */
    protected $prefixes = array();

    /**
     * Default suffix to use
     *
     * Appends this suffix if the template requested does not use it.
     *
     * @var string
     */
    protected $defaultSuffix = 'phtml';

    /**
     * Flag indicating whether or not LFI protection for rendering view scripts is enabled
     * @var bool
     */
    protected $lfiProtectionOn = true;

    /**
     * Array containing prefix as key and TemplatePathStack as value
     *
     * @var ResolverInterface[]
     */
    protected $templatePathStackResolvers;

    /**
     * Constructor
     *
     * @param array               $prefixes          Set of prefix and their directories
     * @param bool                $lfiProjectionFlag LFI protection flag
     * @param string              $defaultSuffix     Default file suffix to use when looking up view scripts
     * @param ResolverInterface[] $resolvers         Resolvers to use for particular prefixes, indexed by prefix
     */
    public function __construct(
        array $prefixes = array(),
        $lfiProjectionFlag = true,
        $defaultSuffix = self::DEFAULT_SUFFIX,
        array $resolvers = array()
    ) {
        foreach ($prefixes as $prefix => $paths) {
            $this->set($prefix, $paths);
        }

        $this->lfiProtectionOn = (bool) $lfiProjectionFlag;
        $this->defaultSuffix   = (string) $defaultSuffix;
        $this->templatePathStackResolvers = $resolvers;
    }

    /**
     * Return status of LFI protection flag
     *
     * @return bool
     */
    public function isLfiProtectionOn()
    {
        return $this->lfiProtectionOn;
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
        $this->templatePathStackResolvers[$prefix] = $resolver;

        return $this;
    }

    /**
     * Get template path stack resolver for a prefix
     *
     * @return TemplatePathStack
     */
    public function getTemplatePathStackResolver($prefix)
    {
        if (!isset($this->templatePathStackResolvers[$prefix])) {
            if (!isset($this->prefixes[$prefix])) {
                throw new Exception\InvalidArgumentException(sprintf('Prefix %s does not exists.', $prefix));
            }
            $this->templatePathStackResolvers[$prefix] = new TemplatePathStack;
        }

        return $this->templatePathStackResolvers[$prefix];
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

            $resolver = & $this->templatePathStackResolvers[$prefix];

            if (! isset($resolver)) {
                $resolver = $this->getTemplatePathStackResolver($prefix);

                $resolver->setPaths($paths);
                $resolver->setDefaultSuffix($this->defaultSuffix);
                $resolver->setLfiProtection($this->isLfiProtectionOn());
            }

            if ($result = $resolver->resolve(substr($name, strlen($prefix)), $renderer)) {
                return $result;
            }
        }
    }
}
