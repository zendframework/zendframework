<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Exception;

class MethodScanner implements Scanner
{
    protected $isScanned    = false;

    protected $scannerClass = null;
    protected $class        = null;
    protected $namespace    = null;
    protected $uses         = array();
    protected $name         = null;
    protected $isFinal      = false;
    protected $isAbstract   = false;
    protected $isPublic     = true;
    protected $isProtected  = false;
    protected $isPrivate    = false;
    protected $isStatic     = false;

    protected $tokens       = array();
    protected $infos        = array();
    
    public function __construct(array $methodTokens, $namespace = null, array $uses = array())
    {
        $this->tokens = $methodTokens;
        $this->namespace = $namespace;
        $this->uses = $uses;
    }
    
    public function setClass($class)
    {
        $this->class = $class;
    }
    
    public function setScannerClass(ClassScanner $scannerClass)
    {
        $this->scannerClass = $scannerClass;
    }
    
    public function getClassScanner()
    {
        return $this->scannerClass;
    }
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }

        if (!$this->tokens) {
            throw new Exception\RuntimeException('No tokens were provided');
        }

        $fastForward = 0;
        $tokenIndex  = 0;

        $this->scanMethodInfo($tokenIndex, $fastForward);

        if ($fastForward) {
            $tokenIndex += $fastForward - 1;
            $fastForward = 0;
        }
        
        // advance to first paren
        while ($this->tokens[$tokenIndex] != '(') {
            $tokenIndex++;
        }
        
        $this->scanParameters($tokenIndex, $fastForward);
        
        $this->isScanned = true;
    }
    
    protected function scanMethodInfo($tokenIndex, &$fastForward)
    {
        while (true) {
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON
            if (is_string($token) && $token == '(') {
                break;
            }
            
            // ANALYZE
            if (is_string($token)) {
                continue;
            }
            
            switch ($token[0]) {
                case T_FINAL:
                    $this->isFinal = true;
                    continue;

                case T_ABSTRACT:
                    $this->isAbstract = true;
                    continue;

                case T_PUBLIC:
                    continue;

                case T_PROTECTED:
                    $this->isProtected = true;
                    $this->isPublic = false;
                    continue;

                case T_PRIVATE:
                    $this->isPrivate = true;
                    $this->isPublic = false;
                    continue;

                case T_STATIC:
                    $this->isStatic = true;
                    continue;

                case T_STRING:
                    $this->name = $token[1];
                    continue;
            }
            
            $fastForward++;
            $tokenIndex++;
        }
    }
    
    protected function scanParameters($tokenIndex, &$fastForward)
    {
        // first token is paren let loop increase
        $parenCount = 1;
        $info       = null;
        $position   = 0;
        
        while (true) {
            $tokenIndex++;
            $fastForward++;
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON
            if ($parenCount == 1 && is_string($token) && $token == ')') {
                if ($info) {
                    $info['tokenEnd'] = $tokenIndex - 1;
                    $this->infos[] = $info;
                }
                break;
            }
            
            // ANALYZE
                    
            // gather line information if we can
            if (!isset($info)) {
                $info = array(
                	'type'        => 'parameter',
                    'tokenStart'  => $tokenIndex,
                    'tokenEnd'    => null,
                    'lineStart'   => $token[2],
                    'lineEnd'     => $token[2],
                    'name'        => null,
                    'position'    => ++$position,
                );
            }
            
            if (is_array($token) && isset($info)) {
                $info['lineEnd'] = $token[2];
            }
            
            if (is_array($token) && $token[0] === T_WHITESPACE) {
                continue;
            }
            
            if (is_string($token)) {
                if ($token == '(') {
                    $parenCount++;
                }
                if ($token == ')') {
                    $parenCount--;
                }
                
                if ($parenCount !== 1) {
                    continue;
                }
                
            }
            
            if (isset($info) && is_string($token) && $token == ',') {
                $info['tokenEnd'] = $tokenIndex - 1;
                $this->infos[]    = $info;
                unset($info);
            }

            if (is_array($token) && $token[0] === T_VARIABLE) {
                $info['name'] = ltrim($token[1], '$');
            }
            
        }
        
    }
    
    public function getName()
    {
        $this->scan();
        return $this->name;
    }
    
    public function isFinal()
    {
        $this->scan();
        return $this->isFinal;
    }
    
    public function isAbstract()
    {
        $this->scan();
        return $this->isAbstract;
    }
    
    public function isPublic()
    {
        $this->scan();
        return $this->isPublic;
    }
    
    public function isProtected()
    {
        $this->scan();
        return $this->isProtected;
    }
    
    public function isPrivate()
    {
        $this->scan();
        return $this->isPrivate;
    }
    
    public function isStatic()
    {
        $this->scan();
        return $this->isStatic;
    }
    
    public function getNumberOfParameters()
    {
        return count($this->getParameters());
    }
    
    public function getParameters($returnScanner = false)
    {
        $this->scan();
        
        $return = array();

        foreach ($this->infos as $info) {
            if ($info['type'] != 'parameter') {
                continue;
            }

            if (!$returnScanner) {
                $return[] = $info['name'];
            } else {
                $return[] = $this->getParameter($info['name'], $returnScanner);
            }
        }
        return $return;
    }
    
    public function getParameter($parameterNameOrInfoIndex, $returnScanner = 'Zend\Code\Scanner\ParameterScanner')
    {
        $this->scan();
        
        // process the class requested
        // Static for performance reasons
        static $baseScannerClass = 'Zend\Code\Scanner\ParameterScanner';
        if ($returnScanner !== $baseScannerClass) {
            if (!is_string($returnScanner)) {
                $returnScanner = $baseScannerClass;
            }
            $returnScanner = ltrim($returnScanner, '\\');
            if ($returnScanner !== $baseScannerClass 
                && !is_subclass_of($returnScanner, $baseScannerClass)
            ) {
                throw new Exception\RuntimeException(sprintf(
                    'Class must be or extend "%s"', $baseScannerClass
                ));
            }
        }
        
        if (is_int($parameterNameOrInfoIndex)) {
            $info = $this->infos[$parameterNameOrInfoIndex];
            if ($info['type'] != 'parameter') {
                throw new Exception\InvalidArgumentException('Index of info offset is not about a parameter');
            }
        } elseif (is_string($parameterNameOrInfoIndex)) {
            $methodFound = false;
            foreach ($this->infos as $infoIndex => $info) {
                if ($info['type'] === 'parameter' && $info['name'] === $parameterNameOrInfoIndex) {
                    $methodFound = true;
                    break;
                }
            }
            if (!$methodFound) {
                return false;
            }
        }
        
        $p = new $returnScanner(
            array_slice($this->tokens, $info['tokenStart'], $info['tokenEnd'] - $info['tokenStart'] + 1),
            $this->namespace,
            $this->uses
            );
        $p->setDeclaringFunction($this->name);
        $p->setDeclaringScannerFunction($this);
        $p->setDeclaringClass($this->class);
        $p->setDeclaringScannerClass($this->scannerClass);
        $p->setPosition($info['position']);
        return $p;
    }

    public static function export()
    {
        // @todo
    }
    
    public function __toString()
    {
        $this->scan();
        return var_export($this, true);
    }
    
}
