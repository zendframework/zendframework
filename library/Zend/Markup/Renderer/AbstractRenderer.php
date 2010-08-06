<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer;

use Zend\Markup\Parser,
    Zend\Markup\Token,
    Zend\Filter;

/**
 * Defines the basic rendering functionality
 *
 * @uses       \Zend\Filter\FilterChain
 * @uses       \Zend\Markup\Renderer\Exception
 * @uses       \Zend\Markup\Renderer\TokenConverter
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractRenderer
{
    const TYPE_CALLBACK = 4;
    const TYPE_REPLACE  = 8;
    const TYPE_ALIAS    = 16;

    /**
     * Tag info
     *
     * @var array
     */
    protected $_markups = array();

    /**
     * Parser
     *
     * @var \Zend\Markup\Parser
     */
    protected $_parser;

    /**
     * What filter to use
     *
     * @var bool
     */
    protected $_filter;

    /**
     * Filter chain
     *
     * @var \Zend\Filter\FilterChain
     */
    protected $_defaultFilter;

    /**
     * The current group
     *
     * @var string
     */
    protected $_group;

    /**
     * Groups definition
     *
     * @var array
     */
    protected $_groups = array();

    /**
     * Plugin loader for tags
     *
     * @var \Zend\Loader\PrefixPathMapper
     */
    protected $_pluginLoader;

    /**
     * The current token
     *
     * @var \Zend\Markup\Token
     */
    protected $_token;

    /**
     * Encoding
     *
     * @var string
     */
    protected static $_encoding = 'UTF-8';


    /**
     * Constructor
     *
     * @param array|\Zend\Config\Config $options
     *
     * @todo make constructor compliant with new configuration standards
     *
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        if (isset($options['encoding'])) {
            $this->setEncoding($options['encoding']);
        }
        if (isset($options['parser'])) {
            $this->setParser($options['parser']);
        }
        if (!isset($options['useDefaultFilters']) || ($options['useDefaultFilters'] === true)) {
            $this->addDefaultFilters();
        }
        if (isset($options['defaultFilter'])) {
            $this->addDefaultFilter($options['defaultFilter']);
        }
    }

    /**
     * Set the parser
     *
     * @param  \Zend\Markup\Parser $parser
     * @return \Zend\Markup\AbstractRenderer
     */
    public function setParser(Parser $parser)
    {
        $this->_parser = $parser;
        return $this;
    }

    /**
     * Get the parser
     *
     * @return \Zend\Markup\Parser
     */
    public function getParser()
    {
        return $this->_parser;
    }

    /**
     * Get the plugin loader
     *
     * @return \Zend\Loader\PrefixPathMapper
     */
    public function getPluginLoader()
    {
        return $this->_pluginLoader;
    }

    /**
     * Set the renderer's encoding
     *
     * @param string $encoding
     *
     * @return \Zend\Markup\AbstractRenderer
     */
    public static function setEncoding($encoding)
    {
        self::$_encoding = $encoding;
    }

    /**
     * Get the renderer's encoding
     *
     * @return string
     */
    public static function getEncoding()
    {
        return self::$_encoding;
    }

    /**
     * Add a new markup
     *
     * @param string $name
     * @param string $type
     * @param array $options
     *
     * @return \Zend\Markup\AbstractRenderer
     */
    public function addMarkup($name, $type, array $options)
    {
        if (!isset($options['group']) && ($type ^ self::TYPE_ALIAS)) {
            throw new Exception("There is no render group defined.");
        }

        // add the filter
        if (isset($options['filter'])) {
            if ($options['filter'] instanceof Filter\Filter) {
                $filter = $options['filter'];
            } elseif ($options['filter'] === true) {
                $filter = $this->getDefaultFilter();
            } else {
                $filter = false;
            }
        } else {
            $filter = $this->getDefaultFilter();
        }

        // check the type
        if ($type & self::TYPE_CALLBACK) {
            // add a callback tag
            if (isset($options['callback'])) {
                if (!($options['callback'] instanceof TokenConverter)) {
                    throw new Exception("Not a valid markup callback.");
                }
                if (method_exists($options['callback'], 'setRenderer')) {
                    $options['callback']->setRenderer($this);
                }
            } else {
                $options['callback'] = null;
            }

            $options['type'] = $type;
            $options['filter'] = $filter;

            $this->_markups[$name] = $options;
        } elseif ($type & self::TYPE_ALIAS) {
            // add an alias
            if (empty($options['name'])) {
                throw new Exception(
                        'No alias was provided but markup was defined as such');
            }

            $this->_markups[$name] = array(
                'type' => self::TYPE_ALIAS,
                'name' => $options['name']
            );
        } else {
            if ($type && array_key_exists('empty', $options) && $options['empty']) {
                // add a single replace markup
                $options['type']   = $type;
                $options['filter'] = $filter;

                $this->_markups[$name] = $options;
            } else {
                // add a replace markup
                $options['type']   = $type;
                $options['filter'] = $filter;

                $this->_markups[$name] = $options;
            }
        }
        return $this;
    }

    /**
     * Remove a markup
     *
     * @param string $name
     *
     * @return void
     */
    public function removeMarkup($name)
    {
        unset($this->_markups[$name]);
    }

    /**
     * Remove the default tags
     *
     * @return void
     */
    public function clearMarkups()
    {
        $this->_markups = array();
    }

    /**
     * Render function
     *
     * @param  \Zend\Markup\TokenList|string $tokenList
     *
     * @return string
     */
    public function render($value)
    {
        if ($value instanceof Markup\TokenList) {
            $tokenList = $value;
        } else {
            $tokenList = $this->getParser()->parse($value);
        }

        $root = $tokenList->current();

        $this->_filter = $this->getDefaultFilter();

        return $this->_render($root);
    }

    /**
     * Render a single token
     *
     * @param  \Zend\Markup\Token $token
     * @return string
     */
    protected function _render(Token $token)
    {
        $return    = '';

        $this->_token = $token;

        // if this tag has children, execute them
        if ($token->hasChildren()) {
            foreach ($token->getChildren() as $child) {
                $return .= $this->_execute($child);
            }
        }

        return $return;
    }

    /**
     * Get the group of a token
     *
     * @param  \Zend\Markup\Token $token
     * @return string|bool
     */
    protected function _getGroup(Token $token)
    {
        if (!isset($this->_markups[$token->getName()])) {
            return false;
        }

        $tag = $this->_markups[$token->getName()];

        // alias processing
        while ($tag['type'] & self::TYPE_ALIAS) {
            $tag = $this->_markups[$tag['name']];
        }

        return isset($tag['group']) ? $tag['group'] : false;
    }

    /**
     * Execute the token
     *
     * @param  \Zend\Markup\Token $token
     * @return string
     */
    protected function _execute(Token $token)
    {
        // first return the normal text tags
        if ($token->getType() == Token::TYPE_NONE) {
            return $this->_filter($token->getTag());
        }

        // if the token doesn't have a notation, return the plain text
        if (!isset($this->_markups[$token->getName()])) {
            $oldToken  = $this->_token;
            $return = $this->_filter($token->getTag()) . $this->_render($token) . $token->getStopper();
            $this->_token = $oldToken;
            return $return;
        }

        $name   = $this->_getMarkupName($token);
        $markup = (!$name) ? false : $this->_markups[$name];
        $empty  = (is_array($markup) && array_key_exists('empty', $markup) && $markup['empty']);

        // check if the tag has content
        if (!$empty && !$token->hasChildren()) {
            return '';
        }

        // check for the context
        if (is_array($markup) && !in_array($markup['group'], $this->_groups[$this->_group])) {
            $oldToken = $this->_token;
            $return   = $this->_filter($token->getTag()) . $this->_render($token) . $token->getStopper();
            $this->_token = $oldToken;
            return $return;
        }

        // check for the filter
        if (!isset($markup['filter'])
            || (!($markup['filter'] instanceof Filter\Filter) && ($markup['filter'] !== false))) {
            $this->_markups[$name]['filter'] = $this->getDefaultFilter();
        }

        // save old values to reset them after the work is done
        $oldFilter = $this->_filter;
        $oldGroup  = $this->_group;

        $return = '';

        // set the filter and the group
        $this->_filter = $this->getFilter($name);

        if ($group = $this->_getGroup($token)) {
            $this->_group = $group;
        }

        // callback
        if (is_array($markup) && ($markup['type'] & self::TYPE_CALLBACK)) {
            // load the callback if the tag doesn't exist
            if (!($markup['callback'] instanceof TokenConverter)) {
                $class = $this->getPluginLoader()->load($name);

                $markup['callback'] = new $class;

                if (method_exists($markup['callback'], 'setRenderer')) {
                    $markup['callback']->setRenderer($this);
                }
            }
            if ($markup['type'] && !$empty) {
                $return = $markup['callback']->__invoke($token, $this->_render($token));
            } else {
                $return = $markup['callback']->__invoke($token, null);
            }
        } else {
            // replace
            if ($markup['type'] && !$empty) {
                $return = $this->_executeReplace($token, $markup);
            } else {
                $return = $this->_executeSingleReplace($token, $markup);
            }
        }

        // reset to the old values
        $this->_filter = $oldFilter;
        $this->_group  = $oldGroup;

        return $return;
    }

    /**
     * Filter method
     *
     * @param string $value
     *
     * @return string
     */
    protected function _filter($value)
    {
        if ($this->_filter instanceof Filter\Filter) {
            return $this->_filter->filter($value);
        }
        return $value;
    }

    /**
     * Get the markup name
     *
     * @param \Zend\Markup\Token
     *
     * @return string
     */
    protected function _getMarkupName(Token $token)
    {
        $name = $token->getName();
        if (empty($name)) {
            return false;
        }

        return $this->_resolveMarkupName($name);
    }

    /**
     * Resolve aliases for a markup name
     *
     * @param string $name
     *
     * @return string
     */
    protected function _resolveMarkupName($name)
    {
        while (($type = $this->_getMarkupType($name))
               && ($type & self::TYPE_ALIAS)
        ) {
            $name = $this->_markups[$name]['name'];
        }

        return $name;
    }

    /**
     * Retrieve markup type
     *
     * @param  string $name
     * @return false|int
     */
    protected function _getMarkupType($name)
    {
        if (!isset($this->_markups[$name])) {
            return false;
        }
        if (!isset($this->_markups[$name]['type'])) {
            return false;
        }
        return $this->_markups[$name]['type'];
    }

    /**
     * Execute a replace token
     *
     * @param  \Zend\Markup\Token $token
     * @param  array $tag
     * @return string
     */
    protected function _executeReplace(Token $token, $tag)
    {
        return $tag['start'] . $this->_render($token) . $tag['end'];
    }

    /**
     * Execute a single replace token
     *
     * @param  \Zend\Markup\Token $token
     * @param  array $tag
     * @return string
     */
    protected function _executeSingleReplace(Token $token, $tag)
    {
        return $tag['replace'];
    }

    /**
     * Get the default filter
     *
     * @return void
     */
    public function getDefaultFilter()
    {
        if (null === $this->_defaultFilter) {
            $this->addDefaultFilters();
        }

        return $this->_defaultFilter;
    }

    /**
     * Add a default filter
     *
     * @param string $filter
     *
     * @return void
     */
    public function addDefaultFilter(Filter\Filter $filter, $placement = Filter\FilterChain::CHAIN_APPEND)
    {
        if (!($this->_defaultFilter instanceof Filter\FilterChain)) {
            $defaultFilter = new Filter\FilterChain();
            $defaultFilter->addFilter($filter);
            $this->_defaultFilter = $defaultFilter;
        }

        $this->_defaultFilter->addFilter($filter, $placement);
    }

    /**
     * Set the default filter
     *
     * @param \Zend\Filter\Filter $filter
     *
     * @return void
     */
    public function setDefaultFilter(Filter\Filter $filter)
    {
        $this->_defaultFilter = $filter;
    }

    /**
     * Get the filter for an existing markup
     *
     * @param string $markup
     *
     * @return \Zend\Filter\Filter
     */
    public function getFilter($markup)
    {
        $markup = $this->_resolveMarkupName($markup);

        if (!isset($this->_markups[$markup]['filter'])
            || !($this->_markups[$markup]['filter'] instanceof Filter\Filter)
        ) {
            if (isset($this->_markups[$markup]['filter']) && $this->_markups[$markup]['filter']) {
                $this->_markups[$markup]['filter'] = $this->getDefaultFilter();
            } else {
                return false;
            }
        }

        return $this->_markups[$markup]['filter'];
    }

    /**
     * Add a filter for an existing markup
     *
     * @param \Zend\Filter\Filter $filter
     * @param string $markup
     * @param string $placement
     *
     * @return \Zend\Markup\AbstractRenderer
     */
    public function addFilter(Filter\Filter $filter, $markup, $placement = Filter\FilterChainFilter\FilterChain::CHAIN_APPEND)
    {
        $markup = $this->_resolveMarkupName($markup);

        $oldFilter = $this->getFilter($markup);

        // if this filter is the default filter, clone it first
        if ($oldFilter === $this->getDefaultFilter()) {
            $oldFilter = clone $oldFilter;
        }

        if (!($oldFilter instanceof Filter\FilterChain)) {
            $this->_markups[$markup]['filter'] = new Filter\FilterChain();

            if ($oldFilter instanceof Filter\Filter) {
                $this->_markups[$markup]['filter']->addFilter($oldFilter);
            }
        } else {
            $this->_markups[$markup]['filter'] = $oldFilter;
        }

        $this->_markups[$markup]['filter']->addFilter($filter, $placement);

        return $this;
    }

    /**
     * Set the filter for an existing
     *
     * @param \Zend\Filter\Filter $filter
     * @param string $markup
     *
     * @return \Zend\Markup\AbstractRenderer
     */
    public function setFilter(Filter\Filter $filter, $markup)
    {
        $markup = $this->_resolveMarkupName($markup);

        $this->_markups[$markup]['filter'] = $filter;

        return $this;
    }

    /**
     * Add a render group
     *
     * @param string $name
     * @param array $allowedInside
     * @param array $allowsInside
     *
     * @return void
     */
    public function addGroup($name, array $allowedInside = array(), array $allowsInside = array())
    {
        $this->_groups[$name] = $allowsInside;

        foreach ($allowedInside as $group) {
            $this->_groups[$group][] = $name;
        }
    }

    /**
     * Get group definitions
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->_groups;
    }

    /**
     * Set the default filters
     *
     * @return void
     */
    abstract public function addDefaultFilters();

}
