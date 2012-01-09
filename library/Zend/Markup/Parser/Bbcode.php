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
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Markup\Parser;
use Zend\Markup\Parser,
    Zend\Markup\Token,
    Zend\Markup\TokenList,
    Zend\Config\Config;

/**
 * @uses       \Zend\Markup\Parser\Exception
 * @uses       \Zend\Markup\Parser\ParserInterface
 * @uses       \Zend\Markup\Token
 * @uses       \Zend\Markup\TokenList
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Bbcode implements Parser
{
    const NEWLINE   = "[newline\0]";

    // there is a parsing difference between the default tags and single tags
    const TYPE_DEFAULT = 'default';
    const TYPE_SINGLE  = 'single';

    const NAME_CHARSET = '^\[\]=\s';

    /**
     * Token tree
     *
     * @var TokenList
     */
    protected $_tree;

    /**
     * Current token
     *
     * @var Token
     */
    protected $_current;

    /**
     * Stoppers that we are searching for
     *
     * @var array
     */
    protected $_searchedStoppers = array();

    /**
     * Tag information
     *
     * Contains the following information about every tag:
     *
     * - type:     single or default
     * - stoppers: how to end this tag
     * - group:    in which group the tag lives
     *
     * @var array
     */
    protected $_tags = array(
        'Zend_Markup_Root' => array(
            'type'     => self::TYPE_DEFAULT,
            'stoppers' => array()
        )
    );

    /**
     * Token array
     *
     * @var array
     */
    protected $_tokens = array();

    /**
     * Groups configuration
     *
     * An example of the format for this array:
     *
     * <code>
     * array(
     *     'block'  => array('inline', 'block'),
     *     'inline' => array('inline')
     * )
     * </code>
     *
     * This example shows two groups, block and inline. Elements who are in the
     * block group, allow elements from the inline and block groups inside
     * them. But elements from the inline group, only allow elements from the
     * inline group inside them.
     *
     * @var array
     */
    protected $_groups = array();

    /**
     * The current group
     *
     * @var string
     */
    protected $_group;

    /**
     * Default group for unknown tags
     *
     * @var string
     */
    protected $_defaultGroup;


    /**
     * Constructor
     *
     * @param \Zend\Config\Config|array $config
     *
     * @return array
     */
    public function __construct($options = array())
    {
        if ($options instanceof Config) {
            $options = $options->toArray();
        }

        if (isset($options['groups'])) {
            $this->setGroups($options['groups']);
        }
        if (isset($options['default_group'])) {
            $this->setDefaultGroup($options['default_group']);
        }
        if (isset($options['initial_group'])) {
            $this->setInitialGroup($options['initial_group']);
        }
        if (isset($options['tags'])) {
            $this->setTags($options['tags']);
        }
    }

    /**
     * Allow a group inside another group
     *
     * @param string $group
     * @param string $inside
     *
     * @return Bbcode
     */
    public function allowInside($group, $inside)
    {
        if (!isset($this->_groups[$group])) {
            throw new Exception\InvalidArgumentException("There is no group with the name '$group'.");
        }
        if (!isset($this->_groups[$inside])) {
            throw new Exception\InvalidArgumentException("There is no group with the name '$inside'.");
        }

        $this->_groups[$inside][] = $group;

        return $this;
    }

    /**
     * Add a group.
     *
     * @param string $group
     * @param array $allows
     * @param array $allowedInside
     *
     * @throws Exception\InvalidArgumentException If a group in $allowedInside does not exist
     *
     * @return Bbcode
     */
    public function addGroup($group, array $allows, array $allowedInside = array())
    {
        $this->_groups[$group] = $allows;

        foreach ($allowedInside as $inside) {
            $this->allowedInside($group, $inside);
        }

        return $this;
    }

    /**
     * Add multiple groups.
     *
     * The groups should be defined with the group as key, and the groups
     * allowed inside as value.
     *
     * An example for the $groups parameter.
     *
     * <code>
     * array(
     *     'block'  => array('block', 'inline'),
     *     'inline' => array('inline')
     * )
     * </code>
     *
     * @param array $groups
     *
     * @return Bbcode
     */
    public function addGroups(array $groups)
    {
        foreach ($groups as $group => $allowed) {
            $this->addGroup($group, $allowed);
        }

        return $this;
    }

    /**
     * Clear the groups.
     *
     * @return Bbcode
     */
    public function clearGroups()
    {
        $this->_groups = array();

        return $this;
    }

    /**
     * Overwrite the current groups definitions.
     *
     * The groups should be defined with the group as key, and the groups
     * allowed inside as value.
     *
     * An example for the $groups parameter.
     *
     * <code>
     * array(
     *     'block'  => array('block', 'inline'),
     *     'inline' => array('inline')
     * )
     * </code>
     *
     * @param array $groups
     *
     * @return Bbcode
     */
    public function setGroups(array $groups)
    {
        $this->clearGroups();

        $this->addGroups($groups);

        return $this;
    }

    /**
     * Set the default group for all tags.
     *
     * @param string $group
     *
     * @throws Exception\InvalidArgumentException If $group doesn't exist
     *
     * @return Bbcode
     */
    public function setDefaultGroup($group)
    {
        if (!isset($this->_groups[$group])) {
            throw new Exception\InvalidArgumentException("There is no group with the name '$group'.");
        }

        $this->_defaultGroup = $group;

        return $this;
    }

    /**
     * Set the initial group.
     *
     * The initial group is the group that contains all elements.
     *
     * @param string $group
     *
     * @throws Exception\InvalidArgumentException If $group doesn't exist
     *
     * @return Bbcode
     */
    public function setInitialGroup($group)
    {
        if (!isset($this->_groups[$group])) {
            throw new Exception\InvalidArgumentException("There is no group with the name '$group'.");
        }

        $this->_group = $group;

        return $this;
    }

    /**
     * Add a definition for a tag
     *
     * @param string $name
     * @param string $group
     * @param string $type Either Bbcode::TYPE_DEFAULT or Bbcode::TYPE_SINGLE
     * @param array $stoppers
     *
     * @throws Exception\InvalidArgumentException If the given group doesn't exist
     * @throws Exception\InvalidArgumentException If the type isn't correct
     * @throws Exception\InvalidArgumentException If the stoppers argument isn't correct
     *
     * @return Bbcode
     */
    public function addTag($name, $group = null, $type = self::TYPE_DEFAULT, $stoppers = null)
    {
        if (null === $group) {
            $group = $this->_defaultGroup;
        } elseif (!isset($this->_groups[$group])) {
            throw new Exception\InvalidArgumentException("There is no group with the name '$group'.");
        }
        if (($type != self::TYPE_DEFAULT) && ($type != self::TYPE_SINGLE)) {
            throw new Exception\InvalidArgumentException("You can only use the types 'default' and 'single'");
        }
        if (null === $stoppers) {
            $stoppers = array(
                '[/' . $name . ']',
                '[/]'
            );
        } elseif (is_string($stoppers)) {
            $stoppers = array($stoppers);
        } elseif (!is_array($stoppers)) {
            throw new Exception\InvalidArgumentException("Invalid stoppers argument provided.");
        }

        // after checking everything, add the tag
        $this->_tags[$name] = array(
            'type'     => $type,
            'group'    => $group,
            'stoppers' => $stoppers
        );

        return $this;
    }

    /**
     * Add multiple tag definitions at once
     *
     * @param array $tags
     *
     * @return Bbcode
     */
    public function addTags(array $tags)
    {
        foreach ($tags as $name => $tag) {
            if (!isset($tag['group'])) {
                $tag['group'] = null;
            }
            if (!isset($tag['type'])) {
                $tag['type'] = self::TYPE_DEFAULT;
            }
            if (!isset($tag['stoppers'])) {
                $tag['stoppers'] = null;
            }

            $this->addTag($name, $tag['group'], $tag['type'], $tag['stoppers']);
        }

        return $this;
    }

    /**
     * Clear the tags
     *
     * @return Bbcode
     */
    public function clearTags()
    {
        $this->_tags = array(
            'Zend_Markup_Root' => array(
                'type'     => self::TYPE_DEFAULT,
                'stoppers' => array()
            )
        );

        return $this;
    }

    /**
     * Override the current tags
     *
     * @param array $tags
     *
     * @return Bbcode
     */
    public function setTags(array $tags)
    {
        $this->clearTags();

        $this->addTags($tags);

        return $this;
    }

    /**
     * Parse a BBCode string, this simply sources out the lexical analysis to
     * the {@link tokenize()} method, and the syntactical analysis to the
     * {@link buildTree()} method.
     *
     * @param  string $value
     * @return \Zend\Markup\TokenList
     */
    public function parse($value)
    {
        if (!is_string($value)) {
            throw new Exception\InvalidArgumentException('Value to parse should be a string.');
        }
        if (empty($value)) {
            throw new Exception\InvalidArgumentException('Value to parse cannot be left empty.');
        }

        $tokens = $this->tokenize($value);

        return $this->buildTree($tokens);
    }

    /**
     * Tokenize
     *
     * @param string $value
     *
     * @return array
     */
    public function tokenize($value)
    {
        $value = str_replace(array("\r\n", "\r", "\n"), self::NEWLINE, $value);

        $attribute = '';

        $valueLen = strlen($value);
        $pointer  = 0;
        $buffer   = '';
        $temp     = array();
        $tokens   = array();

        scan: {
            if ($valueLen <= $pointer) {
                goto end;
            }

            $matches = array();
            $regex   = '#\G(?<text>[^\[]*)(?<open>\[(?<name>[' . self::NAME_CHARSET . ']+)?)?#';
            if (!preg_match($regex, $value, $matches, null, $pointer)) {
                goto end;
            }

            $pointer += strlen($matches[0]);

            if (!empty($matches['text'])) {
                $buffer .= $matches['text'];
            }

            if (!isset($matches['open'])) {
                // great, no tag, we are ending the string
                goto scan;
            }
            if (!isset($matches['name'])) {
                $buffer .= $matches['open'];
                goto scan;
            }

            $temp = array(
                'tag'        => '[' . $matches['name'],
                'name'       => $matches['name'],
                'attributes' => array()
            );

            if ($pointer >= $valueLen) {
                // damn, no tag
                $buffer .= $temp['tag'];
                goto end;
            }

            if ($value[$pointer] == '=') {
                $pointer++;

                $temp['tag'] .= '=';
                $attribute           = $temp['name'];

                goto parsevalue;
            }
            goto scanattrs;
        }

        scanattrs: {
            $matches = array();
            $regex   = '#\G((?<end>\s*\])|\s+(?<attribute>[' . self::NAME_CHARSET . ']+)(?<eq>=?))#';
            if (!preg_match($regex, $value, $matches, null, $pointer)) {
                goto end;
            }

            $pointer += strlen($matches[0]);

            if (!empty($matches['end'])) {
                if (!empty($buffer)) {
                    $tokens[] = array(
                        'tag' => $buffer,
                        'type' => Token::TYPE_NONE
                    );
                    $buffer = '';
                }
                $temp['tag'] .= $matches['end'];
                $temp['type'] = Token::TYPE_MARKUP;

                $tokens[] = $temp;
                $temp     = array();

                goto scan;
            } else {
                // attribute name
                $attribute = $matches['attribute'];

                $temp['tag'] .= $matches[0];

                $temp['attributes'][$attribute] = '';

                if (empty($matches['eq'])) {
                    goto scanattrs;
                }
                goto parsevalue;
            }
        }

        parsevalue: {
            $matches = array();
            $regex   = '#\G((?<quote>"|\')(?<valuequote>.*?)\\2|(?<value>[^\]\s]+))#';
            if (!preg_match($regex, $value, $matches, null, $pointer)) {
                goto scanattrs;
            }

            $pointer += strlen($matches[0]);

            if (!empty($matches['quote'])) {
                $temp['attributes'][$attribute] = $matches['valuequote'];
            } else {
                $temp['attributes'][$attribute] = $matches['value'];
            }
            $temp['tag'] .= $matches[0];

            goto scanattrs;
        }

        end:

        if (!empty($buffer)) {
            $tokens[] = array(
                'tag'  => $buffer,
                'type' => Token::TYPE_NONE
            );
        }

        return $tokens;
    }

    /**
     * Build a tree with a certain strategy
     *
     * @param array $tokens
     * @param string $strategy
     *
     * @throws Exception\RuntimeException If there are no groups defined
     * @throws Exception\RuntimeException If there is no initial group defined
     * @throws Exception\RuntimeException If there is no default group defined
     *
     * @return \Zend\Markup\TokenList/
     */
    public function buildTree(array $tokens, $strategy = 'default')
    {
        if (empty($this->_groups)) {
            throw new Exception\RuntimeException("There are no groups defined.");
        }
        if (null === $this->_groups) {
            throw new Exception\RuntimeException("There is no initial group defined.");
        }
        if (null === $this->_defaultGroup) {
            throw new Exception\RuntimeException("There is no default group defined.");
        }

        switch ($strategy) {
            case 'default':
                return $this->_createTree($tokens);
                break;
            default:
                throw new Exception\InvalidArgumentException("There is no treebuilding strategy called '$strategy'.");
                break;
        }
    }

    /**
     * Parse the token array into a tree
     *
     * @param array $tokens
     *
     * @return \Zend\Markup\TokenList
     */
    protected function _createTree($tokens)
    {
        // variable initialization for treebuilder
        $groupStack              = array($this->_group);
        $this->_searchedStoppers = array();
        $this->_tree             = new TokenList();
        $this->_current          = new Token(
            '',
            Token::TYPE_NONE,
            'Zend_Markup_Root'
        );

        $this->_tree->addChild($this->_current);

        foreach ($tokens as $token) {
            // first we want to know if this tag is a stopper, or at least a searched one
            if ($this->_isStopper($token['tag'])) {
                // find the stopper
                $oldItems = array();

                while (!in_array($token['tag'], $this->_tags[$this->_current->getName()]['stoppers'])) {
                    $oldItems[]     = clone $this->_current;
                    $this->_current = $this->_current->getParent();

                    // use a lower level group
                    $this->_group = array_pop($groupStack);
                }

                // we found the stopper, so stop the tag
                $this->_current->setStopper($token['tag']);
                $this->_removeFromSearchedStoppers($this->_current);
                $this->_current = $this->_current->getParent();
                $this->_group   = array_pop($groupStack);

                // add the old items again if there are any
                if (!empty($oldItems)) {
                    foreach (array_reverse($oldItems) as $item) {
                        /* @var $token \Zend\Markup\Token */
                        $this->_current->addChild($item);
                        $item->setParent($this->_current);
                        $this->_current = $item;

                        // re-add the group
                        $groupStack[] = $this->_group;
                        $this->_group = $this->_getGroup($item->getName());
                    }
                }
            } else {
                if ($token['type'] == Token::TYPE_MARKUP) {
                    if ($token['tag'] == self::NEWLINE) {
                        // this is a newline tag, add it as a token
                        $this->_current->addChild(new Token(
                            "\n",
                            Token::TYPE_NONE,
                            '',
                            array(),
                            $this->_current
                        ));
                    } elseif (isset($token['name']) && ($token['name'][0] == '/')) {
                        // this is a stopper, add it as a empty token
                        $this->_current->addChild(new Token(
                            $token['tag'],
                            Token::TYPE_NONE,
                            '',
                            array(),
                            $this->_current
                        ));
                    } elseif (!$this->_checkTagAllowed($token)) {
                        // TODO: expand this to using groups for the context-awareness
                        $this->_current->addChild(new Token(
                            $token['tag'],
                            Token::TYPE_NONE,
                            '',
                            array(),
                            $this->_current
                        ));
                    } else {
                        // add the tag
                        $child = new Token(
                            $token['tag'],
                            $token['type'],
                            $token['name'],
                            $token['attributes'],
                            $this->_current
                        );
                        $this->_current->addChild($child);

                        // set the new group
                        $groupStack[] = $this->_group;
                        $this->_group = $this->_getGroup($token['name']);

                        // add stoppers for this tag, if its has stoppers
                        if ($this->_getType($token['name']) == self::TYPE_DEFAULT) {
                            $this->_current = $child;

                            $this->_addToSearchedStoppers($this->_current);
                        }
                    }
                } else {
                    // no tag, just add it as a simple token
                    $this->_current->addChild(new Token(
                        $token['tag'],
                        Token::TYPE_NONE,
                        '',
                        array(),
                        $this->_current
                    ));
                }
            }
        }

        return $this->_tree;
    }

    /**
     * Check if there is a tag declaration, and if it isnt there, add it
     *
     * @param string $name
     *
     * @return void
     */
    protected function _checkTagDeclaration($name)
    {
        if (!isset($this->_tags[$name])) {
            $this->_tags[$name] = array(
                'type'     => self::TYPE_DEFAULT,
                'stoppers' => array(
                    '[/' . $name . ']',
                    '[/]'
                ),
                'group' => $this->_defaultGroup
            );
        }
    }

    /**
     * Get the group for a token
     *
     * @param string $name
     *
     * @return string
     */
    protected function _getGroup($name)
    {
        $this->_checkTagDeclaration($name);

        return $this->_tags[$name]['group'];
    }

    /**
     * Check if a tag is allowed in the current context
     *
     * @todo Use groups to determine if tags are allowed in the current context
     *
     * @param array $token
     *
     * @return bool
     */
    protected function _checkTagAllowed(array $token)
    {
        if (in_array($this->_getGroup($token['name']), $this->_groups[$this->_group])) {
            return true;
        }

        // fallback for not allowed
        return false;
    }

    /**
     * Check the tag's type
     *
     * @param  string $name
     * @return string
     */
    protected function _getType($name)
    {
        $this->_checkTagDeclaration($name);

        return $this->_tags[$name]['type'];
    }

    /**
     * Check if the tag is a stopper
     *
     * @param  string $tag
     * @return bool
     */
    protected function _isStopper($tag)
    {
        $this->_checkTagDeclaration($this->_current->getName());

        if (!empty($this->_searchedStoppers[$tag])) {
            return true;
        }

        return false;
    }

    /**
     * Add to searched stoppers
     *
     * @param  \Zend\Markup\Token $token
     * @return void
     */
    protected function _addToSearchedStoppers(Token $token)
    {
        $this->_checkTagDeclaration($token->getName());

        foreach ($this->_tags[$token->getName()]['stoppers'] as $stopper) {
            if (!isset($this->_searchedStoppers[$stopper])) {
                $this->_searchedStoppers[$stopper] = 0;
            }
            ++$this->_searchedStoppers[$stopper];
        }
    }

    /**
     * Remove from searched stoppers
     *
     * @param  \Zend\Markup\Token $token
     * @return void
     */
    protected function _removeFromSearchedStoppers(Token $token)
    {
        $this->_checkTagDeclaration($token->getName());

        foreach ($this->_tags[$token->getName()]['stoppers'] as $stopper) {
            --$this->_searchedStoppers[$stopper];
        }
    }

}
