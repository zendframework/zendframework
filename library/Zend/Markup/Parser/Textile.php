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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_Parser_Textile implements Zend_Markup_Parser_ParserInterface
{
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
     * Simple tag translation
     *
     * @var array
     */
    protected $_simpleTags = array(
        '*'  => 'strong',
        '**' => 'bold',
        '_'  => 'emphasized',
        '__' => 'italic',
        '??' => 'citation',
        '-'  => 'deleted',
        '+'  => 'insert',
        '^'  => 'superscript',
        '~'  => 'subscript',
        '%'  => 'span',
        // these are a little more complicated
        '@'  => 'code',
        '!'  => 'img',
    );

    /**
     * The list's level
     *
     * @var int
     */
    protected $_listLevel = 0;


    /**
     * Prepare the parsing of a Textile string, the real parsing is done in {@link _parse()}
     *
     * @param  string $value
     * @return array
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
        $this->_value = str_replace(array("\r\n", "\r"), "\n", $value);

        // trim and add a leading LF to make sure that headings on the first line
        // are parsed correctly
        $this->_value = trim($this->_value);

        // initialize variables
        $this->_tree     = new Zend_Markup_TokenList();
        $this->_valueLen = strlen($this->_value);
        $this->_pointer  = 0;
        $this->_buffer   = '';
        $this->_temp     = array();
        $this->_current  = new Zend_Markup_Token('', Zend_Markup_Token::TYPE_NONE, 'Zend_Markup_Root');
        $this->_tree->addChild($this->_current);


        $info = array(
            'tag'        => '',
            'attributes' => array()
        );

        // check if the base paragraph has some extra information
        if ($this->_value[$this->_pointer] == 'p') {
            $this->_pointer++;
            $info = $this->_parseTagInfo('p', '.', true);

            // check if the paragraph definition is correct
            if (substr($this->_value, $this->_pointer, 2) != '. ') {
                $this->_pointer = 0;
            } else {
                $this->_pointer += 2;
            }
        }

        // add the base paragraph
        $paragraph = new Zend_Markup_Token(
            $info['tag'],
            Zend_Markup_Token::TYPE_TAG,
            'p',
            $info['attributes'],
            $this->_current
        );
        $this->_current->addChild($paragraph);
        $this->_current = $paragraph;

        // start the parsing process
        $this->_parse(true);

        return $this->_tree;
    }

    /**
     * Parse a Textile string
     *
     * @return void
     */
    protected function _parse($checkStartTags = false)
    {
        // just keep looping until the parsing is done
        if ($checkStartTags) {
            // check the starter tags (newlines)
            switch ($this->_value[$this->_pointer]) {
                case 'h':
                    $this->_parseHeading();
                    break;
                case '*':
                case '#':
                    $this->_parseList();
                    break;
            }
        }
        while ($this->_pointer < $this->_valueLen) {
            $this->_parseTag();
        }

        $this->_processBuffer();
    }

    /**
     * Parse an inline string
     *
     * @param  string $value
     * @param  Zend_Markup_Token $token
     * @return Zend_Markup_Token
     */
    protected function _parseInline($value, Zend_Markup_Token $token)
    {
        // save the old values
        $oldValue    = $this->_value;
        $oldValueLen = $this->_valueLen;
        $oldPointer  = $this->_pointer;
        $oldTree     = $this->_tree;
        $oldCurrent  = $this->_current;
        $oldBuffer   = $this->_buffer;

        // set the new values
        $this->_value    = $value;
        $this->_valueLen = strlen($value);
        $this->_pointer  = 0;
        $this->_tree     = $token;
        $this->_current  = $token;
        $this->_buffer   = '';

        // parse
        $this->_parse();

        // set the old values
        $this->_value    = $oldValue;
        $this->_valueLen = $oldValueLen;
        $this->_pointer  = $oldPointer;
        $this->_tree     = $oldTree;
        $this->_current  = $oldCurrent;
        $this->_buffer   = $oldBuffer;

        return $token;
    }

    /**
     * Parse a tag
     *
     * @return void
     */
    protected function _parseTag()
    {
        switch ($this->_value[$this->_pointer]) {
            case '*':
            case '_':
            case '?':
            case '-':
            case '+':
            case '^':
            case '~':
            case '%':
                // simple tags like bold, italic
                $this->_parseSimpleTag($this->_value[$this->_pointer]);
                break;
            case '@':
            case '!':
                // simple tags, only they don't have anything inside them
                $this->_parseEmptyTag($this->_value[$this->_pointer]);
                break;
            case '"':
                $this->_parseLink();
                break;
            case '(':
                $this->_parseAcronym();
                break;
            case "\n":
                // this could mean multiple things, let this function check what
                $this->_parseNewline();
                break;
            default:
                // just add this token to the buffer
                $this->_buffer .= $this->_value[$this->_pointer++];
                break;
        }
    }

    /**
     * Parse tag information
     *
     * @todo use $tagEnd property for identation
     * @param  string $tag
     * @param  string $tagEnd
     * @param  bool $block
     * @return array
     */
    protected function _parseTagInfo($tag = '', $tagEnd = '', $block = false)
    {
        $info = array(
            'attributes' => array(),
            'tag'        => $tag
        );

        if ($this->_pointer >= $this->_valueLen) {
            return $info;
        }

        // check which attribute
        switch ($this->_value[$this->_pointer]) {
            case '(':
                // class or id
                $attribute = $this->_parseAttributeEnd(')', $tagEnd);

                if (!$attribute) {
                    break;
                }

                $info['tag'] .= '(' . $attribute . ')';

                if ($attribute[0] == '#') {
                    $info['attributes']['id'] = substr($attribute, 1);
                } elseif (($len = strpos($attribute, '#')) !== false) {
                    $info['attributes']['class'] = substr($attribute, 0, $len);
                    $info['attributes']['id']    = substr($attribute, $len + 1);
                } else {
                    $info['attributes']['class'] = $attribute;
                }

                $this->_pointer++;
                break;
            case '{':
                // style
                $attribute = $this->_parseAttributeEnd('}', $tagEnd);

                if (!$attribute) {
                    break;
                }

                $info['tag'] .= '{' . $attribute . '}';

                $info['attributes']['style'] = $attribute;

                $this->_pointer++;
                break;
            case '[':
                // style
                $attribute = $this->_parseAttributeEnd(']', $tagEnd);

                if (!$attribute) {
                    break;
                }

                $info['tag'] .= '[' . $attribute . ']';

                $info['attributes']['lang'] = $attribute;

                $this->_pointer++;
                break;
            case '<':
                if ($block) {
                    $info['tag'] .= '<';
                    if (($this->_value[++$this->_pointer] == '>')) {
                        $info['attributes']['align'] = 'justify';
                        $info['tag'] .= '>';
                        $this->_pointer++;
                    } else {
                        $info['attributes']['align'] = 'left';
                    }
                }
                break;
            case '>':
                if ($block) {
                    $info['attributes']['align'] = 'right';
                    $info['tag'] .= '>';
                    $this->_pointer++;
                }
                break;
            case '=':
                if ($block) {
                    $info['attributes']['align'] = 'center';
                    $info['tag'] .= '=';
                    $this->_pointer++;
                }
                break;
            default:
                // simply do nothing, there are no attributes
                break;
        }

        return $info;
    }

    /**
     * Parse the attribute's end
     *
     * @todo use $tagEnd property for indentation
     * @param  string $endToken
     * @param  string $tagEnd
     * @param  bool $block
     * @return string|bool
     */
    protected function _parseAttributeEnd($endToken, $tagEnd = '', $block = false)
    {
        $value = '';

        $oldPointer = $this->_pointer;

        while ($this->_pointer < $this->_valueLen) {
            if ($this->_pointer + 1 >= $this->_valueLen) {
                $this->_pointer = $oldPointer;
                return false;
            }
            if ($this->_value[++$this->_pointer] == $endToken) {
                return $value;
            }
            $value .= $this->_value[$this->_pointer];
        }
    }

    /**
     * Parse a simple markup tag
     *
     * @param  string $tag
     * @return void
     */
    protected function _parseSimpleTag($tag)
    {
        if (++$this->_pointer >= $this->_valueLen) {
	        // could be a stopper
	        if ($this->_isSimpleStopper($tag)) {
	            $this->_processBuffer();
	            $this->_current->setStopper($tag);
	            $this->_current = $this->_current->getParent();
	        } else {
	        	$this->_buffer .= $tag;
	        }
            return;
        }

        if ($this->_value[$this->_pointer] == $tag) {
            $tag = $tag . $tag;
            $this->_pointer++;
        }

        // check if this is a stopper
        if ($this->_isSimpleStopper($tag)) {
            $this->_processBuffer();
            $this->_current->setStopper($tag);
            $this->_current = $this->_current->getParent();
            return;
        }

        // check if this is a tag
        if (isset($this->_simpleTags[$tag])) {
            $name = $this->_simpleTags[$tag];

            // process the buffer and add the tag
            $this->_processBuffer();

            // parse a possible attribute
            $info = $this->_parseTagInfo($tag);

            $token = new Zend_Markup_Token(
                $info['tag'],
                Zend_Markup_Token::TYPE_TAG,
                $name,
                $info['attributes'],
                $this->_current
            );
            $this->_current->addChild($token);
            $this->_current = $token;
        } else {
            $this->_buffer .= $tag;
        }
    }

    /**
     * Parse an 'empty' markup tag
     *
     * @todo implement support for attributes
     * @param  string $tag
     * @return void
     */
    protected function _parseEmptyTag($tag)
    {
        if (!isset($this->_simpleTags[$tag])) {
            $this->_buffer .= $tag;
            return;
        }

        // add the tag
        $this->_processBuffer();

        $this->_pointer++;

        $info = $this->_parseTagInfo($tag);

        $name = $this->_simpleTags[$tag];

        $token = new Zend_Markup_Token(
            $info['tag'],
            Zend_Markup_Token::TYPE_TAG,
            $name,
            $info['attributes'],
            $this->_current
        );
        $this->_current->addChild($token);
        $this->_current = $token;

        // find the stopper
        while ($this->_valueLen > $this->_pointer) {
            if ($this->_value[$this->_pointer] == $tag) {
                // found the stopper, set it and return
                $this->_pointer++;

                $this->_processBuffer();
                $this->_current->setStopper($tag);
                $this->_current = $this->_current->getParent();
                return;
            } else {
                // not yet found, add the character to the buffer and go to the next one
                $this->_buffer .= $this->_value[$this->_pointer++];
            }
        }
    }

    /**
     * Parse a link
     *
     * @return void
     */
    protected function _parseLink()
    {
        // first find the other "
        $len  = strcspn($this->_value, '"', ++$this->_pointer);
        $text = substr($this->_value, $this->_pointer, $len);

        // not a link tag
        if (($this->_pointer + $len >= $this->_valueLen) || ($this->_value[$this->_pointer + $len++] != '"')) {
            $this->_buffer  .= '"' . $text;
            $this->_pointer += $len;
            return;
        }
        // not a link tag
        if (($this->_pointer + $len >= $this->_valueLen) || ($this->_value[$this->_pointer + $len++] != ':')) {
            $this->_buffer  .= '"' . $text . '"';
            $this->_pointer += $len;
            return;
        }

        // update the pointer
        $this->_pointer += $len;

        // now, get the URL
        $len = strcspn($this->_value, "\n\t ", $this->_pointer);
        $url = substr($this->_value, $this->_pointer, $len);

        $this->_pointer += $len;

        // gather the attributes
        $attributes = array(
            'url' => $url,
        );

        // add the tag
        $this->_processBuffer();
        $token = new Zend_Markup_Token(
            '"',
            Zend_Markup_Token::TYPE_TAG,
            'url',
            $attributes,
            $this->_current
        );
        $token->addChild(new Zend_Markup_Token(
            $text,
            Zend_Markup_Token::TYPE_NONE,
            '',
            array(),
            $token
        ));
        $token->setStopper('":');

        $this->_current->addChild($token);
    }

    /**
     * Parse a newline
     *
     * A newline could mean multiple things:
     * - Heading {@link _parseHeading()}
     * - List {@link _parseList()}
     * - Paragraph {@link _parseParagraph()}
     *
     * @return void
     */
    protected function _parseNewline()
    {
        if (!empty($this->_buffer) && ($this->_buffer[strlen($this->_buffer) - 1] == "\n")) {
            $this->_parseParagraph();
        } else {
            switch ($this->_value[++$this->_pointer]) {
                case 'h':
                    $this->_parseHeading();
                    break;
                case '*':
                case '#':
                    $this->_parseList();
                    break;
                default:
                    $this->_buffer .= "\n";
                    break;
            }
        }
    }

    /**
     * Parse a paragraph declaration
     *
     * @return void
     */
    protected function _parseParagraph()
    {
        // remove the newline from the buffer and increase the pointer
        $this->_buffer = substr($this->_buffer, 0, -1);
        $this->_pointer++;

        // check if we are in the current paragraph
        if ($this->_current->getName() == 'p') {
            $this->_processBuffer();

            $this->_current->setStopper("\n");
            $this->_current = $this->_current->getParent();

            $info = array(
                'tag'        => "\n",
                'attributes' => array()
            );

            $oldPointer = $this->_pointer;

            if ($this->_value[$this->_pointer] == 'p') {
                $this->_pointer++;
                $info = $this->_parseTagInfo("\np", '.', true);

                if (substr($this->_value, $this->_pointer, 2) == '. ') {
                    $this->_pointer += 2;
                } else {
                    // incorrect declaration of paragraph, reset the pointer and use default info
                    $this->_pointer = $oldPointer;
                    $info = array(
                        'tag'        => "\n",
                        'attributes' => array()
                    );
                }
            }

            // create a new one and jump onto it
            $paragraph = new Zend_Markup_Token(
                $info['tag'],
                Zend_Markup_Token::TYPE_TAG,
                'p',
                $info['attributes'],
                $this->_current
            );
            $this->_current->addChild($paragraph);
            $this->_current = $paragraph;
        } else {
            /**
             * @todo Go down in the tree until you find the paragraph
             * while remembering every step. After that, close the
             * paragraph, add a new one and climb back up by re-adding
             * every step
             */
        }
    }

    /**
     * Parse a heading
     *
     * @todo implement support for attributes
     * @return void
     */
    protected function _parseHeading()
    {
        // check if it is a valid heading
        if (in_array($this->_value[++$this->_pointer], range(1, 6))) {
            $name = 'h' . $this->_value[$this->_pointer++];

            $info = $this->_parseTagInfo($name, '.', true);

            // now, the next char should be a dot
            if ($this->_value[$this->_pointer] == '.') {
                $info['tag'] .= '.';

                // add the tag
                $this->_processBuffer();

                $token = new Zend_Markup_Token(
                    $info['tag'],
                    Zend_Markup_Token::TYPE_TAG,
                    $name,
                    $info['attributes'],
                    $this->_current
                );
                $this->_current->addChild($token);
                $this->_current = $token;

                if ($this->_value[++$this->_pointer] != ' ') {
                    $this->_buffer .= $this->_value[$this->_pointer];
                }

                // find the end
                $len = strcspn($this->_value, "\n", ++$this->_pointer);

                $this->_buffer.= substr($this->_value, $this->_pointer, $len);

                $this->_pointer += $len;

                // end the tag and return
                $this->_processBuffer();
                $this->_current = $this->_current->getParent();

                return;
            }
            $this->_buffer .= "\n" . $name;
            return;
        }

        // not a valid heading
        $this->_buffer .= "\nh";
    }

    /**
     * Parse a list
     *
     * @todo allow a deeper list level
     * @todo add support for markup inside the list items
     * @return void
     */
    protected function _parseList()
    {
        // for this operation, we need the entire line
        $len  = strcspn($this->_value, "\n", $this->_pointer);
        $line = substr($this->_value, $this->_pointer, $len);

        // add the list tag
        $this->_processBuffer();

        // maybe we have to rewind
        $oldPointer = $this->_pointer;

        // attributes array
        $attrs = array();
        if ($line[0] == '#') {
            $attrs['list'] = 'decimal';
        }

        if ((strlen($line) <= 1) || ($line[1] != ' ')) {
            // rewind and return
            unset($list);
            $this->_pointer = $oldPointer;

            return;
        }

        // add the token
        $list = new Zend_Markup_Token('', Zend_Markup_Token::TYPE_TAG, 'list', $attrs, $this->_current);

        // loop through every next line, until there are no list items any more
        while ($this->_valueLen > $this->_pointer) {
            // add the li-tag with contents
            $item = new Zend_Markup_Token(
                $line[0],
                Zend_Markup_Token::TYPE_TAG,
                'li',
                array(),
                $list
            );

            // parse and add the content
            $this->_parseInline(substr($line, 2), $item);

            $list->addChild($item);

            $this->_pointer += $len;

            // check if the next line is a list item too
            if (($this->_pointer + 1 >= $this->_valueLen) || $this->_value[++$this->_pointer] != $line[0]) {
                // there is no new list item coming
                break;
            }

            // get the next line
            $len  = strcspn($this->_value, "\n", $this->_pointer);
            $line = substr($this->_value, $this->_pointer, $len);
        }

        // end the list tag
        $this->_current->addChild($list);
        $this->_current = $this->_current->getParent();
    }

    /**
     * Parse an acronym
     *
     * @return void
     */
    protected function _parseAcronym()
    {
        $this->_pointer++;

        // first find the acronym itself
        $acronym = '';
        $pointer = 0;

        if (empty($this->_buffer)) {
            $this->_buffer .= '(';
            return;
        }

        $bufferLen = strlen($this->_buffer);

        while (($bufferLen > $pointer) && ctype_upper($this->_buffer[$bufferLen - ++$pointer])) {
            $acronym = $this->_buffer[strlen($this->_buffer) - $pointer] . $acronym;
        }

        if (strlen($acronym) < 3) {
            // just add the '(' to the buffer, this isn't an acronym
            $this->_buffer .= '(';
            return;
        }

        // now, find the closing ')'
        $title = '';
        while ($this->_pointer < $this->_valueLen) {
            if ($this->_value[$this->_pointer] == ')') {
                break;
            } else {
                $title .= $this->_value[$this->_pointer];
            }
            $this->_pointer++;
        }

        if ($this->_pointer >= $this->_valueLen) {
            $this->_buffer .= '(';
            return;
        }

        $this->_pointer++;

        if (empty($title)) {
            $this->_buffer .= '()';
            return;
        }

        $this->_buffer = substr($this->_buffer, 0, -$this->_pointer);
        $this->_processBuffer();

        // now add the tag
        $token = new Zend_Markup_Token(
            '',
            Zend_Markup_Token::TYPE_TAG,
            'acronym',
            array('title' => $title),
            $this->_current
        );
        $token->setStopper('(' . $title . ')');
        $token->addChild(new Zend_Markup_Token(
            $acronym,
            Zend_Markup_Token::TYPE_NONE,
            '',
            array(),
            $token
        ));
        $this->_current->addChild($token);
    }

    /**
     * Check if the tag is a simple stopper
     *
     * @param  string $tag
     * @return bool
     */
    protected function _isSimpleStopper($tag)
    {
        if ($tag == substr($this->_current->getTag(), 0, strlen($tag))) {
            return true;
        }
        return false;
    }

    /**
     * Process the current buffer
     *
     * @return void
     */
    protected function _processBuffer()
    {
        if (!empty($this->_buffer)) {
            // no tag start found, add the buffer to the current tag and stop parsing
            $token = new Zend_Markup_Token(
                $this->_buffer,
                Zend_Markup_Token::TYPE_NONE,
                '',
                array(),
                $this->_current
            );
            $this->_current->addChild($token);
            $this->_buffer  = '';
        }
    }
}