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
    protected $_tags = array();

    /**
     * Parser
     *
     * @var Zend_Markup_Parser_ParserInterface
     */
    protected $_parser;

    /**
     * Use the filter or not
     *
     * @var bool
     */
    protected $_filter = true;

    /**
     * Filter chain
     *
     * @var Zend_Filter
     */
    protected $_filterChain;

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
        if (isset($options['useDefaultTags']) && ($options['useDefaultTags'] == false)) {
            $this->removeDefaultTags();
        }
        if (isset($options['filter'])) {
            $this->getFilterChain()->addFilter($options['filter']);
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
     * Get the filter chain
     *
     * @return Zend_Filter
     */
    public function getFilterChain()
    {
        if (null === $this->_filterChain) {
            $this->_filterChain = new Zend_Filter();
        }

        return $this->_filterChain;
    }

    /**
     * Add a filter
     *
     * @param Zend_Filter_Interface $filter
     *
     * @return void
     */
    public function addFilter(Zend_Filter_Interface $filter)
    {
        $this->getFilterChain()->addFilter($filter);
    }

    /**
     * Add a new tag
     *
     * @param string $name
     * @param string $type
     * @param array $info
     *
     * @return Zend_Markup_Renderer_RendererAbstract
     */
    public function addTag($name, $type, array $info)
    {
        if (!isset($info['group']) && ($type ^ self::TYPE_ALIAS)) {
            require_once 'Zend/Markup/Renderer/Exception.php';
            throw new Zend_Markup_Renderer_Exception("There is no render group defined.");
        }

        if (isset($info['filter'])) {
            $filter = (boolean) $info['filter'];
        } else {
            $filter = true;
        }

        // check the type
        if ($type & self::TYPE_CALLBACK) {
            // add a callback tag
            if (isset($info['callback'])) {
                if (!($info['callback'] instanceof Zend_Markup_Renderer_TokenConverterInterface)) {
                    require_once 'Zend/Markup/Renderer/Exception.php';
                    throw new Zend_Markup_Renderer_Exception("Not a valid tag callback.");
                }
                if (method_exists($info['callback'], 'setRenderer')) {
                    $info['callback']->setRenderer($this);
                }
            } else {
                $info['callback'] = null;
            }

            $info['type'] = $type;
            $info['filter'] = $filter;

            $this->_tags[$name] = $info;
        } elseif ($type & self::TYPE_ALIAS) {
            // add an alias
            if (empty($info['name'])) {
                require_once 'Zend/Markup/Renderer/Exception.php';
                throw new Zend_Markup_Renderer_Exception(
                        'No alias was provided but tag was defined as such');
            }

            $this->_tags[$name] = array(
                'type' => self::TYPE_ALIAS,
                'name' => $info['name']
            );
        } else {
            if ($type & self::TAG_SINGLE) {
                // add a single replace tag
                $info['type']   = $type;
                $info['filter'] = $filter;

                $this->_tags[$name] = $info;
            } else {
                // add a replace tag
                $info['type']   = $type;
                $info['filter'] = $filter;

                $this->_tags[$name] = $info;
            }
        }
        return $this;
    }

    /**
     * Remove a tag
     *
     * @param string $name
     *
     * @return void
     */
    public function removeTag($name)
    {
        unset($this->_tags[$name]);
    }

    /**
     * Remove the default tags
     *
     * @return void
     */
    public function clearTags()
    {
        $this->_tags = array();
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
        $oldToken  = $this->_token;

        // check filter and group usage in this tag
        if (isset($this->_tags[$token->getName()])) {
            if (isset($this->_tags[$token->getName()]['filter'])
                && $this->_tags[$token->getName()]['filter']
            ) {
                $this->_filter = true;
            } else {
                $this->_filter = false;
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
        $this->_token  = $oldToken;
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
        if (!isset($this->_tags[$token->getName()])) {
            return false;
        }

        $tag = $this->_tags[$token->getName()];

        // alias processing
        while ($tag['type'] & self::TYPE_ALIAS) {
            $tag = $this->_tags[$tag['name']];
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
        if (!isset($this->_tags[$token->getName()])) {
            return $this->_filter($token->getTag()) . $this->_render($token) . $token->getStopper();
        }

        $name = $this->_getTagName($token);
        $tag  = $this->_tags[$name];

        // check if the tag has content
        if (($tag['type'] & self::TAG_NORMAL) && !$token->hasChildren()) {
            return '';
        }

        // check for the context
        if (!in_array($tag['group'], $this->_groups[$this->_group])) {
            return $this->_filter($token->getTag()) . $this->_render($token) . $token->getStopper();
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
        while ($this->_tags[$name]['type'] & self::TYPE_ALIAS) {
            $name = $this->_tags[$name]['name'];
        }

        return $name;
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
        if ($this->_filter) {
            return $this->getFilterChain()->filter($value);
        }
        return $value;
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

}
