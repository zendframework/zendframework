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
 * @see Zend_config
 */
require_once 'Zend/Config.php';
/**
 * @see Zend_Filter
 */
require_once 'Zend/Filter.php';
/**
 * @see Zend_Markup_Renderer_TokenConverterInterface
 */
require_once 'Zend/Markup/Renderer/TokenConverterInterface.php';

/**
 * Defines the basic rendering functionality
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Markup_Renderer_RendererAbstract
{
    const TAG_SINGLE    = 1;
    const TAG_NORMAL    = 2;

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
     * @var Zend_Markup_Parser_ParserInterface
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
     * @var Zend_Filter
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
     * @var Zend_Loader_PluginLoader
     */
    protected $_pluginLoader;

    /**
     * The current token
     *
     * @var Zend_Markup_Token
     */
    protected $_token;


    /**
     * Constructor
     *
     * @param array|Zend_Config $options
     *
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (isset($options['parser'])) {
            $this->setParser($options['parser']);
        }
        if (isset($options['useDefaultTags']) && ($options['useDefaultTags'] === false)) {
            $this->removeDefaultTags();
        }
        if (!isset($options['useDefaultFilters']) || ($options['useDefaultFilters'] === true)) {
            $this->setDefaultFilter();
        }
        if (isset($options['defaultFilter'])) {
            $this->addDefaultFilter($options['defaultFilter']);
        }
    }

    /**
     * Set the parser
     *
     * @param  Zend_Markup_Parser_ParserInterface $parser
     * @return Zend_Markup_Renderer_RendererAbstract
     */
    public function setParser(Zend_Markup_Parser_ParserInterface $parser)
    {
        $this->_parser = $parser;
        return $this;
    }

    /**
     * Get the parser
     *
     * @return Zend_Markup_Parser_ParserInterface
     */
    public function getParser()
    {
        return $this->_parser;
    }

    /**
     * Get the plugin loader
     *
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader()
    {
        return $this->_pluginLoader;
    }

    /**
     * Add a new markup
     *
     * @param string $name
     * @param string $type
     * @param array $options
     *
     * @return Zend_Markup_Renderer_RendererAbstract
     */
    public function addMarkup($name, $type, array $options)
    {
        if (!isset($options['group']) && ($type ^ self::TYPE_ALIAS)) {
            require_once 'Zend/Markup/Renderer/Exception.php';
            throw new Zend_Markup_Renderer_Exception("There is no render group defined.");
        }

        // add the filter
        if (isset($options['filter'])) {
            if ($options['filter'] instanceof Zend_Filter_Interface) {
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
                if (!($options['callback'] instanceof Zend_Markup_Renderer_TokenConverterInterface)) {
                    require_once 'Zend/Markup/Renderer/Exception.php';
                    throw new Zend_Markup_Renderer_Exception("Not a valid tag callback.");
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
                require_once 'Zend/Markup/Renderer/Exception.php';
                throw new Zend_Markup_Renderer_Exception(
                        'No alias was provided but tag was defined as such');
            }

            $this->_markups[$name] = array(
                'type' => self::TYPE_ALIAS,
                'name' => $options['name']
            );
        } else {
            if ($type & self::TAG_SINGLE) {
                // add a single replace tag
                $options['type']   = $type;
                $options['filter'] = $filter;

                $this->_markups[$name] = $options;
            } else {
                // add a replace tag
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
     * @param  Zend_Markup_TokenList|string $tokenList
     * @return string
     */
    public function render($value)
    {
        if ($value instanceof Zend_Markup_TokenList) {
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
     * @param  Zend_Markup_Token $token
     * @return string
     */
    protected function _render(Zend_Markup_Token $token)
    {
        $return    = '';

        // save old values to reset them after the work is done
        $oldFilter = $this->_filter;
        $oldGroup  = $this->_group;

        // check filter and group usage in this tag
        if (isset($this->_markups[$token->getName()])) {
            if (isset($this->_markups[$token->getName()]['filter'])) {
                $this->_filter = $this->_markups[$token->getName()]['filter'];
            }

            if ($group = $this->_getGroup($token)) {
                $this->_group = $group;
            }
        }

        $this->_token = $token;

        // if this tag has children, execute them
        if ($token->hasChildren()) {
            foreach ($token->getChildren() as $child) {
                $return .= $this->_execute($child);
            }
        }

        // reset to the old values
        $this->_filter = $oldFilter;
        $this->_group  = $oldGroup;

        return $return;
    }

    /**
     * Get the group of a token
     *
     * @param  Zend_Markup_Token $token
     * @return string|bool
     */
    protected function _getGroup(Zend_Markup_Token $token)
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
     * @param  Zend_Markup_Token $token
     * @return string
     */
    protected function _execute(Zend_Markup_Token $token)
    {
        // first return the normal text tags
        if ($token->getType() == Zend_Markup_Token::TYPE_NONE) {
            return $this->_filter($token->getTag());
        }

        // if the token doesn't have a notation, return the plain text
        if (!isset($this->_markups[$token->getName()])) {
            $oldToken  = $this->_token;
            $return = $this->_filter($token->getTag()) . $this->_render($token) . $token->getStopper();
            $this->_token = $oldToken;
            return $return;
        }

        $name = $this->_getTagName($token);
        $tag  = $this->_markups[$name];

        // check if the tag has content
        if (($tag['type'] & self::TAG_NORMAL) && !$token->hasChildren()) {
            return '';
        }

        // check for the context
        if (!in_array($tag['group'], $this->_groups[$this->_group])) {
            $oldToken  = $this->_token;
            $return = $this->_filter($token->getTag()) . $this->_render($token) . $token->getStopper();
            $this->_token = $oldToken;
            return $return;
        }

        // check for the filter
        if (!isset($tag['filter'])
        || (!($tag['filter'] instanceof Zend_Filter_Interface) && ($tag['filter'] !== false))) {
            $this->_markups[$name]['filter'] = $this->getDefaultFilter();
        }

        // callback
        if ($tag['type'] & self::TYPE_CALLBACK) {
            // load the callback if the tag doesn't exist
            if (!($tag['callback'] instanceof Zend_Markup_Renderer_TokenConverterInterface)) {
                $class = $this->getPluginLoader()->load($name);

                $tag['callback'] = new $class;

                if (!($tag['callback'] instanceof Zend_Markup_Renderer_TokenConverterInterface)) {
                    require_once 'Zend/Markup/Renderer/Exception.php';
                    throw new Zend_Markup_Renderer_Exception("Callback for tag '$name' found, but it isn't valid.");
                }

                if (method_exists($tag['callback'], 'setRenderer')) {
                    $tag['callback']->setRenderer($this);
                }
            }
            if ($tag['type'] & self::TAG_NORMAL) {
                return $tag['callback']->convert($token, $this->_render($token));
            }
            return $tag['callback']->convert($token, null);
        }
        // replace
        if ($tag['type'] & self::TAG_NORMAL) {
            return $this->_executeReplace($token, $tag);
        }
        return $this->_executeSingleReplace($token, $tag);
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
        if ($this->_filter instanceof Zend_Filter_Interface) {
            return $this->_filter->filter($value);
        }
        return $value;
    }

    /**
     * Get the tag name
     *
     * @param Zend_Markup_Token
     *
     * @return string
     */
    protected function _getTagName(Zend_Markup_Token $token)
    {
        $name = $token->getName();

        // process the aliases
        while ($this->_markups[$name]['type'] & self::TYPE_ALIAS) {
            $name = $this->_markups[$name]['name'];
        }

        return $name;
    }

    /**
     * Execute a replace token
     *
     * @param  Zend_Markup_Token $token
     * @param  array $tag
     * @return string
     */
    protected function _executeReplace(Zend_Markup_Token $token, $tag)
    {
        return $tag['start'] . $this->_render($token) . $tag['end'];
    }

    /**
     * Execute a single replace token
     *
     * @param  Zend_Markup_Token $token
     * @param  array $tag
     * @return string
     */
    protected function _executeSingleReplace(Zend_Markup_Token $token, $tag)
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
            $this->setDefaultFilter();
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
    public function addDefaultfilter(Zend_Filter_Interface $filter, $placement = Zend_Filter::CHAIN_APPEND)
    {
        if (!($this->_defaultFilter instanceof Zend_Filter)) {
            $defaultFilter = new Zend_Filter();
            $defaultFilter->addFilter($filter);
            $this->_defaultFilter = $defaultFilter;
        }

        $this->_defaultFilter->addFilter($filter, $placement);
    }

    /**
     * Set the default filters
     *
     * @param Zend_Filter_Interface $filter
     *
     * @return void
     */
    abstract public function setDefaultFilter(Zend_Filter_Interface $filter = null);
}
