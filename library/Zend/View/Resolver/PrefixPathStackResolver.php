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
     * @var array
     */
    protected $templatePathStackResolvers;

    /**
     * Constructor
     *
     * @param array $prefixes           Set of prefix and their directories
     * @param bool $lfiProjectionFlag   LFI protection flag
     */
    public function __construct(array $prefixes = array(), $lfiProjectionFlag = true)
    {
        foreach ($prefixes as $prefix => $paths) {
            $this->set($prefix, $paths);
        }
        $this->lfiProtectionOn = (bool) $lfiProjectionFlag;
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
     * Set default file suffix
     *
     * @param  string $defaultSuffix
     * @return self
     */
    public function setDefaultSuffix($defaultSuffix)
    {
        $this->defaultSuffix = (string) $defaultSuffix;

        return $this;
    }

    /**
     * Get default file suffix
     *
     * @return string
     */
    public function getDefaultSuffix()
    {
        return $this->defaultSuffix;
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

            $templatePathStackResolver = $this->getTemplatePathStackResolver($prefix);
            $templatePathStackResolver->setPaths($paths);
            $templatePathStackResolver->setDefaultSuffix($this->getDefaultSuffix());
            $templatePathStackResolver->setLfiProtection($this->isLfiProtectionOn());

            $template = substr($name, strlen($prefix));

            return $templatePathStackResolver->resolve($template, $renderer);
        }
    }
}
