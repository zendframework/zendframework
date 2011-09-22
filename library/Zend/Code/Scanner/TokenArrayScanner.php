<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\NameInformation,
    Zend\Code\Exception;

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
     * @var NameInformation
     */
    protected $nameInformation = null;

    /**
     * @var array
     */
    protected $infos = array();

    /**
     * @param null|array $tokens
     */
    public function __construct($tokens = null)
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
    
    public function getUses()
    {
        $this->scan();

        $namespaces = array();
        foreach ($this->infos as $info) {
            if ($info['type'] == 'uses') {
                $namespaces[] = $info['uses'];
            }
        }
        return $namespaces;
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
        for ($u = 0; $u < count($this->infos); $u++) {
            if ($this->infos[$u]['type'] == 'use') {
                foreach ($this->infos[$u]['statements'] as $useStatement) {
                    if ($useStatement['as'] === null) {
                        $uses[] = $useStatement['use'];
                    } else {
                        $uses[$useStatement['use']] = $useStatement['as'];
                    }
                }
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

        static $MACROS = null;

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

        if ($MACROS === null) {
            $MACROS = array(
                'TOKEN_ADVANCE' => function() use (&$tokens, &$tokenIndex, &$token, &$tokenType, &$tokenContent, &$tokenLine) {
                    $tokenIndex = ($tokenIndex === null) ? 0 : $tokenIndex+1;
                    if (!isset($tokens[$tokenIndex])) {
                        $token        = false;
                        $tokenContent = false;
                        $tokenType    = false;
                        $tokenLine    = false;
                        return;
                    }
                    $token = $tokens[$tokenIndex];
                    if (is_string($token)) {
                        $tokenType = null;
                        $tokenContent = $token;
                    } else {
                        list($tokenType, $tokenContent, $tokenLine) = $token;
                    }
                    return $tokenIndex;
                },
                'TOKEN_LOGICAL_START_INDEX' => function() use (&$tokenIndex, &$docCommentIndex) {;
                    return ($docCommentIndex === false) ? $tokenIndex : $docCommentIndex;
                },
                'DOC_COMMENT_START' => function() use (&$tokenIndex, &$docCommentIndex) {
                    $docCommentIndex = $tokenIndex;
                    return $docCommentIndex;
                },
                'DOC_COMMENT_VALIDATE' => function() use (&$tokenType, &$docCommentIndex) {
                    static $validTrailingTokens = null;
                    if ($validTrailingTokens === null) {
                        $validTrailingTokens = array(T_WHITESPACE, T_FINAL, T_ABSTRACT, T_INTERFACE, T_CLASS, T_FUNCTION);
                    }
                    if ($docCommentIndex !== false && !in_array($tokenType, $validTrailingTokens)) {
                        $docCommentIndex = false;
                    }
                    return $docCommentIndex;
                },
                'INFO_ADVANCE' => function() use (&$infoIndex, &$infos, &$tokenIndex, &$tokenLine) {
                    $infos[$infoIndex]['tokenEnd'] = $tokenIndex;
                    $infos[$infoIndex]['lineEnd'] = $tokenLine;
                    $infoIndex++;
                    return $infoIndex;
                }
            );
        }

        /**
         * START FINITE STATE MACHINE FOR SCANNING TOKENS
         */

        // Initialize token
        $MACROS['TOKEN_ADVANCE']();

        TOKEN_SCANNER_TOP:

            if ($token === false) {
                goto TOKEN_SCANNER_END;
            }

            // Validate current doc comment index
            $MACROS['DOC_COMMENT_VALIDATE']();

            switch ($tokenType) {

                case T_DOC_COMMENT:

                    $MACROS['DOC_COMMENT_START']();
                    goto TOKEN_SCANNER_CONTINUE;

                case T_NAMESPACE:

                    $infos[$infoIndex] = array(
                        'type'       => 'namespace',
                        'tokenStart' => $MACROS['TOKEN_LOGICAL_START_INDEX'](),
                        'tokenEnd'   => null,
                        'lineStart'  => $token[2],
                        'lineEnd'    => null,
                        'namespace'  => null,
                    );

                    // start processing with next token
                    $MACROS['TOKEN_ADVANCE']();

                    TOKEN_SCANNER_NAMESPACE_TOP:

                        if ($tokenType === null && $tokenContent === ';') {
                            goto TOKEN_SCANNER_NAMESPACE_END;
                        }

                        if ($tokenType === T_WHITESPACE) {
                            goto TOKEN_SCANNER_NAMESPACE_CONTINUE;
                        }
                        
                        if ($tokenType === T_NS_SEPARATOR || $tokenType === T_STRING) {
                            $infos[$infoIndex]['namespace'] .= $tokenContent;
                            $namespace = $infos[$infoIndex]['namespace'];
                        }

                    TOKEN_SCANNER_NAMESPACE_CONTINUE:

                        $MACROS['TOKEN_ADVANCE']();
                        goto TOKEN_SCANNER_NAMESPACE_TOP;

                    TOKEN_SCANNER_NAMESPACE_END:

                        $MACROS['INFO_ADVANCE']();
                        goto TOKEN_SCANNER_CONTINUE;

                case T_USE:

                    $infos[$infoIndex] = array(
                        'type'       => 'use',
                        'tokenStart' => $MACROS['TOKEN_LOGICAL_START_INDEX'](),
                        'tokenEnd'   => null,
                        'lineStart'  => $tokens[$tokenIndex][2],
                        'lineEnd'    => null,
                        'statements' => array(0 => array('use' => null, 'as' => null)),
                    );

                    $useStatementIndex = 0;
                    $useAsContext = false;

                    // start processing with next token
                    $MACROS['TOKEN_ADVANCE']();

                    TOKEN_SCANNER_USE_TOP:

                        if ($tokenType === null) {
                            if ($tokenContent === ';') {
                                goto TOKEN_SCANNER_USE_END;
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
                                goto TOKEN_SCANNER_USE_CONTINUE;
                            }

                            if ($tokenType == T_NS_SEPARATOR || $tokenType == T_STRING) {
                                if ($useAsContext == false) {
                                    $infos[$infoIndex]['statements'][$useStatementIndex]['use'] .= $tokenContent;
                                } else {
                                    $infos[$infoIndex]['statements'][$useStatementIndex]['as'] = $tokenContent;
                                }
                            }

                        }

                    TOKEN_SCANNER_USE_CONTINUE:

                        $MACROS['TOKEN_ADVANCE']();
                        goto TOKEN_SCANNER_USE_TOP;

                    TOKEN_SCANNER_USE_END:

                        $MACROS['INFO_ADVANCE']();
                        goto TOKEN_SCANNER_CONTINUE;

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
                        'tokenStart'  => $MACROS['TOKEN_LOGICAL_START_INDEX'](),
                        'tokenEnd'    => null,
                        'lineStart'   => $tokens[$tokenIndex][2],
                        'lineEnd'     => null,
                        'includeType' => $includeTypes[$tokens[$tokenIndex][0]],
                        'path'        => '',
                    );

                    // start processing with next token
                    $MACROS['TOKEN_ADVANCE']();

                    TOKEN_SCANNER_INCLUDE_TOP:

                        if ($tokenType === null && $token === ';') {
                            goto TOKEN_SCANNER_INCLUDE_END;
                        }

                        $infos[$infoIndex]['path'] .= $tokenContent;

                    TOKEN_SCANNER_INCLUDE_CONTINUE:

                        $MACROS['TOKEN_ADVANCE']();
                        goto TOKEN_SCANNER_INCLUDE_TOP;

                    TOKEN_SCANNER_INCLUDE_END:

                        $MACROS['INFO_ADVANCE']();
                        goto TOKEN_SCANNER_CONTINUE;

                case T_FUNCTION:
                case T_FINAL:
                case T_ABSTRACT:
                case T_CLASS:
                case T_INTERFACE:

                    $infos[$infoIndex] = array(
                        'type'        => ($tokenType === T_FUNCTION) ? 'function' : 'class',
                        'tokenStart'  => $MACROS['TOKEN_LOGICAL_START_INDEX'](),
                        'tokenEnd'    => null,
                        'lineStart'   => $tokens[$tokenIndex][2],
                        'lineEnd'     => null,
                        'namespace'   => $namespace,
                        'name'        => null,
                        'shortName'   => null,
                    );

                    $classBraceCount = 0;

                    // start processing with current token

                    TOKEN_SCANNER_CLASS_TOP:

                        if ($tokenType === null && $tokenContent == '}' && $classBraceCount == 1) {
                            goto TOKEN_SCANNER_CLASS_END;
                        }

                        // process the name
                        if ($tokenType === T_CLASS || $tokenType === T_INTERFACE ||
                            ($tokenType === T_FUNCTION && $infos[$infoIndex]['type'] === 'function')
                        ) {
                            $infos[$infoIndex]['shortName'] = $tokens[$tokenIndex+2][1];
                            $infos[$infoIndex]['name'] = (($namespace) ? $namespace . '\\' : '') . $infos[$infoIndex]['shortName'];
                        }



                        if ($tokenType === null) {
                            if ($tokenContent == '{') {
                                $classBraceCount++;
                            }
                            if ($tokenContent == '}') {
                                $classBraceCount--;
                            }
                        }

                    TOKEN_SCANNER_CLASS_CONTINUE:

                        $MACROS['TOKEN_ADVANCE']();
                        goto TOKEN_SCANNER_CLASS_TOP;

                    TOKEN_SCANNER_CLASS_END:

                        $MACROS['INFO_ADVANCE']();
                        goto TOKEN_SCANNER_CONTINUE;

            }

        TOKEN_SCANNER_CONTINUE:

            $MACROS['TOKEN_ADVANCE']();
            goto TOKEN_SCANNER_TOP;

        TOKEN_SCANNER_END:

        /**
         * END FINITE STATE MACHINE FOR SCANNING TOKENS
         */

        $this->isScanned = true;
    }

    // @todo hasNamespace(), getNamespace()

}
