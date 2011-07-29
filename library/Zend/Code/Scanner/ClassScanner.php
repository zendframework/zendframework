<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Exception;

class ClassScanner implements Scanner
{
    protected $isScanned        = false;

    protected $namespace        = null;
    protected $uses             = array();
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
    protected $infos            = array();
    
    public function __construct(array $classTokens, $namespace = null, array $uses = array())
    {
        $this->tokens    = $classTokens;
        $this->namespace = $namespace;
        $this->uses      = $uses;
    }
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }
        
        if (!$this->tokens) {
            throw new Exception\RuntimeException('No tokens were provided');
        }
        
        for ($tokenIndex = 0; $tokenIndex < count($this->tokens); $tokenIndex++) {
            $token = $this->tokens[$tokenIndex];

            if (is_string($token)) {
                continue;
            }
            
            // tokens with some value are arrays (will have a token identifier, & line num)
            $fastForward = 0;
            switch ($token[0]) {
                case T_CLASS:
                case T_INTERFACE:
                    $this->scanClassInfo($tokenIndex, $fastForward);
                    break;
                
                case T_CONST:
                    $this->scanConstant($tokenIndex, $fastForward);
                    break;

                case T_FINAL:
                case T_ABSTRACT:
                    if (!$this->name) {
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
                        $this->scanMethod($tokenIndex, $fastForward);
                    } else {
                        $this->scanProperty($tokenIndex, $fastForward);
                    }
                    
                    break;
            }

            if ($fastForward) {
                $tokenIndex += $fastForward - 1;
            }
        }

        $this->isScanned = true;
    }
    
    protected function scanClassInfo($tokenIndex, &$fastForward)
    {
        if (isset($this->tokens[$tokenIndex-2]) && is_array($this->tokens[$tokenIndex-2])) {
            $tokenTwoBack = $this->tokens[$tokenIndex-2];
        }
        
        // T_ABSTRACT & T_FINAL will have been bypassed if no class name, and 
        // will alwasy be 2 tokens behind T_CLASS
        $this->isAbstract = (isset($tokenTwoBack) && ($tokenTwoBack[0] === T_ABSTRACT));
        $this->isFinal    = (isset($tokenTwoBack) && ($tokenTwoBack[0] === T_FINAL));
        
        $this->isInterface = (is_array($this->tokens[$tokenIndex]) && $this->tokens[$tokenIndex][0] == T_INTERFACE);
        $this->shortName   = $this->tokens[$tokenIndex+2][1];
        $this->name        = (($this->namespace) ? $this->namespace . '\\' : '') . $this->shortName;
        
        
        $context        = null;
        $interfaceIndex = 0;

        while (true) {
            $fastForward++;
            $tokenIndex++;
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON
            if (is_string($token) && $token == '{') {
                break;
            }
            
            // ANALYZE
            if (is_string($token) && $context == T_IMPLEMENTS && $token == ',') {
                $interfaceIndex++;
                $this->shortInterfaces[$interfaceIndex] = '';
            }
            
            if (is_array($token)) {
                if ($token[0] == T_NS_SEPARATOR || $token[0] == T_STRING) {
                    if ($context == T_EXTENDS) {
                        $this->shortParentClass .= $token[1];
                    } elseif ($context == T_IMPLEMENTS) {
                        $this->shortInterfaces[$interfaceIndex] .= $token[1];
                    }
                }
                if ($token[0] == T_EXTENDS && !$this->isInterface) {
                    $context = T_EXTENDS;
                    $this->shortParentClass = '';
                }
                if ($token[0] == T_IMPLEMENTS || ($this->isInterface && $token[0] == T_EXTENDS)) {
                    $context = T_IMPLEMENTS;
                    $this->shortInterfaces[$interfaceIndex] = '';
                }
            }

        }
        
        $data = (object) array(
            'namespace' => $this->namespace,
            'uses'      => $this->uses,
        );

        if ($this->shortInterfaces) {
            $this->interfaces = $this->shortInterfaces;
            array_walk($this->interfaces, array('Zend\Code\Scanner\Util', 'resolveImports'), $data);
        }
        
        if ($this->shortParentClass) {
            $this->parentClass = $this->shortParentClass;
            Util::resolveImports($this->parentClass, null, $data);
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
                $return[] = $this->getClass($info['name'], $returnScannerProperty);
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
                $return[] = $this->getMethod($info['name'], $returnScannerMethod);
            }
        }
        return $return;
    }
    
    /**
     * @param string|int $methodNameOrInfoIndex
     * @param string $returnScannerClass
     * @return Zend\Code\Scanner\MethodScanner
     */
    public function getMethod($methodNameOrInfoIndex, $returnScannerClass = 'Zend\Code\Scanner\MethodScanner')
    {
        $this->scan();
        
        // process the class requested
        // Static for performance reasons
        static $baseScannerClass = 'Zend\Code\Scanner\MethodScanner';
        if ($returnScannerClass !== $baseScannerClass) {
            if (!is_string($returnScannerClass)) {
                $returnScannerClass = $baseScannerClass;
            }
            $returnScannerClass = ltrim($returnScannerClass, '\\');
            if ($returnScannerClass !== $baseScannerClass 
                && !is_subclass_of($returnScannerClass, $baseScannerClass)
            ) {
                throw new Exception\RuntimeException(sprintf(
                    'Class must be or extend "%s"', $baseScannerClass
                ));
            }
        }
        
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
            die();
        }
        $m = new $returnScannerClass(
            array_slice($this->tokens, $info['tokenStart'], $info['tokenEnd'] - $info['tokenStart'] + 1),
            $this->namespace,
            $this->uses
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
