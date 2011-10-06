<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\NameInformation,
    Zend\Code\Annotation\AnnotationManager;

class DocBlockScanner implements Scanner
{
    /**
     * @var bool
     */
    protected $isScanned = false;

    /**
     * @var string
     */
    protected $docComment = null;

    /**
     * @var NameInformation
     */
    protected $nameInformation = null;

    /**
     * @var AnnotationManager
     */
    protected $annotationManager = null;

    /**
     * @var string
     */
    protected $shortDescription = null;

    /**
     * @var string
     */
    protected $longDescription = '';

    /**
     * @var array[]
     */
    protected $tags = array();

    /**
     * @var Annotation[]
     */
    protected $annotations = array();

    /**
     * @param string $docComment
     * @param AnnotationManager $annotationManager
     */
    public function __construct($docComment, NameInformation $nameInformation = null)
    {
        $this->docComment = $docComment;
        $this->nameInformation = $nameInformation;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        $this->scan();
        return $this->shortDescription;
    }

    /**
     * @return string
     */
    public function getLongDescription()
    {
        $this->scan();
        return $this->longDescription;
    }

    /**
     * @return array[]
     */
    public function getTags()
    {
        $this->scan();
        return $this->tags;
    }

    public function getAnnotations()
    {
        $this->scan();
        return $this->annotations;
    }

    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }

        $tokens = $this->tokenize();
        $tagIndex = null;
        /*
        $currentAnnotationName = null;
        $currentAnnotationValue = '';
        */

        reset($tokens);

        SCANNER_TOP:
            $token = current($tokens);

            switch ($token[0]) {

                case 'DOCBLOCK_NEWLINE':
                    if ($this->shortDescription === null) {
                        $this->shortDescription = '';
                    }
                    goto SCANNER_CONTINUE;

                case 'DOCBKOCK_WHITESPACE':
                    /*
                    if ($currentAnnotationName === null) {
                        goto SCANNER_CONTINUE;
                    }
                    */
                /*
                case 'DOCBLOCK_ANNOTATION_VALUE':
                    if ($currentAnnotationName === null) {
                        goto SCANNER_CONTINUE;
                    }
                    $currentAnnotationValue .= $token[1];
                    goto SCANNER_CONTINUE;
                */
                case 'DOCBLOCK_TAG':
                    array_push($this->tags, array('name' => $token[1], 'value' => ''));
                    end($this->tags);
                    $tagIndex = key($this->tags);
                    /*
                    if (!$this->annotationManager || !$this->annotationManager->hasAnnotationName(ltrim($token[1], '@'))) {
                        goto SCANNER_CONTINUE;
                    }
                    */
                /*
                case 'DOCBLOCK_ANNOTATION_NAME':
                    if ($currentAnnotationName !== null) {
                        $this->annotations[] = $this->annotationManager->createAnnotation($currentAnnotationName, $currentAnnotationValue);
                        $currentAnnotationName = $currentAnnotationValue = null;
                    }

                    $currentAnnotationName = ltrim($token[1], '@');
                    if (!$this->annotationManager->hasAnnotationName($currentAnnotationName)) {
                        $currentAnnotationName = null;
                    }
                    goto SCANNER_CONTINUE;
                */
                case 'DOCBLOCK_TEXT':
                    if ($tagIndex !== null) {
                        $this->tags[$tagIndex]['value'] .= ($this->tags[$tagIndex]['value'] == '') ? $token[1] : ' ' . $token[1];
                    } elseif ($this->shortDescription !== null) {
                        if ($this->shortDescription === '') {
                            $this->shortDescription = $token[1];
                        } else {
                            $this->longDescription .= $token[1];
                        }
                    }
                    goto SCANNER_CONTINUE;

                case 'DOCBLOCK_COMMENTEND':
                    goto SCANNER_END;

            }

        SCANNER_CONTINUE:
            if (next($tokens) === false) {
                goto SCANNER_END;
            }
            goto SCANNER_TOP;

        SCANNER_END:

            /*
            if ($currentAnnotationName !== null) {
                $this->annotations[] = $this->annotationManager->createAnnotation($currentAnnotationName, $currentAnnotationValue);
            }
            */

        $this->shortDescription = rtrim($this->shortDescription);
        $this->longDescription  = rtrim($this->longDescription);
        $this->isScanned = true;
    }

    protected function tokenize()
    {
        static $CONTEXT_INSIDE_DOCBLOCK = 0x01;
        static $CONTEXT_INSIDE_ASTERISK = 0x02;
//        static $CONTEXT_INSIDE_ANNOTATION = 0x04;

        $context = 0x00;
        $stream = $this->docComment;
        $streamIndex = null;
        $tokens = array();
        $tokenIndex = null;
        $currentChar = null;
        $currentWord = null;
        $currentLine = null;

//        $annotationMode = (isset($this->annotationManager));
//        $annotationParenCount = 0;


        $MACRO_STREAM_ADVANCE_CHAR = function ($positionsForward = 1) use (&$stream, &$streamIndex, &$currentChar, &$currentWord, &$currentLine, &$annotationMode) {
            $positionsForward = ($positionsForward > 0) ? $positionsForward : 1;
            $streamIndex = ($streamIndex === null) ? 0 : $streamIndex+$positionsForward;
            if (!isset($stream[$streamIndex])) {
                $currentChar = false;
                return false;
            }
            $currentChar = $stream[$streamIndex];
            $matches = array();
            $currentLine = (preg_match('#(.*)\n#', $stream, $matches, null, $streamIndex) === 1) ? $matches[1] : substr($stream, $streamIndex);
            if ($currentChar === ' ') {
                $currentWord = (preg_match('#( +)#', $currentLine, $matches) === 1) ? $matches[1] : $currentLine;
            } else {
                if ($annotationMode) {
                    $currentWord = (($matches = strpos($currentLine, ' ')) !== false) ? substr($currentLine, 0, $matches) : $currentLine;
                } else {
                    $currentWord = strtok($currentLine, " \n\t\r");
                }
            }
            return $currentChar;
        };
        $MACRO_STREAM_ADVANCE_WORD = function () use (&$currentWord, &$MACRO_STREAM_ADVANCE_CHAR) {
            return $MACRO_STREAM_ADVANCE_CHAR(strlen($currentWord));
        };
        $MACRO_STREAM_ADVANCE_LINE = function () use (&$currentLine, &$MACRO_STREAM_ADVANCE_CHAR) {
            return $MACRO_STREAM_ADVANCE_CHAR(strlen($currentLine));
        };
        $MACRO_TOKEN_ADVANCE = function () use (&$tokenIndex, &$tokens) {
            $tokenIndex = ($tokenIndex === null) ? 0 : $tokenIndex+1;
            $tokens[$tokenIndex] = array('DOCBLOCK_UNKNOWN', '');
        };
        $MACRO_TOKEN_SET_TYPE = function ($type) use (&$tokenIndex, &$tokens) {
            $tokens[$tokenIndex][0] = $type;
        };
        $MACRO_TOKEN_APPEND_CHAR = function () use (&$currentChar, &$tokens, &$tokenIndex) {
            $tokens[$tokenIndex][1] .= $currentChar;
        };
        $MACRO_TOKEN_APPEND_WORD = function () use (&$currentWord, &$tokens, &$tokenIndex) {
            $tokens[$tokenIndex][1] .= $currentWord;
        };
        $MACRO_TOKEN_APPEND_WORD_PARTIAL = function ($length) use (&$currentWord, &$tokens, &$tokenIndex) {
            $tokens[$tokenIndex][1] .= substr($currentWord, 0, $length);
        };
        $MACRO_TOKEN_APPEND_LINE = function () use (&$currentLine, &$tokens, &$tokenIndex) {
            $tokens[$tokenIndex][1] .= $currentLine;
        };

        $MACRO_STREAM_ADVANCE_CHAR();
        $MACRO_TOKEN_ADVANCE();

        TOKENIZER_TOP:

            if ($context === 0x00 && $currentChar === '/' && $currentWord === '/**') {
                $MACRO_TOKEN_SET_TYPE('DOCBLOCK_COMMENTSTART');
                $MACRO_TOKEN_APPEND_WORD();
                $MACRO_TOKEN_ADVANCE();
                $context |= $CONTEXT_INSIDE_DOCBLOCK;
                $context |= $CONTEXT_INSIDE_ASTERISK;
                if ($MACRO_STREAM_ADVANCE_WORD() === false) {
                    goto TOKENIZER_END;
                }
                goto TOKENIZER_TOP;
            }

            if ($context & $CONTEXT_INSIDE_DOCBLOCK && $currentWord === '*/') {
                $MACRO_TOKEN_SET_TYPE('DOCBLOCK_COMMENTEND');
                $MACRO_TOKEN_APPEND_WORD();
                $MACRO_TOKEN_ADVANCE();
                $context &= ~$CONTEXT_INSIDE_DOCBLOCK;
                if ($MACRO_STREAM_ADVANCE_WORD() === false) {
                    goto TOKENIZER_END;
                }
                goto TOKENIZER_TOP;
            }

            if ($currentChar === ' ') {
                $MACRO_TOKEN_SET_TYPE(($context & $CONTEXT_INSIDE_ASTERISK) ? 'DOCBLOCK_WHITESPACE' : 'DOCBLOCK_WHITESPACE_INDENT');
                $MACRO_TOKEN_APPEND_WORD();
                $MACRO_TOKEN_ADVANCE();
                if ($MACRO_STREAM_ADVANCE_WORD() === false) {
                    goto TOKENIZER_END;
                }
                goto TOKENIZER_TOP;
            }

            if ($currentChar === '*') {
                if (($context & $CONTEXT_INSIDE_DOCBLOCK) && ($context & $CONTEXT_INSIDE_ASTERISK)) {
                    $MACRO_TOKEN_SET_TYPE('DOCBLOCK_TEXT');
                } else {
                    $MACRO_TOKEN_SET_TYPE('DOCBLOCK_ASTERISK');
                    $context |= $CONTEXT_INSIDE_ASTERISK;
                }
                $MACRO_TOKEN_APPEND_CHAR();
                $MACRO_TOKEN_ADVANCE();
                if ($MACRO_STREAM_ADVANCE_CHAR() === false) {
                    goto TOKENIZER_END;
                }
                goto TOKENIZER_TOP;
            }

            /*
            if ($currentChar === '@' && $annotationMode && ($startOfAnnotation = strpos($currentWord, '(')) !== false) {
                $MACRO_TOKEN_SET_TYPE('DOCBLOCK_ANNOTATION_NAME');
                $MACRO_TOKEN_APPEND_WORD_PARTIAL($startOfAnnotation);
                $MACRO_TOKEN_ADVANCE();
                $context |= $CONTEXT_INSIDE_ANNOTATION;
                if ($MACRO_STREAM_ADVANCE_CHAR($startOfAnnotation) === false) {
                    goto TOKENIZER_END;
                }
                goto TOKENIZER_TOP;
            }
            */

            /*
            if ($annotationMode && ($context && $CONTEXT_INSIDE_ANNOTATION) && ($context && $CONTEXT_INSIDE_ASTERISK)) {
                $MACRO_TOKEN_SET_TYPE('DOCBLOCK_ANNOTATION_VALUE');
                $MACRO_TOKEN_APPEND_WORD();
                $MACRO_TOKEN_ADVANCE();
                $annotationParenCount += substr_count($currentWord, '(') - substr_count($currentWord, ')');
                if ($annotationParenCount === 0) {
                    $context &= ~$CONTEXT_INSIDE_ANNOTATION;
                }
                if ($MACRO_STREAM_ADVANCE_WORD() === false) {
                    goto TOKENIZER_END;
                }
                goto TOKENIZER_TOP;
            }
            */

            if ($currentChar === '@') {
                $MACRO_TOKEN_SET_TYPE('DOCBLOCK_TAG');
                $MACRO_TOKEN_APPEND_WORD();
                $MACRO_TOKEN_ADVANCE();
                if ($MACRO_STREAM_ADVANCE_WORD() === false) {
                    goto TOKENIZER_END;
                }
                goto TOKENIZER_TOP;
            }

            if ($currentChar === "\n") {
                $MACRO_TOKEN_SET_TYPE('DOCBLOCK_NEWLINE');
                $MACRO_TOKEN_APPEND_CHAR();
                $MACRO_TOKEN_ADVANCE();
                $context &= ~$CONTEXT_INSIDE_ASTERISK;
                if ($MACRO_STREAM_ADVANCE_CHAR() === false) {
                    goto TOKENIZER_END;
                }
                goto TOKENIZER_TOP;
            }

            $MACRO_TOKEN_SET_TYPE('DOCBLOCK_TEXT');
            $MACRO_TOKEN_APPEND_LINE();
            $MACRO_TOKEN_ADVANCE();
            if ($MACRO_STREAM_ADVANCE_LINE() === false) {
                goto TOKENIZER_END;
            }
            goto TOKENIZER_TOP;

        TOKENIZER_END:

            array_pop($tokens);

        return $tokens;
    }
}
