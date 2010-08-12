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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Markup\Parser;

use Zend\Markup\Parser,
    Zend\Markup;

/**
 * @uses       \Zend\Markup\Parser\Exception
 * @uses       \Zend\Markup\Parser
 * @uses       \Zend\Markup\Token
 * @uses       \Zend\Markup\TokenList
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @var \Zend\Markup\TokenList
     */
    protected $_tree;

    /**
     * Current token
     *
     * @var \Zend\Markup\Token
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
     * @var array
     */
    protected $_tags = array(
        'Zend_Markup_Root' => array(
            'type'     => self::TYPE_DEFAULT,
            'stoppers' => array(),
        ),
        '*' => array(
            'type'     => self::TYPE_DEFAULT,
            'stoppers' => array(self::NEWLINE, '[/*]', '[/]'),
        ),
        'hr' => array(
            'type'     => self::TYPE_SINGLE,
            'stoppers' => array(),
        ),
        'code' => array(
            'type'         => self::TYPE_DEFAULT,
            'stoppers'     => array('[/code]', '[/]'),
            'parse_inside' => false
        )
    );

    /**
     * Token array
     *
     * @var array
     */
    protected $_tokens = array();


    /**
     * Prepare the parsing of a bbcode string, the real parsing is done in {@link _parse()}
     *
     * @param  string $value
     * @return \Zend\Markup\TokenList
     */
    public function parse($value)
    {
        if (!is_string($value)) {
            throw new Exception('Value to parse should be a string.');
        }

        if (empty($value)) {
            throw new Exception('Value to parse cannot be left empty.');
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
                        'type' => Markup\Token::TYPE_NONE
                    );
                    $buffer = '';
                }
                $temp['tag'] .= $matches['end'];
                $temp['type'] = Markup\Token::TYPE_TAG;

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
                'type' => Markup\Token::TYPE_NONE
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
     * @return \Zend\Markup\TokenList/
     */
    public function buildTree(array $tokens, $strategy = 'default')
    {
        switch ($strategy) {
            case 'default':
                return $this->_createTree($tokens);
                break;
            default:
                // TODO: throw exception for this case
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
        $this->_searchedStoppers = array();
        $this->_tree             = new Markup\TokenList();
        $this->_current          = new Markup\Token(
            '',
            Markup\Token::TYPE_NONE,
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
                }

                // we found the stopper, so stop the tag
                $this->_current->setStopper($token['tag']);
                $this->_removeFromSearchedStoppers($this->_current);
                $this->_current = $this->_current->getParent();

                // add the old items again if there are any
                if (!empty($oldItems)) {
                    foreach (array_reverse($oldItems) as $item) {
                        /* @var $token \Zend\Markup\Token */
                        $this->_current->addChild($item);
                        $item->setParent($this->_current);
                        $this->_current = $item;
                    }
                }
            } else {
                if ($token['type'] == Markup\Token::TYPE_TAG) {
                    if ($token['tag'] == self::NEWLINE) {
                        // this is a newline tag, add it as a token
                        $this->_current->addChild(new Markup\Token(
                            "\n",
                            Markup\Token::TYPE_NONE,
                            '',
                            array(),
                            $this->_current
                        ));
                    } elseif (isset($token['name']) && ($token['name'][0] == '/')) {
                        // this is a stopper, add it as a empty token
                        $this->_current->addChild(new Markup\Token(
                            $token['tag'],
                            Markup\Token::TYPE_NONE,
                            '',
                            array(),
                            $this->_current
                        ));
                    } elseif (isset($this->_tags[$this->_current->getName()]['parse_inside'])
                        && !$this->_tags[$this->_current->getName()]['parse_inside']
                    ) {
                        $this->_current->addChild(new Markup\Token(
                            $token['tag'],
                            Markup\Token::TYPE_NONE,
                            '',
                            array(),
                            $this->_current
                        ));
                    } else {
                        // add the tag
                        $child = new Markup\Token(
                            $token['tag'],
                            $token['type'],
                            $token['name'],
                            $token['attributes'],
                            $this->_current
                        );
                        $this->_current->addChild($child);

                        // add stoppers for this tag, if its has stoppers
                        if ($this->_getType($token['name']) == self::TYPE_DEFAULT) {
                            $this->_current = $child;

                            $this->_addToSearchedStoppers($this->_current);
                        }
                    }
                } else {
                    // no tag, just add it as a simple token
                    $this->_current->addChild(new Markup\Token(
                        $token['tag'],
                        Markup\Token::TYPE_NONE,
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
                )
            );
        }
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
    protected function _addToSearchedStoppers(Markup\Token $token)
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
    protected function _removeFromSearchedStoppers(Markup\Token $token)
    {
        $this->_checkTagDeclaration($token->getName());

        foreach ($this->_tags[$token->getName()]['stoppers'] as $stopper) {
            --$this->_searchedStoppers[$stopper];
        }
    }

}
