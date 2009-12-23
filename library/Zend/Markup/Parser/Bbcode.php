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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Markup_TokenList
 */
require_once 'Zend/Markup/TokenList.php';

/**
 * @see Zend_Markup_Parser_ParserInterface
 */
require_once 'Zend/Markup/Parser/ParserInterface.php';

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_Parser_Bbcode implements Zend_Markup_Parser_ParserInterface
{
    const TAG_START = '[';
    const TAG_END   = ']';
    const NEWLINE   = "[newline\0]";

    // there is a parsing difference between the default tags and single tags
    const TYPE_DEFAULT = 'default';
    const TYPE_SINGLE  = 'single';

    /**
     * Token tree
     *
     * @var Zend_Markup_TokenList
     */
    protected $_tree;

    /**
     * Current token
     *
     * @var Zend_Markup_Token
     */
    protected $_current;

    /**
     * Source to tokenize
     *
     * @var string
     */
    protected $_value = '';

    /**
     * Length of the value
     *
     * @var int
     */
    protected $_valueLen = 0;

    /**
     * Current pointer
     *
     * @var int
     */
    protected $_pointer = 0;

    /**
     * The buffer
     *
     * @var string
     */
    protected $_buffer = '';

    /**
     * The current tag we are working on
     *
     * @var string
     */
    protected $_tag = '';

    /**
     * The current tag name
     *
     * @var string
     */
    protected $_name;

    /**
     * Attributes of the tag we are working on
     *
     * @var array
     */
    protected $_attributes = array();

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
            'stoppers' => array(self::NEWLINE),
        ),
        'hr' => array(
            'type'     => self::TYPE_SINGLE,
            'stoppers' => array(),
        ),
    );


    /**
     * Prepare the parsing of a bbcode string, the real parsing is done in {@link _parse()}
     *
     * @param  string $value
     * @return Zend_Markup_TokenList
     */
    public function parse($value)
    {
        if (!is_string($value)) {
            /**
             * @see Zend_Markup_Parser_Exception
             */
            require_once 'Zend/Markup/Parser/Exception.php';
            throw new Zend_Markup_Parser_Exception('Value to parse should be a string.');
        }

        if (empty($value)) {
            /**
             * @see Zend_Markup_Parser_Exception
             */
            require_once 'Zend/Markup/Parser/Exception.php';
            throw new Zend_Markup_Parser_Exception('Value to parse cannot be left empty.');
        }

        // first make we only have LF newlines
        $this->_value = str_replace(array("\r\n", "\r", "\n"), self::NEWLINE, $value);

        // initialize variables
        $this->_tree             = new Zend_Markup_TokenList();
        $this->_valueLen         = strlen($this->_value);
        $this->_pointer          = 0;
        $this->_buffer           = '';
        $this->_temp             = array();
        $this->_searchedStoppers = array();
        $this->_current          = new Zend_Markup_Token(
            '',
            Zend_Markup_Token::TYPE_NONE,
            'Zend_Markup_Root'
        );

        $this->_tree->addChild($this->_current);

        // start the parsing process
        $this->_parse();

        return $this->_tree;
    }

    /**
     * Parse a bbcode string
     *
     * @return void
     */
    protected function _parse()
    {
        // just keep looping until the parsing is done
        do {
            $this->_parseTagStart();
        } while ($this->_pointer < $this->_valueLen);

        if (!empty($this->_buffer)) {
            // no tag start found, add the buffer to the current tag and stop parsing
            $token = new Zend_Markup_Token(
                $this->_buffer,
                Zend_Markup_Token::TYPE_NONE,
                $this->_current
            );
            $this->_current->addChild($token);
            $this->_buffer  = '';
        }
    }

    /**
     * Parse the start of a tag
     *
     * @return void
     */
    protected function _parseTagStart()
    {
        $start = strpos($this->_value, self::TAG_START, $this->_pointer);

        if ($start === false) {
            if ($this->_valueLen > $this->_pointer) {
                $this->_buffer .= substr($this->_value, $this->_pointer);
                $this->_pointer = $this->_valueLen;
            }
            return;
        }

        // add the prepended text to the buffer
        if ($start > $this->_pointer) {
            $this->_buffer .= substr($this->_value, $this->_pointer, $start - $this->_pointer);
        }

        $this->_pointer = $start;

        // we have the start of this tag, now we need its name
        $this->_parseTag();
    }

    /**
     * Get the tag information
     *
     * @return void
     */
    protected function _parseTag()
    {
        // get the tag's name
        $len         = strcspn($this->_value, " \n\r\t=" . self::TAG_END, $this->_pointer + 1);
        $this->_name = substr($this->_value, $this->_pointer + 1, $len);

        $this->_tag      = self::TAG_START . $this->_name;
        $this->_pointer += $len + 1;

        if (!isset($this->_value[$this->_pointer])) {
            // this is not a tag
            $this->_buffer .= $this->_tag;

            return;
        }

        switch ($this->_value[$this->_pointer]) {
            case self::TAG_END:
                // ending the tag
                $this->_tag .= self::TAG_END;
                $this->_endTag();
                return;
                break;
            case '=':
                // we are dealing with an name-attribute
                $this->_tag .= '=';
                ++$this->_pointer;
                $value = $this->_parseAttributeValue();

                if (false === $value) {
                    // this isn't a tag, just end it right here, right now
                    $this->_buffer .= $this->_tag;
                    return;
                }

                $this->_attributes[$this->_name] = $value;
                break;
            default:
                // the tag didn't end, so get the rest of the tag.
                break;
        }

        $this->_parseAttributes();
    }

    /**
     * Parse attributes
     *
     * @return void
     */
    protected function _parseAttributes()
    {
        while ($this->_pointer < $this->_valueLen) {
            // we are looping until we find something
            switch ($this->_value[$this->_pointer]) {
                case self::TAG_END:
                    // end the tag and return
                    $this->_tag .= self::TAG_END;
                    $this->_endTag();
                    return;
                    break;
                default:
                    // just go further
                    if (ctype_space($this->_value[$this->_pointer])) {
                        //@TODO: implement this speedhack later
                        $len             = strspn($this->_value, " \n\r\t", $this->_pointer + 1);
                        $this->_tag     .= substr($this->_value, $this->_pointer, $len - 1);
                        $this->_tag .= $this->_value[$this->_pointer];
                        ++$this->_pointer;
                    } else {
                        $this->_parseAttribute();
                    }
                    break;
            }
        }

        // end tags without ']'
        $this->_endTag();
    }

    /**
     * Parse an attribute
     *
     * @return void
     */
    protected function _parseAttribute()
    {
        // first find the =, or a ] when the attribute is empty
        $len = strcspn($this->_value, "=" . self::TAG_END, $this->_pointer);

        // get the name and value
        $name = substr($this->_value, $this->_pointer, $len);
        $this->_pointer += $len;

        if (isset($this->_value[$this->_pointer]) && ($this->_value[$this->_pointer] == '=')) {
            ++$this->_pointer;
            // ending attribute
            $this->_tag .= $name . '=';

            $value = $this->_parseAttributeValue();

            $this->_attributes[trim($name)] = $value;
        } else {
            // empty attribute
            $this->_tag .= $name;
        }
    }

    /**
     * Parse the value from an attribute
     *
     * @return string
     */
    protected function _parseAttributeValue()
    {
        //$delimiter = $this->_value[$this->_pointer];
        $delimiter = substr($this->_value, $this->_pointer, 1);
        if (($delimiter == "'") || ($delimiter == '"')) {
            $delimiter = $this->_value[$this->_pointer];

            // just find the delimiter
            $len   = strcspn($this->_value, $delimiter, $this->_pointer + 1);
            $value = substr($this->_value, $this->_pointer + 1, $len);

            if ($this->_pointer + $len + 1 >= $this->_valueLen) {
                // i think we just ran out of gas....
                $this->_pointer++;
                $this->_tag .= $delimiter;
                return false;
            }

            $this->_pointer += $len + 2;

            $this->_tag .= $delimiter . $value . $delimiter;
        } else {
            // find a tag end or a whitespace
            $len   = strcspn($this->_value, " \n\r\t" . self::TAG_END, $this->_pointer);
            $value = substr($this->_value, $this->_pointer, $len);

            $this->_pointer += $len;

            $this->_tag .= $value;
        }
        return $value;
    }

    /**
     * End the found tag
     *
     * @return void
     */
    protected function _endTag()
    {
        // rule out empty tags (just '[]')
        if (strlen($this->_name) == 0) {
            $this->_buffer .= $this->_tag;
            $this->_pointer++;
            return;
        }

        // first check if the tag is a newline or a stopper without a tag
        if (!$this->_isStopper($this->_tag, true)) {
            if ($this->_tag == self::NEWLINE) {
                $this->_buffer .= "\n";
                ++$this->_pointer;
                return;
            } elseif ($this->_name[0] == '/') {
                $this->_buffer .= $this->_tag;
                ++$this->_pointer;
                return;
            }
        }

        // first add the buffer as token and clear the buffer
        if (!empty($this->_buffer)) {
            $token = new Zend_Markup_Token(
                $this->_buffer,
                Zend_Markup_Token::TYPE_NONE,
                '',
                array(),
                $this->_current
            );
            $this->_current->addChild($token);
            $this->_buffer = '';
        }

        $attributes = $this->_attributes;

        // check if this tag is a stopper
        if ($this->_isStopper($this->_tag)) {
            // we got a stopper, end the current tag and get back to the parent
            $this->_current->setStopper($this->_tag);

            $this->_removeFromSearchedStoppers($this->_current);

            $this->_current = $this->_current->getParent();
        } elseif (!empty($this->_searchedStoppers[$this->_tag])) {
            // hell has broken loose, these stoppers are searched somewere
            // lower in the tree
            $oldItems = array();

            while (!in_array($this->_tag, $this->_tags[$this->_current->getName()]['stoppers'])) {
                $oldItems[]     = clone $this->_current;
                $this->_current = $this->_current->getParent();
            }

            // ladies and gentlemen... WE GOT HIM!
            $this->_current->setStopper($this->_tag);
            $this->_removeFromSearchedStoppers($this->_current);
            $this->_current = $this->_current->getParent();

            // add those old items again
            foreach (array_reverse($oldItems) as $token) {
                /* @var $token Zend_Markup_Token */
                $this->_current->addChild($token);
                $token->setParent($this->_current);
                $this->_current = $token;
            }
        } elseif ($this->_getType($this->_name) == self::TYPE_SINGLE) {
            $token = new Zend_Markup_Token(
                $this->_tag,
                Zend_Markup_Token::TYPE_TAG,
                $this->_name,
                $attributes,
                $this->_current
            );
            $this->_current->addChild($token);
        } else {
            // add the tag and jump into it
            $token = new Zend_Markup_Token(
                $this->_tag,
                Zend_Markup_Token::TYPE_TAG,
                $this->_name,
                $attributes,
                $this->_current
            );
            $this->_current->addChild($token);
            $this->_current = $token;

            $this->_addToSearchedStoppers($token);
        }
        ++$this->_pointer;
        $this->_attributes = array();
    }

    /**
     * Check the tag's type
     *
     * @param  string $name
     * @return string
     */
    protected function _getType($name)
    {
        // first check if the current tag has a row for this
        if (!isset($this->_tags[$name])) {
            $this->_tags[$name] = array(
                'type'     => self::TYPE_DEFAULT,
                'stoppers' => array(
                    self::TAG_START . '/' . $name . self::TAG_END,
                    self::TAG_START . '/' . self::TAG_END
                )
            );
        }

        return $this->_tags[$name]['type'];
    }

    /**
     * Check if the tag is a stopper
     *
     * @param  string $tag
     * @return bool
     */
    protected function _isStopper($tag, $searched = false)
    {
        // first check if the current tag has registered stoppers
        if (!isset($this->_tags[$this->_current->getName()])) {
            $this->_tags[$this->_current->getName()] = array(
                'type'     => self::TYPE_DEFAULT,
                'stoppers' => array(
                    self::TAG_START . '/' . $this->_current->getName() . self::TAG_END,
                    self::TAG_START . '/' . self::TAG_END
                )
            );
        }

        // and now check if it is a stopper
        $tags = $this->_tags[$this->_current->getName()]['stoppers'];
        if (in_array($tag, $tags)
            || (!empty($this->_searchedStoppers[$this->_tag]) && $searched)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Add to searched stoppers
     *
     * @param  Zend_Markup_Token $token
     * @return void
     */
    protected function _addToSearchedStoppers(Zend_Markup_Token $token)
    {
        if (!isset($this->_tags[$token->getName()])) {
            $this->_tags[$token->getName()] = array(
                'type'     => self::TYPE_DEFAULT,
                'stoppers' => array(
                    self::TAG_START . '/' . $token->getName() . self::TAG_END,
                    self::TAG_START . '/' . self::TAG_END
                )
            );
        }

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
     * @param  Zend_Markup_Token $token
     * @return void
     */
    protected function _removeFromSearchedStoppers(Zend_Markup_Token $token)
    {
        foreach ($this->_tags[$token->getName()]['stoppers'] as $stopper) {
            --$this->_searchedStoppers[$stopper];
        }
    }
}
