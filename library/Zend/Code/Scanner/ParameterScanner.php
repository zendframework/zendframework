<?php

namespace Zend\Code\Scanner;

class ParameterScanner
{
    protected $isScanned                = false;

    protected $declaringScannerClass    = null;
    protected $declaringClass           = null;
    protected $declaringScannerFunction = null;
    protected $declaringFunction        = null;
    protected $defaultValue             = null;
    protected $class                    = null;
    protected $name                     = null;
    protected $position                 = null;
    protected $isArray                  = false;
    protected $isDefaultValueAvailable  = false;
    protected $isOptional               = false;
    protected $isPassedByReference      = false;

    protected $tokens                   = null;
    protected $namespace                = null;
    protected $uses                     = array();
    
    public function __construct(array $parameterTokens, $namespace = null, array $uses = array())
    {
        $this->tokens = $parameterTokens;
        $this->namespace = $namespace;
        $this->uses   = $uses;
    }
    
    public function setDeclaringClass($class)
    {
        $this->declaringClass = $class;
    }
    
    public function setDeclaringScannerClass(ClassScanner $scannerClass)
    {
        $this->declaringScannerClass = $scannerClass;
    }
    
    public function setDeclaringFunction($function)
    {
        $this->declaringFunction = $function;
    }
    
    public function setDeclaringScannerFunction(MethodScanner $scannerFunction)
    {
        $this->declaringScannerFunction = $scannerFunction;
    }
    
    public function setPosition($position)
    {
        $this->position = $position;
    }
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }
        
        $tokenIndex = 0;
        $token = $this->tokens[$tokenIndex];
        
        if ($token[0] !== T_VARIABLE) {
            while (true) {
                if ($token[0] == T_WHITESPACE) {
                    break;
                }
                $this->class .= $token[1];
                $token = $this->tokens[++$tokenIndex];
            }
        }
        
        if (strtolower($this->class) == 'array') {
            $this->isArray = true;
            $this->class = null;
        } elseif ($this->class !== null) {
            
            $data = (object) array(
                'namespace' => $this->namespace,
                'uses'      => $this->uses,
            );
            
            Util::resolveImports($this->class, null, $data);
            
            /*
            $namespace = (($decClassLastSlash = strrpos($this->declaringClass, '\\')) !== false) 
                       ? substr($this->declaringClass, 0, $decClassLastSlash) 
                       : null;
            if ((!$this->uses && !$namespace) || strlen($this->class) <= 0 || $this->class{0} == '\\') {
                $this->class = ltrim($this->class, '\\');
            } else {
                if ($namespace || $this->uses) {
                    $firstPartEnd = (strpos($this->class, '\\')) ?: strlen($this->class-1);
                    $firstPart = substr($this->class, 0, $firstPartEnd);
                    if (array_key_exists($firstPart, $this->uses)) {
                        $this->class = substr_replace($this->class, $this->uses[$firstPart], 0, $firstPartEnd);
                    } elseif ($namespace) {
                        $this->class = $namespace . '\\' . $this->class;
                    }
                }
            }
            */
        }
        
        if ($token[0] == T_WHITESPACE) {
            $token = $this->tokens[++$tokenIndex];
        }
        
        if (is_string($token) && $token == '&') {
            $this->isPassedByReference = true;
            $token = $this->tokens[++$tokenIndex];
        }
        
        // next token is sure a T_VARIABLE
        $this->name = ltrim($token[1], '$');
        $token = (isset($this->tokens[++$tokenIndex])) 
               ? $this->tokens[$tokenIndex] 
               : null;
        
        if (!$token) {
            $this->isScanned = true;
            return;
        }
        
        // move past whitespace if it exist
        if ($token[0] == T_WHITESPACE) {
            $token = (isset($this->tokens[++$tokenIndex])) 
                   ? $this->tokens[$tokenIndex] 
                   : null;
        }
        
        if (!$token) {
            $this->isScanned = true;
            return;
        }
        
        if (!(is_string($token) && $token == '=')) {
            $this->isScanned = true;
            return;
        }
        
        // get past =
        $token = $this->tokens[++$tokenIndex];
        
        // move past whitespace if it exist
        if ($token[0] == T_WHITESPACE) {
            $token = (isset($this->tokens[++$tokenIndex])) 
                   ? $this->tokens[$tokenIndex] 
                   : null;
        }
        
        $this->isOptional              = true;
        $this->isDefaultValueAvailable = true;
        
        do {
            $this->defaultValue .= ((is_array($token)) ? $token[1] : $token);
            $token = (isset($this->tokens[++$tokenIndex])) 
                   ? $this->tokens[$tokenIndex] 
                   : false;
        } while ($token);
        
        if ($this->class) {
            /*
            $uses = $this->uses;
            if ($this->shortInterfaces) {
                $this->interfaces = $this->shortInterfaces;
                $data = (object) array('namespace' => $namespace, 'uses' => $uses);
                array_walk($this->interfaces, array('Zend\Code\Scanner\Util', 'resolveImports'), $data);
            }
            */
        }
        
        $this->isScanned = true;
    }
	/**
     * @return the $declaringScannerClass
     */
    public function getDeclaringScannerClass()
    {
        return $this->declaringScannerClass;
    }

	/**
     * @return the $declaringClass
     */
    public function getDeclaringClass()
    {
        return $this->declaringClass;
    }

	/**
     * @return the $declaringScannerFunction
     */
    public function getDeclaringScannerFunction()
    {
        return $this->declaringScannerFunction;
    }

	/**
     * @return the $declaringFunction
     */
    public function getDeclaringFunction()
    {
        return $this->declaringFunction;
    }

	/**
     * @return the $defaultValue
     */
    public function getDefaultValue()
    {
        $this->scan();
        return $this->defaultValue;
    }

	/**
     * @return the $class
     */
    public function getClass()
    {
        $this->scan();
        return $this->class;
    }

	/**
     * @return the $name
     */
    public function getName()
    {
        $this->scan();
        return $this->name;
    }

	/**
     * @return the $position
     */
    public function getPosition()
    {
        $this->scan();
        return $this->position;
    }

	/**
     * @return the $isArray
     */
    public function isArray()
    {
        $this->scan();
        return $this->isArray;
    }

	/**
     * @return the $isDefaultValueAvailable
     */
    public function isDefaultValueAvailable()
    {
        $this->scan();
        return $this->isDefaultValueAvailable;
    }

	/**
     * @return the $isOptional
     */
    public function isOptional()
    {
        $this->scan();
        return $this->isOptional;
    }

	/**
     * @return the $isPassedByReference
     */
    public function isPassedByReference()
    {
        $this->scan();
        return $this->isPassedByReference;
    }

    
}
