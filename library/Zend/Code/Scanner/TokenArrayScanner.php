<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Exception;

class TokenArrayScanner implements Scanner
{
    protected $isScanned = false;

    protected $tokens    = array();

    protected $infos     = array();
    
    public function __construct($tokens = null, $options = null)
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
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }
        
        if (!$this->tokens) {
            throw new Exception\RuntimeException('No tokens were provided');
        }

        $currentNamespace = null;
        
        for ($tokenIndex = 0; $tokenIndex < count($this->tokens); $tokenIndex++) {
            $token = $this->tokens[$tokenIndex];
            
            // tokens with some value are arrays (will have a token identifier, & line num)
            $fastForward = 0;
            switch ($token[0]) {
                case T_DOC_COMMENT:
                    // @todo Implement docblock scanner
                    break;

                case T_NAMESPACE:
                    $currentNamespace = $this->scanNamespace($tokenIndex, $fastForward);
                    break;

                case T_USE:
                    // process uses
                    $this->scanUse($tokenIndex, $fastForward);
                    break;

                case T_INCLUDE:
                case T_INCLUDE_ONCE:
                case T_REQUIRE:
                case T_REQUIRE_ONCE:
                    // process include
                    $this->scanInclude($tokenIndex, $fastForward);
                    break;

                case T_FINAL:
                case T_ABSTRACT:
                case T_CLASS:
                case T_INTERFACE:
                    $this->scanClass($tokenIndex, $fastForward, $currentNamespace);
                    break;

                case T_FUNCTION:
                    $this->scanFunction($tokenIndex, $fastForward, $currentNamespace);
                    break;
            }
            if ($fastForward) {
                $tokenIndex += $fastForward - 1;
            }            
        }
        
        $this->isScanned = true;
    }
    
    protected function scanNamespace($tokenIndex, &$fastForward)
    {
        $info = array(
            'type'       => 'namespace',
            'tokenStart' => $tokenIndex,
            'tokenEnd'   => null,
            'lineStart'  => $this->tokens[$tokenIndex][2],
            'lineEnd'    => null,
            'namespace'  => null,
        );

        // move past current T_NAMESPACE & following T_WHITESPACE
        $tokenIndex++; 
        $fastForward++;
        
        while (true) {
            $tokenIndex++;
            $fastForward++;
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON:
            if (is_string($token) && $token == ';') {
                break;
            }
            
            // ANALYZE
            if (is_array($token)) {
                $info['lineEnd'] = $token[2];
                if ($token[0] == T_WHITESPACE) {
                    continue;
                }
                if ($token[0] == T_NS_SEPARATOR || $token[0] == T_STRING) {
                    $info['namespace'] .= (is_string($token)) ? $token : $token[1];
                }
            }
        }
        
        $info['tokenEnd'] = $tokenIndex;
        $this->infos[]    = $info;
        
        return $info['namespace'];
    }
    
    protected function scanUse($tokenIndex, &$fastForward)
    {
        $info = array(
            'type'       => 'use',
            'tokenStart' => $tokenIndex,
            'tokenEnd'   => null,
            'lineStart'  => $this->tokens[$tokenIndex][2],
            'lineEnd'    => null,
            'statements' => array(),
        );
        
        // Static for performance purposes
        static $statementTemplate = array(
            'use'        => null,
            'as'         => null,
            'asComputed' => null,
        );
        
        // skip current token T_USE and following T_WHITESPACE
        $tokenIndex++; 
        $fastForward++;
        
        $hasAs     = false;
        $sCount    = 0;
        $statement = $statementTemplate;

        while (true) {
            $tokenIndex++;
            $fastForward++;
            $token = $this->tokens[$tokenIndex];

            // BREAK ON:
            if (is_string($token) && $token == ';') {
                break;
            }
            
            // ANALYZE
            if (is_array($token)) {
                // store known line end
                $info['lineEnd'] = $token[2];
                
                if ($token[0] == T_NS_SEPARATOR || $token[0] == T_STRING) {
                    if ($hasAs == false) {
                        $statement['use'] .= (is_string($token)) ? $token : $token[1];
                    } else {
                        $statement['as'] = $token[1]; // always a string
                    }
                }
                if ($token[0] == T_AS) {
                    $hasAs = true;
                }
            }
            
            $tokenLookahead = $this->tokens[$tokenIndex+1];
            
            if (is_string($tokenLookahead) && ($tokenLookahead == ',' || $tokenLookahead == ';')) {
                if (!$hasAs) {
                    $statement['asComputed'] = substr(
                        $statement['use'],
                        (strpos($statement['use'],'\\') ? (strrpos($statement['use'],'\\')+1) : 0)
                    );
                }
                $info['statements'][$sCount] = $statement;
                $sCount++;
                $statement = $statementTemplate;
                $hasAs = false;
            }
            
        }
        
        $info['tokenEnd'] = $tokenIndex;
        $this->infos[]    = $info;
    }
    
    protected function scanInclude($tokenIndex, &$fastForward)
    {
        // Static for performance purposes
        static $types = array(
            T_INCLUDE      => 'include',
            T_INCLUDE_ONCE => 'include_once',
            T_REQUIRE      => 'require',
            T_REQUIRE_ONCE => 'require_once',
        );

        $info = array(
            'type'        => 'include',
            'tokenStart'  => $tokenIndex,
            'tokenEnd'    => null,
            'lineStart'   => $this->tokens[$tokenIndex][2],
            'lineEnd'     => null,
            'includeType' => $types[$this->tokens[$tokenIndex][0]],
            'path'        => '',
        );

        $path  = '';
        $index = $tokenIndex;
        
        // move past include & the required whitespace
        $fastForward++;
        $index++;

        while (true) {
            $fastForward++;
            $tokenIndex++;
            $token = $this->tokens[$index++];
            
            // BREAK ON
            if (is_string($token) && $token == ';') {
                break;
            }
            
            // ANALYZE
            if (is_array($token)) {
                $info['lineEnd'] = $token[2];
            }            
            
            $info['path'] .= (is_string($token)) ? $token : $token[1];
        }
        
        $info['tokenEnd'] = $tokenIndex;
        $this->infos[] = $info;
    }
    
    protected function scanClass($tokenIndex, &$fastForward, $namespace = null)
    {
        $info = array(
            'type'        => 'class',
            'tokenStart'  => $tokenIndex,
            'tokenEnd'    => null,
            'lineStart'   => $this->tokens[$tokenIndex][2],
            'lineEnd'     => null,
            'namespace'   => $namespace,
            'name'        => null,
            'shortName'   => null,
        );
        
        // if FINAL or ABSTRACT marker is found, find name accordingly
        if ($this->tokens[$tokenIndex][0] === T_FINAL 
            || $this->tokens[$tokenIndex][0] === T_ABSTRACT
        ) {
            $info['shortName'] = $this->tokens[$tokenIndex+4][1];
        } else {
            $info['shortName'] = $this->tokens[$tokenIndex+2][1];
        }
        
        $info['name'] = (($namespace) ? $namespace . '\\' : '') . $info['shortName'];

        $braceCount = 0;
        while (true) {
            $fastForward++;
            $tokenIndex++;
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
            
            if (is_array($token)) {
                $info['lineEnd'] = $token[2];
            }
            

        }
        
        $info['tokenEnd'] = $tokenIndex;
        $this->infos[]    = $info;
    }
    
    protected function scanFunction($tokenIndex, &$fastForward, $namespace = null, $usesComputed = array())
    {
        $info = array(
            'type'        => 'function',
            'tokenStart'  => $tokenIndex,
            'tokenEnd'    => null,
            'lineStart'   => $this->tokens[$tokenIndex][2],
            'lineEnd'     => null,
            'name'        => $namespace . '\\' . $this->tokens[$tokenIndex+2][1],
            'shortName'   => $this->tokens[$tokenIndex+2][1],
            'namespace'   => $namespace,
        );

        $braceCount = 0;
        while (true) {
            $fastForward++;
            $tokenIndex++;
            $token = $this->tokens[$tokenIndex];
            
            // BREAK ON
            if ($braceCount === false) {
                break;
            }
            
            // ANALYZE
            if (is_string($token)) {
                if ($token == '{') {
                    $context = null;
                    $braceCount++;
                }
                if ($token == '}') {
                    $braceCount = ($braceCount == 1) ? false : ($braceCount - 1);
                }
            }
            if (is_array($token)) {
                $info['lineEnd'] = $token[2];
            }
        }
        
        $info['tokenEnd'] = $tokenIndex;
        $this->infos[]    = $info;
    }

    // @todo hasNamespace(), getNamespace()
    
    public function getNamespaces($returnScannerClass = false)
    {
        $this->scan();
        
        if (!$returnScannerClass) {
            $namespaces = array();
            foreach ($this->infos as $info) {
                if ($info['type'] == 'namespace') {
                    $namespaces[] = $info['namespace'];
                }
            }
            return $namespaces;
        } else {
            if ($returnScannerClass === true) {
                $returnScannerClass = 'Zend\Code\Scanner\NamespaceScanner';
            }
            $scannerClass = new $returnScannerClass;
            // @todo
        }
    }
    
    public function getUses($returnScannerClass = false)
    {
        $this->scan();
        
        if (!$returnScannerClass) {
            $namespaces = array();
            foreach ($this->infos as $info) {
                if ($info['type'] == 'uses') {
                    $namespaces[] = $info['uses'];
                }
            }
            return $namespaces;
        }
    }
    
    public function getIncludes($returnScannerClass = false)
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
     * @param string $returnScannerClass
     * @return Zend\Code\Scanner\ClassScanner
     */
    public function getClass($classNameOrInfoIndex, $returnScannerClass = 'Zend\Code\Scanner\ClassScanner')
    {
        $this->scan();
        
        // Process the class requested
        // Static for performance reasons
        static $baseScannerClass = 'Zend\Code\Scanner\ClassScanner';
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
                    $useKey        = ($useStatement['as']) ?: $useStatement['asComputed'];
                    $uses[$useKey] = $useStatement['use'];
                }
            }
        }
        
        return new $returnScannerClass(
            array_slice(
                $this->tokens, 
                $info['tokenStart'], 
                ($info['tokenEnd'] - $info['tokenStart'] + 1)
            ), // zero indexed array
            $info['namespace'],
            $uses
        );
    }
    
    public function getFunctions($returnScannerClass = false)
    {
        $this->scan();
        
        if (!$returnScannerClass) {
            $functions = array();
            foreach ($this->infos as $info) {
                if ($info['type'] == 'function') {
                    $functions[] = $info['name'];
                }
            }
            return $functions;
        } else {
            if ($returnScannerClass === true) {
                $returnScannerClass = 'Zend\Code\Scanner\FunctionScanner';
            }
            $scannerClass = new $returnScannerClass;
            // @todo
        }
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
