<?php

namespace Zend\Code\Scanner;

class ScannerMethod implements ScannerInterface
{
    protected $isScanned = false;
    
    protected $class = null;
    protected $uses = array();
    protected $name = null;
    protected $isFinal = false;
    protected $isAbstract = false;
    protected $isPublic = true;
    protected $isProtected = false;
    protected $isPrivate = false;
    protected $isStatic = false;
    
    protected $tokens = array();
    protected $infos = array();
    
    public function __construct(array $methodTokens, $class = null, array $uses = array())
    {
        $this->tokens = $methodTokens;
        $this->class = $class;
        $this->uses = $uses;
    }
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }
        
        if (!$this->tokens) {
            throw new \RuntimeException('No tokens were provided');
        }

        $fastForward = 0;
        $tokenIndex = 0;
        
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
        
        /*
        if ($this->tokens[$tokenIndex] != '{') {
            return;
        }
        */
        
        //$this->scanBody($tokenIndex);
        
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
        $info = null;
        
        while (true) {
            $tokenIndex++;
            $fastForward++;
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON
            if ($parenCount == 1 && is_string($token) && $token == ')') {
                break;
            }
            
            // ANALYZE
            if (is_string($token)) {
                if ($token == '(') {
                    $parenCount++;
                }
                if ($token == ')') {
                    $parenCount--;
                }
            }
            
            if ($parenCount == 1 && isset($info)) {
                $nextToken = (isset($info['name']) && is_string($this->tokens[$tokenIndex+1])) ? $this->tokens[$tokenIndex+1] : null;
                if ((is_string($token) && $token == ',') || (isset($nextToken) && $nextToken == ')')) {
                    $info['tokenEnd'] = $tokenIndex;
                    $this->infos[] = $info;
                    unset($info);
                }
                unset($nextToken);
            }
            
            if (is_array($token) && isset($info)) {
                $info['lineEnd'] = $token[2];
            }
            
            if ($parenCount > 1 || is_string($token)) {
                continue;
            }
            
            // gather line information if we can
            if (!isset($info)) {
                $info = array(
                	'type'        => 'parameter',
                    'tokenStart'  => $tokenIndex,
                    'tokenEnd'    => null,
                    'lineStart'   => $this->tokens[$tokenIndex][2],
                    'lineEnd'     => null,
                    'name'        => null
                );
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
    
    public function getParameter($parameterNameOrInfoIndex, $returnScanner = 'Zend\Code\Scanner\ScannerParameter')
    {
        $this->scan();
        
        // process the class requested
        static $baseScannerClass = 'Zend\Code\Scanner\ScannerParameter';
        if ($returnScanner !== $baseScannerClass) {
            if (!is_string($returnScanner)) {
                $returnScanner = $baseScannerClass;
            }
            $returnScanner = ltrim($returnScanner, '\\');
            if ($returnScanner !== $baseScannerClass && !is_subclass_of($returnScanner, $baseScannerClass)) {
                throw new \RuntimeException('Class must be or extend ' . $baseScannerClass);
            }
        }
        
        if (is_int($parameterNameOrInfoIndex)) {
            $info = $this->infos[$parameterNameOrInfoIndex];
            if ($info['type'] != 'parameter') {
                throw new \InvalidArgumentException('Index of info offset is not about a parameter');
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
        
        return new $returnScanner(
            array_slice($this->tokens, $info['tokenStart'], $info['tokenEnd'] - $info['tokenStart'] - 1),
            $this->name,
            $this->uses
            );
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