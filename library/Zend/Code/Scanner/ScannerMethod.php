<?php

namespace Zend\Code\Scanner;

class ScannerClass implements ScannerInterface
{
    protected $class = null;
    protected $uses = array();
    protected $name = null;
    protected $isFinal = false;
    protected $isAbstract = false;
    protected $isInterface = false;

    protected $tokens = array();
    protected $infos = array();
    
    public function __construct(array $methodTokens, $class = null, array $uses = array())
    {
        $this->tokens = $methodTokens;
        $this->class = $class;
        $this->uses = $uses;
    }
    
    public function scan()
    {
        if (!$this->tokens) {
            throw new \RuntimeException('No tokens were provided');
        }

        $currentNamespace = null;
        
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
                    } while (!(is_array($subToken) && $subToken[0] == T_FUNCTION) && !(is_string($subToken) && $subToken == '='));

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
        
        // find constants
        // find properties
        // find methods
        // var_dump($this);
    }
    
}