<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\NameInformation,
    Zend\Code\Exception,
    Zend\Code\Annotation\AnnotationManager;

class TokenArrayScanner implements Scanner
{
    /**
     * @var bool
     */
    protected $isScanned = false;

    /**
     * @var array
     */
    protected $tokens = array();

    /**
     * @var null
     */
    protected $docComment = null;

    /**
     * @var NameInformation
     */
    protected $nameInformation = null;

    /**
     * @var array
     */
    protected $infos = array();

    /**
     * @var AnnotationManager
     */
    protected $annotationManager = null;

    /**
     * @param null|array $tokens
     */
    public function __construct($tokens = null, AnnotationManager $annotationManager = null)
    {
        if ($tokens) {
            $this->setTokens($tokens);
        }
    }

    public function reset()
    {
        $this->isScanned = false;
        $this->infos     = array();
    }
    
    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
        $this->reset();
    }

    public function getAnnotationManager()
    {
        return $this->annotationManager;
    }

    public function setAnnotationManager(AnnotationManager $annotationManager)
    {
        $this->annotationManager = $annotationManager;
    }

    public function getDocComment()
    {
        return null;
    }

    public function getNamespaces()
    {
        $this->scan();

        $namespaces = array();
        foreach ($this->infos as $info) {
            if ($info['type'] == 'namespace') {
                $namespaces[] = $info['namespace'];
            }
        }
        return $namespaces;
    }
    
    public function getUses($namespace = null)
    {
        $this->scan();
        return $this->getNamespaceUsesNoScan($namespace);
    }
    
    public function getIncludes()
    {
        $this->scan();
        // @todo Implement getIncludes() in TokenArrayScanner
    }
    
    public function getClasses($returnScannerClass = false)
    {
        $this->scan();
        
        $return = array();
        
        foreach ($this->infos as $info) {
            if ($info['type'] != 'class') {
                continue;
            }

            if (!$returnScannerClass) {
                $return[] = $info['name'];
            } else {
                $return[] = $this->getClass($info['name'], $returnScannerClass);
            }
        }
        return $return;
    }
    
    /**
     * 
     * Enter description here ...
     * @param string|int $classNameOrInfoIndex
     * @return ClassScanner
     */
    public function getClass($classNameOrInfoIndex)
    {
        $this->scan();

        if (is_int($classNameOrInfoIndex)) {
            $info = $this->infos[$classNameOrInfoIndex];
            if ($info['type'] != 'class') {
                throw new Exception\InvalidArgumentException('Index of info offset is not about a class');
            }
        } elseif (is_string($classNameOrInfoIndex)) {
            $classFound = false;
            foreach ($this->infos as $infoIndex => $info) {
                if ($info['type'] === 'class' && $info['name'] === $classNameOrInfoIndex) {
                    $classFound = true;
                    break;
                }
            }
            if (!$classFound) {
                return false;
            }
        }

        $uses = array();
        foreach ($info['uses'] as $useStatement) {
            if ($useStatement['as'] === null) {
                $uses[] = $useStatement['use'];
            } else {
                $uses[$useStatement['use']] = $useStatement['as'];
            }
        }

        return new ClassScanner(
            array_slice(
                $this->tokens, 
                $info['tokenStart'], 
                ($info['tokenEnd'] - $info['tokenStart'] + 1)
            ), // zero indexed array
            new NameInformation($info['namespace'], $uses)
        );
    }

    public function getClassNameInformation($className)
    {
        $this->scan();

        $classFound = false;
        foreach ($this->infos as $infoIndex => $info) {
            if ($info['type'] === 'class' && $info['name'] === $className) {
                $classFound = true;
                break;
            }
        }
        if (!$classFound) {
            return false;
        }


        $uses = array();
        foreach ($info['uses'] as $useStatement) {
            if ($useStatement['as'] === null) {
                $uses[] = $useStatement['use'];
            } else {
                $uses[$useStatement['use']] = $useStatement['as'];
            }
        }

        return new NameInformation($info['namespace'], $uses);
    }

    public function getFunctions($returnInfo = false)
    {
        $this->scan();
        
        if (!$returnInfo) {
            $functions = array();
            foreach ($this->infos as $info) {
                if ($info['type'] == 'function') {
                    $functions[] = $info['name'];
                }
            }
            return $functions;
        } else {
            $scannerClass = new FunctionScanner();
            // @todo
            return $scannerClass;
        }
    }

    public static function export($tokens)
    {
        // @todo
    }

    public function __toString()
    {
        // @todo
    }

    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }

        if (!$this->tokens) {
            throw new Exception\RuntimeException('No tokens were provided');
        }

        /**
         * Variables & Setup
         */

        $tokens          = &$this->tokens; // localize
        $infos           = &$this->infos;  // localize
        $tokenIndex      = null;
        $token           = null;
        $tokenType       = null;
        $tokenContent    = null;
        $tokenLine       = null;
        $namespace       = null;
        $docCommentIndex = false;
        $infoIndex       = 0;

        /**
         * MACRO creation
         */

        $MACRO_TOKEN_ADVANCE = function() use (&$tokens, &$tokenIndex, &$token, &$tokenType, &$tokenContent, &$tokenLine) {
            $tokenIndex = ($tokenIndex === null) ? 0 : $tokenIndex+1;
            if (!isset($tokens[$tokenIndex])) {
                $token        = false;
                $tokenContent = false;
                $tokenType    = false;
                $tokenLine    = false;
                return false;
            }
            if (is_string($tokens[$tokenIndex]) && $tokens[$tokenIndex] === '"') {
                do {
                    $tokenIndex++;
                } while (!(is_string($tokens[$tokenIndex]) && $tokens[$tokenIndex] === '"'));
            }
            $token = $tokens[$tokenIndex];
            if (is_array($token)) {
                list($tokenType, $tokenContent, $tokenLine) = $token;
            } else {
                $tokenType = null;
                $tokenContent = $token;
            }
            return $tokenIndex;
        };
        $MACRO_TOKEN_LOGICAL_START_INDEX = function() use (&$tokenIndex, &$docCommentIndex) {;
            return ($docCommentIndex === false) ? $tokenIndex : $docCommentIndex;
        };
        $MACRO_DOC_COMMENT_START = function() use (&$tokenIndex, &$docCommentIndex) {
            $docCommentIndex = $tokenIndex;
            return $docCommentIndex;
        };
        $MACRO_DOC_COMMENT_VALIDATE = function() use (&$tokenType, &$docCommentIndex) {
            static $validTrailingTokens = null;
            if ($validTrailingTokens === null) {
                $validTrailingTokens = array(T_WHITESPACE, T_FINAL, T_ABSTRACT, T_INTERFACE, T_CLASS, T_FUNCTION);
            }
            if ($docCommentIndex !== false && !in_array($tokenType, $validTrailingTokens)) {
                $docCommentIndex = false;
            }
            return $docCommentIndex;
        };
        $MACRO_INFO_ADVANCE = function() use (&$infoIndex, &$infos, &$tokenIndex, &$tokenLine) {
            $infos[$infoIndex]['tokenEnd'] = $tokenIndex;
            $infos[$infoIndex]['lineEnd'] = $tokenLine;
            $infoIndex++;
            return $infoIndex;
        };

        /**
         * START FINITE STATE MACHINE FOR SCANNING TOKENS
         */

        // Initialize token
        $MACRO_TOKEN_ADVANCE();

        SCANNER_TOP:

            if ($token === false) {
                goto SCANNER_END;
            }

            // Validate current doc comment index
            $MACRO_DOC_COMMENT_VALIDATE();

            switch ($tokenType) {

                case T_DOC_COMMENT:

                    $MACRO_DOC_COMMENT_START();
                    goto SCANNER_CONTINUE;

                case T_NAMESPACE:

                    $infos[$infoIndex] = array(
                        'type'       => 'namespace',
                        'tokenStart' => $MACRO_TOKEN_LOGICAL_START_INDEX(),
                        'tokenEnd'   => null,
                        'lineStart'  => $token[2],
                        'lineEnd'    => null,
                        'namespace'  => null,
                    );

                    // start processing with next token
                    if ($MACRO_TOKEN_ADVANCE() === false) {
                        goto SCANNER_END;
                    }

                    SCANNER_NAMESPACE_TOP:

                        if ($tokenType === null && $tokenContent === ';' || $tokenContent === '{') {
                            goto SCANNER_NAMESPACE_END;
                        }

                        if ($tokenType === T_WHITESPACE) {
                            goto SCANNER_NAMESPACE_CONTINUE;
                        }
                        
                        if ($tokenType === T_NS_SEPARATOR || $tokenType === T_STRING) {
                            $infos[$infoIndex]['namespace'] .= $tokenContent;
                        }

                    SCANNER_NAMESPACE_CONTINUE:

                        if ($MACRO_TOKEN_ADVANCE() === false) {
                            goto SCANNER_END;
                        }
                        goto SCANNER_NAMESPACE_TOP;

                    SCANNER_NAMESPACE_END:

                        if ($infos[$infoIndex]['namespace'] === null) {
                            $infos[$infoIndex]['namespace'] = '-GLOBAL-';
                        }
                        $namespace = $infos[$infoIndex]['namespace'];

                        $MACRO_INFO_ADVANCE();
                        goto SCANNER_CONTINUE;

                case T_USE:

                    $infos[$infoIndex] = array(
                        'type'       => 'use',
                        'tokenStart' => $MACRO_TOKEN_LOGICAL_START_INDEX(),
                        'tokenEnd'   => null,
                        'lineStart'  => $tokens[$tokenIndex][2],
                        'lineEnd'    => null,
                        'namespace'  => $namespace,
                        'statements' => array(0 => array('use' => null, 'as' => null)),
                    );

                    $useStatementIndex = 0;
                    $useAsContext = false;

                    // start processing with next token
                    if ($MACRO_TOKEN_ADVANCE() === false) {
                        goto SCANNER_END;
                    }

                    SCANNER_USE_TOP:

                        if ($tokenType === null) {
                            if ($tokenContent === ';') {
                                goto SCANNER_USE_END;
                            } elseif ($tokenContent === ',') {
                                $useAsContext = false;
                                $useStatementIndex++;
                                $infos[$infoIndex]['statements'][$useStatementIndex] = array('use' => null, 'as' => null);
                            }
                        }

                        // ANALYZE
                        if ($tokenType !== null) {

                            if ($tokenType == T_AS) {
                                $useAsContext = true;
                                goto SCANNER_USE_CONTINUE;
                            }

                            if ($tokenType == T_NS_SEPARATOR || $tokenType == T_STRING) {
                                if ($useAsContext == false) {
                                    $infos[$infoIndex]['statements'][$useStatementIndex]['use'] .= $tokenContent;
                                } else {
                                    $infos[$infoIndex]['statements'][$useStatementIndex]['as'] = $tokenContent;
                                }
                            }

                        }

                    SCANNER_USE_CONTINUE:

                        if ($MACRO_TOKEN_ADVANCE() === false) {
                            goto SCANNER_END;
                        }
                        goto SCANNER_USE_TOP;

                    SCANNER_USE_END:



                        $MACRO_INFO_ADVANCE();
                        goto SCANNER_CONTINUE;

                case T_INCLUDE:
                case T_INCLUDE_ONCE:
                case T_REQUIRE:
                case T_REQUIRE_ONCE:

                    // Static for performance
                    static $includeTypes = array(
                        T_INCLUDE      => 'include',
                        T_INCLUDE_ONCE => 'include_once',
                        T_REQUIRE      => 'require',
                        T_REQUIRE_ONCE => 'require_once'
                    );

                    $infos[$infoIndex] = array(
                        'type'        => 'include',
                        'tokenStart'  => $MACRO_TOKEN_LOGICAL_START_INDEX(),
                        'tokenEnd'    => null,
                        'lineStart'   => $tokens[$tokenIndex][2],
                        'lineEnd'     => null,
                        'includeType' => $includeTypes[$tokens[$tokenIndex][0]],
                        'path'        => '',
                    );

                    // start processing with next token
                    if ($MACRO_TOKEN_ADVANCE() === false) {
                        goto SCANNER_END;
                    }

                    SCANNER_INCLUDE_TOP:

                        if ($tokenType === null && $tokenContent === ';') {
                            goto SCANNER_INCLUDE_END;
                        }

                        $infos[$infoIndex]['path'] .= $tokenContent;

                    SCANNER_INCLUDE_CONTINUE:

                        if ($MACRO_TOKEN_ADVANCE() === false) {
                            goto SCANNER_END;
                        }
                        goto SCANNER_INCLUDE_TOP;

                    SCANNER_INCLUDE_END:

                        $MACRO_INFO_ADVANCE();
                        goto SCANNER_CONTINUE;

                case T_FUNCTION:
                case T_FINAL:
                case T_ABSTRACT:
                case T_CLASS:
                case T_INTERFACE:

                    $infos[$infoIndex] = array(
                        'type'        => ($tokenType === T_FUNCTION) ? 'function' : 'class',
                        'tokenStart'  => $MACRO_TOKEN_LOGICAL_START_INDEX(),
                        'tokenEnd'    => null,
                        'lineStart'   => $tokens[$tokenIndex][2],
                        'lineEnd'     => null,
                        'namespace'   => $namespace,
                        'uses'        => $this->getNamespaceUsesNoScan($namespace),
                        'name'        => null,
                        'shortName'   => null,
                    );

                    $classBraceCount = 0;

                    // start processing with current token

                    SCANNER_CLASS_TOP:

                        // process the name
                        if ($infos[$infoIndex]['shortName'] == ''
                            && (($tokenType === T_CLASS || $tokenType === T_INTERFACE)&& $infos[$infoIndex]['type'] === 'class'
                                || ($tokenType === T_FUNCTION && $infos[$infoIndex]['type'] === 'function'))
                        ) {
                            $infos[$infoIndex]['shortName'] = $tokens[$tokenIndex+2][1];
                            $infos[$infoIndex]['name'] = (($namespace != '-GLOBAL-') ? $namespace . '\\' : '') . $infos[$infoIndex]['shortName'];
                        }

                        if ($tokenType === null) {
                            if ($tokenContent == '{') {
                                $classBraceCount++;
                            }
                            if ($tokenContent == '}') {
                                $classBraceCount--;
                                if ($classBraceCount === 0) {
                                    goto SCANNER_CLASS_END;
                                }
                            }
                        }

//                        if ($tokenType === null && $tokenContent == '}' && $classBraceCount == 1) {
//                            echo 'BREAKING OUT of ' . $infos[$infoIndex]['name'] . ' on ' . $tokenIndex . PHP_EOL;
//                            goto SCANNER_CLASS_END;
//                        }

                    SCANNER_CLASS_CONTINUE:

                        if ($MACRO_TOKEN_ADVANCE() === false) {
                            goto SCANNER_END;
                        }
                        goto SCANNER_CLASS_TOP;

                    SCANNER_CLASS_END:

                        $MACRO_INFO_ADVANCE();
                        goto SCANNER_CONTINUE;

            }

        SCANNER_CONTINUE:

            if ($MACRO_TOKEN_ADVANCE() === false) {
                goto SCANNER_END;
            }
            goto SCANNER_TOP;

        SCANNER_END:

        /**
         * END FINITE STATE MACHINE FOR SCANNING TOKENS
         */

        $this->isScanned = true;
    }

    // @todo hasNamespace(), getNamespace()

    protected function getNamespaceUsesNoScan($namespace)
    {
        $namespaces = array();
        foreach ($this->infos as $info) {
            if ($info['type'] == 'namespace') {
                $namespaces[] = $info['namespace'];
            }
        }

        if (!$namespaces) {
            return null;
        }

        if ($namespace === null) {
            $namespace = array_shift($namespaces);
        } elseif (!is_string($namespace)) {
            throw new Exception\InvalidArgumentException('Invalid namespace provided');
        } elseif (!in_array($namespace, $namespaces)) {
            return null;
        }

        $uses = array();
        foreach ($this->infos as $info) {
            if ($info['type'] !== 'use') {
                continue;
            }
            foreach ($info['statements'] as $statement) {
                if ($info['namespace'] == $namespace) {
                    $uses[] = $statement;
                }
            }
        }
        return $uses;
    }

}
