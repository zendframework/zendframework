<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\NameInformation,
    Zend\Code\Exception;

class ClassScanner implements Scanner
{
    protected $isScanned        = false;

    protected $docComment       = null;
    protected $name             = null;
    protected $shortName        = null;
    protected $isFinal          = false;
    protected $isAbstract       = false;
    protected $isInterface      = false;

    protected $parentClass      = null;
    protected $shortParentClass = null;

    protected $interfaces       = array();
    protected $shortInterfaces  = array();

    protected $tokens           = array();
    protected $nameInformation  = null;
    protected $infos            = array();
    
    public function __construct(array $classTokens, NameInformation $nameInformation = null)
    {
        $this->tokens          = $classTokens;
        $this->nameInformation = $nameInformation;
    }
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }
        
        if (!$this->tokens) {
            throw new Exception\RuntimeException('No tokens were provided');
        }

        $currentDocCommentIndex = false;
        $docCommentTokenWhitelist = array(
            T_WHITESPACE, T_FINAL, T_ABSTRACT, T_CLASS, T_INTERFACE, T_PUBLIC,
            T_PROTECTED, T_PRIVATE, T_FUNCTION, T_VAR, T_STATIC, T_CONST
        );

        for ($tokenIndex = 0; $tokenIndex < count($this->tokens); $tokenIndex++) {
            $token = $this->tokens[$tokenIndex];

            // track docblocks through whitespace tokens (they might belong to something)
            if ($currentDocCommentIndex !== false && is_array($token) && !in_array($token[0], $docCommentTokenWhitelist)) {
                $currentDocCommentIndex = false;
            }

            if (is_string($token)) {
                continue;
            }
            
            // tokens with some value are arrays (will have a token identifier, & line num)
            $fastForward = 0;
            switch ($token[0]) {
                case T_DOC_COMMENT:
                    $currentDocCommentIndex = $tokenIndex;
                    break;
                
                case T_CLASS:
                case T_INTERFACE:
                    $this->scanClassInfo(($currentDocCommentIndex !== false) ? $currentDocCommentIndex : $tokenIndex, $fastForward);
                    break;
                
                case T_CONST:
                    $this->scanConstant(($currentDocCommentIndex !== false) ? $currentDocCommentIndex : $tokenIndex, $fastForward);
                    break;

                case T_FINAL:
                case T_ABSTRACT:
                    // are we talking about a class or a class member?
                    if (!$this->name) {
                        $this->scanClassInfo(($currentDocCommentIndex !== false) ? $currentDocCommentIndex : $tokenIndex, $fastForward);
                        break;
                    }
                case T_PUBLIC:
                case T_PROTECTED:
                case T_PRIVATE:
                case T_STATIC:
                case T_FUNCTION:
                case T_VAR:
                    $subTokenIndex = $tokenIndex;
                    do {
                        $subToken = $this->tokens[$subTokenIndex++];
                    } while (!(is_array($subToken) && $subToken[0] == T_FUNCTION) 
                             && !(is_string($subToken) && $subToken == ';')
                    );

                    if (is_array($subToken)) {
                        $this->scanMethod(($currentDocCommentIndex !== false) ? $currentDocCommentIndex : $tokenIndex, $fastForward);
                    } else {
                        $this->scanProperty(($currentDocCommentIndex !== false) ? $currentDocCommentIndex : $tokenIndex, $fastForward);
                    }
                    $currentDocCommentIndex = false;
                    
                    break;
            }

            if ($fastForward) {
                $tokenIndex += $fastForward - 1;
                $currentDocCommentIndex = false; // automatically clear doc comment index
            }
        }

        $this->isScanned = true;
    }
    
    protected function scanClassInfo($tokenIndex, &$fastForward)
    {
        $context        = null;
        $interfaceIndex = 0;

        /**
         * TOKEN SCANNER START
         */

        TOKEN_CLASS_SCANNER_TOP:

            $token = $this->tokens[$tokenIndex];

            // BREAK ON
            if (is_string($token) && $token == '{') {
                goto TOKEN_CLASS_SCANNER_EXIT;
            }
            
            // ANALYZE

            // when token is a string
            if (is_string($token) && $context == T_IMPLEMENTS && $token == ',') {
                $interfaceIndex++;
                $this->shortInterfaces[$interfaceIndex] = '';
            }

            // when token is an array
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_INTERFACE:
                    case T_CLASS:
                        $this->shortName = $this->tokens[$tokenIndex+2][1];
                        if ($this->nameInformation && $this->nameInformation->hasNamespace()) {
                            $this->name = $this->nameInformation->getNamespace() . '\\' . $this->shortName;
                        } else {
                            $this->name = $this->shortName;
                        }
                        break;
                    case T_DOC_COMMENT:
                        $this->docComment = $token[1];
                        break;
                    case T_INTERFACE:
                        $this->isInterface = true;
                        break;
                    case T_ABSTRACT:
                        $this->isAbstract = true;
                        break;
                    case T_FINAL:
                        $this->isFinal = true;
                        break;
                    case T_NS_SEPARATOR:
                    case T_STRING:
                        switch ($context) {
                            case T_EXTENDS:
                                $this->shortParentClass .= $token[1];
                                break;
                            case T_IMPLEMENTS:
                                $this->shortInterfaces[$interfaceIndex] .= $token[1];
                                break;
                        }
                        break;
                    case T_EXTENDS:
                    case T_IMPLEMENTS:
                        $context = $token[0];
                        if (($this->isInterface && $context === T_EXTENDS) || $context === T_IMPLEMENTS) {
                            $this->shortInterfaces[$interfaceIndex] = '';
                        } elseif (!$this->isInterface && $context === T_EXTENDS) {
                            $this->shortParentClass = '';
                        }
                        break;
                }
            }

            $fastForward++;
            $tokenIndex++;
            goto TOKEN_CLASS_SCANNER_TOP;

        TOKEN_CLASS_SCANNER_EXIT:

        /**
         * TOKEN SCANNER END
         */

        if ($this->shortInterfaces) {
            $this->interfaces = $this->shortInterfaces;
            if ($this->nameInformation) {
                foreach ($this->interfaces as $iIndex => $interface) {
                    $this->interfaces[$iIndex] = $this->nameInformation->resolveName($interface);
                }
            }
        }
        
        if ($this->shortParentClass) {
            $this->parentClass = $this->shortParentClass;
            if ($this->nameInformation) {
                $this->parentClass = $this->nameInformation->resolveName($this->parentClass);
            }
        }

    }
    
    protected function scanConstant($tokenIndex, &$fastForward)
    {
        $info = array(
            'type'       => 'constant',
            'tokenStart' => $tokenIndex,
            'tokenEnd'   => null,
            'lineStart'  => $this->tokens[$tokenIndex][2],
            'lineEnd'    => null,
            'name'       => null,
            'value'	     => null,
        );
            
        while (true) {
            $fastForward++;
            $tokenIndex++;
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON
            if (is_string($token) && $token == ';') {
                break;
            }
            
            if ((is_array($token) && $token[0] == T_WHITESPACE) 
                || (is_string($token) && $token == '=')
            ) {
                continue;
            }

            $info['value'] .= (is_array($token)) ? $token[1] : $token;
            
            if (is_array($token)) {
                $info['lineEnd'] = $token[2];
            }
            
        }
        
        $info['tokenEnd'] = $tokenIndex;
        $this->infos[] = $info;
    }
    
    protected function scanMethod($tokenIndex, &$fastForward)
    {
        $info = array(
        	'type'        => 'method',
            'tokenStart'  => $tokenIndex,
            'tokenEnd'    => null,
            'lineStart'   => $this->tokens[$tokenIndex][2],
            'lineEnd'     => null,
            'name'        => null,
        );
        
        // start on first token, not second
        $fastForward--;
        $tokenIndex--;
            
        $braceCount = 0;
        while (true) {
            
            $fastForward++;
            $tokenIndex++;
            if (!isset($this->tokens[$tokenIndex])) {
                break;
            }
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON
            if (is_string($token) && $token == '}' && $braceCount == 1) {
                break;
            }
            
            // ANALYZE
            if (is_string($token)) {
                if ($token == '{') {
                    $braceCount++;
                }
                if ($token == '}') {
                    $braceCount--;
                }
            }
            
            if ($info['name'] === null && $token[0] === T_FUNCTION) {
                // next token after T_WHITESPACE is name
                $info['name'] = $this->tokens[$tokenIndex+2][1];
                continue;
            }
            
            if (is_array($token)) {
                $info['lineEnd'] = $token[2];
            }
            
        }
        
        $info['tokenEnd'] = $tokenIndex;
        $this->infos[]    = $info;
    }
    
    protected function scanProperty($tokenIndex, &$fastForward)
    {
        $info = array(
        	'type'        => 'property',
            'tokenStart'  => $tokenIndex,
            'tokenEnd'    => null,
            'lineStart'   => $this->tokens[$tokenIndex][2],
            'lineEnd'     => null,
            'name'        => null,
        );
        
        $index = $tokenIndex;

        while (true) {
            $fastForward++;
            $tokenIndex++;
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON
            if (is_string($token) && $token = ';') {
                break;
            }
            
            // ANALYZE
            if ($token[0] === T_VARIABLE) {
                $info['name'] = ltrim($token[1], '$');
                continue;
            }
            
            if (is_array($token)) {
                $info['lineEnd'] = $token[2];
            }
            
        }
        
        $info['tokenEnd'] = $index;
        $this->infos[]    = $info;
    }

    public function getDocComment()
    {
        $this->scan();
        return $this->docComment;
    }

    public function getName()
    {
        $this->scan();
        return $this->name;
    }
    
    public function getShortName()
    {
        $this->scan();
        return $this->shortName;
    }
    
    public function isFinal()
    {
        $this->scan();
        return $this->isFinal;
    }

    public function isInstantiable()
    {
        $this->scan();
        return (!$this->isAbstract && !$this->isInterface);
    }
    
    public function isAbstract()
    {
        $this->scan();
        return $this->isAbstract;
    }
    
    public function isInterface()
    {
        $this->scan();
        return $this->isInterface;
    }

    public function hasParentClass()
    {
        $this->scan();
        return ($this->parentClass != null);
    }
    
    public function getParentClass()
    {
        $this->scan();
        return $this->parentClass;
    }
    
    public function getInterfaces()
    {
        $this->scan();
        return $this->interfaces;
    }
    
    public function getConstants()
    {
        $this->scan();
        
        $return = array();
        
        foreach ($this->infos as $info) {
            if ($info['type'] != 'constant') {
                continue;
            }
            $return[] = $info['name'];
        }
        return $return;
    }
    
    public function getProperties($returnScannerProperty = false)
    {
        $this->scan();
        
        $return = array();
        
        foreach ($this->infos as $info) {
            if ($info['type'] != 'property') {
                continue;
            }

            if (!$returnScannerProperty) {
                $return[] = $info['name'];
            } else {
                $return[] = $this->getProperty($info['name']);
            }
        }
        return $return;
    }
    
    public function getMethods($returnScannerMethod = false)
    {
        $this->scan();
        
        $return = array();
        
        foreach ($this->infos as $info) {
            if ($info['type'] != 'method') {
                continue;
            }

            if (!$returnScannerMethod) {
                $return[] = $info['name'];
            } else {
                $return[] = $this->getMethod($info['name']);
            }
        }
        return $return;
    }
    
    /**
     * @param string|int $methodNameOrInfoIndex
     * @return MethodScanner
     */
    public function getMethod($methodNameOrInfoIndex)
    {
        $this->scan();

        if (is_int($methodNameOrInfoIndex)) {
            $info = $this->infos[$methodNameOrInfoIndex];
            if ($info['type'] != 'method') {
                throw new Exception\InvalidArgumentException('Index of info offset is not about a method');
            }
        } elseif (is_string($methodNameOrInfoIndex)) {
            $methodFound = false;
            foreach ($this->infos as $infoIndex => $info) {
                if ($info['type'] === 'method' && $info['name'] === $methodNameOrInfoIndex) {
                    $methodFound = true;
                    break;
                }
            }
            if (!$methodFound) {
                return false;
            }
        }
        if (!isset($info)) {
            // @todo find a way to test this
            die('Massive Failure, test this');
        }
        $m = new MethodScanner(
            array_slice($this->tokens, $info['tokenStart'], $info['tokenEnd'] - $info['tokenStart'] + 1),
            $this->nameInformation
            );
        $m->setClass($this->name);
        $m->setScannerClass($this);
        return $m;
    }
    
    public function hasMethod($name)
    {
        $this->scan();
        
        foreach ($this->infos as $infoIndex => $info) {
            if ($info['type'] === 'method' && $info['name'] === $name) {
                return true;
            }
        }
        return false;
    }
    
    public static function export()
    {
        // @todo
    }
    
    public function __toString()
    {
        // @todo
    }
    
}
