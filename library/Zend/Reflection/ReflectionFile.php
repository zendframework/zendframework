<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Reflection;

/**
 * @uses       Reflector
 * @uses       \Zend\Loader
 * @uses       \Zend\Reflection\ReflectionClass
 * @uses       \Zend\Reflection\Exception
 * @uses       \Zend\Reflection\ReflectionFunction
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReflectionFile implements \Reflector
{
    /**
     * @var string
     */
    protected $_filepath        = null;

    /**
     * @var string
     */
    protected $_docComment      = null;

    /**
     * @var int
     */
    protected $_startLine       = 1;

    /**
     * @var int
     */
    protected $_endLine         = null;

    /**
     * @var string
     */
    protected $_namespace       = null;
    
    /**
     * @var string[]
     */
    protected $_uses            = array();
    
    /**
     * @var string[]
     */
    protected $_requiredFiles   = array();

    /**
     * @var \Zend\Reflection\ReflectionClass[]
     */
    protected $_classes         = array();

    /**
     * @var \Zend\Reflection\ReflectionFunction[]
     */
    protected $_functions       = array();

    /**
     * @var string
     */
    protected $_contents        = null;

    /**
     * Constructor
     *
     * @param  string $file
     * @return void
     */
    public function __construct($file)
    {
        $fileName = $file;

        if (($fileRealpath = realpath($fileName)) === false) {
            $fileRealpath = self::findRealpathInIncludePath($file);
        }

        if (!$fileRealpath || !in_array($fileRealpath, get_included_files())) {
            throw new Exception('File ' . $file . ' must be required before it can be reflected');
        }

        $this->_fileName = $fileRealpath;
        $this->_contents = file_get_contents($this->_fileName);
        $this->_reflect();
    }

    /**
     * Find realpath of file based on include_path
     *
     * @param  string $fileName
     * @return string
     */
    public static function findRealpathInIncludePath($fileName)
    {
        $includePaths = \Zend\Loader::explodeIncludePath();
        while (count($includePaths) > 0) {
            $filePath = array_shift($includePaths) . DIRECTORY_SEPARATOR . $fileName;

            if ( ($foundRealpath = realpath($filePath)) !== false) {
                break;
            }
        }

        return $foundRealpath;
    }

    /**
     * Export
     *
     * Required by the Reflector interface.
     *
     * @todo   What should this do?
     * @return null
     */
    public static function export()
    {
        return null;
    }

    /**
     * Return the file name of the reflected file
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * Get the start line - Always 1, staying consistent with the Reflection API
     *
     * @return int
     */
    public function getStartLine()
    {
        return $this->_startLine;
    }

    /**
     * Get the end line / number of lines
     *
     * @return int
     */
    public function getEndLine()
    {
        return $this->_endLine;
    }

    /**
     * Return the doc comment
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->_docComment;
    }

    /**
     * Return the docblock
     *
     * @param  string $reflectionClass Reflection class to use
     * @return Zend_Reflection_Docblock
     */
    public function getDocblock($reflectionClass = '\Zend\Reflection\ReflectionDocblock')
    {
        $instance = new $reflectionClass($this);
        if (!$instance instanceof ReflectionDocblock) {
            throw new Exception('Invalid reflection class specified; must extend Zend_Reflection_Docblock');
        }
        return $instance;
    }

    /**
     * getNamespace()
     * 
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }
    
    /**
     * getUses()
     * 
     * @return array
     */
    public function getUses()
    {
        return $this->_uses;
    }
    
    /**
     * Return the reflection classes of the classes found inside this file
     *
     * @param  string $reflectionClass Name of reflection class to use for instances
     * @return array Array of \Zend\Reflection\ReflectionClass instances
     */
    public function getClasses($reflectionClass = '\Zend\Reflection\ReflectionClass')
    {
        $classes = array();
        foreach ($this->_classes as $class) {
            $instance = new $reflectionClass($class);
            if (!$instance instanceof ReflectionClass) {
                throw new Exception('Invalid reflection class provided; must extend Zend\Reflection\ReflectionClass');
            }
            $classes[] = $instance;
        }
        return $classes;
    }

    /**
     * Return the reflection functions of the functions found inside this file
     *
     * @param  string $reflectionClass Name of reflection class to use for instances
     * @return array Array of Zend_Reflection_Functions
     */
    public function getFunctions($reflectionClass = '\Zend\Reflection\ReflectionFunction')
    {
        $functions = array();
        foreach ($this->_functions as $function) {
            $instance = new $reflectionClass($function);
            if (!$instance instanceof ReflectionFunction) {
                throw new Exception('Invalid reflection class provided; must extend Zend\Reflection\ReflectionFunction');
            }
            $functions[] = $instance;
        }
        return $functions;
    }

    /**
     * Retrieve the reflection class of a given class found in this file
     *
     * @param  null|string $name
     * @param  string $reflectionClass Reflection class to use when creating reflection instance
     * @return \Zend\Reflection\ReflectionClass
     * @throws \Zend\Reflection\Exception for invalid class name or invalid reflection class
     */
    public function getClass($name = null, $reflectionClass = '\Zend\Reflection\ReflectionClass')
    {
        if ($name === null) {
            reset($this->_classes);
            $selected = current($this->_classes);
            $instance = new $reflectionClass($selected);
            if (!$instance instanceof ReflectionClass) {
                throw new Exception('Invalid reflection class given; must extend Zend_Reflection_Class');
            }
            return $instance;
        }

        if (in_array($name, $this->_classes)) {
            $instance = new $reflectionClass($name);
            if (!$instance instanceof ReflectionClass) {
                throw new Exception('Invalid reflection class given; must extend Zend_Reflection_Class');
            }
            return $instance;
        }

        throw new Exception('Class by name ' . $name . ' not found.');
    }

    /**
     * Return the full contents of file
     *
     * @return string
     */
    public function getContents()
    {
        return $this->_contents;
    }

    /**
     * Serialize to string
     *
     * Required by the Reflector interface
     *
     * @todo   What should this serialization look like?
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    /**
     * This method does the work of "reflecting" the file
     *
     * Uses PHP's tokenizer to perform file reflection.
     *
     * @return void
     */
    protected function _reflect()
    {
        $contents = $this->_contents;
        $tokens   = token_get_all($contents);

        $functionTrapped = false;
        $classTrapped    = false;
        $requireTrapped  = false;
        $namespaceTrapped = false;
        $useTrapped = false;
        $useAsTrapped = false;
        $useIndex = 0;
        $openBraces = 0;

        $this->_checkFileDocBlock($tokens);

        foreach ($tokens as $token) {
            /*
             * Tokens are characters representing symbols or arrays
             * representing strings. The keys/values in the arrays are
             *
             * - 0 => token id,
             * - 1 => string,
             * - 2 => line number
             *
             * Token ID's are explained here:
             * http://www.php.net/manual/en/tokens.php.
             */

            if (is_array($token)) {
                $type    = $token[0];
                $value   = $token[1];
                $lineNum = $token[2];
            } else {
                // It's a symbol
                // Maintain the count of open braces
                if ($token == '{') {
                    $openBraces++;
                } elseif ($token == '}') {
                    $openBraces--;
                } elseif ($token == ';' && $namespaceTrapped == true) {
                    $namespaceTrapped = false;
                } elseif ($token == ';' && $useTrapped == true) {
                    $useTrapped = $useAsTrapped = false;
                    $useIndex++;
                }
                continue;
            }

            switch ($type) {

                // Name of something
                case T_STRING:
                    if ($functionTrapped) {
                        $this->_functions[] = ($this->_namespace) ? $this->_namespace . $value : $value;
                        $functionTrapped = false;
                    } elseif ($classTrapped) {
                        $this->_classes[] = ($this->_namespace) ? $this->_namespace . $value : $value;
                        $classTrapped = false;
                    } elseif ($namespaceTrapped) {
                        $this->_namespace .= $value . '\\';
                    } elseif ($useAsTrapped) {
                        $this->_uses[$useIndex]['as'] .= $value . '\\';
                    } elseif ($useTrapped) {
                        $this->_uses[$useIndex]['namespace'] .= $value . '\\';
                    }
                    continue;

                // Required file names are T_CONSTANT_ENCAPSED_STRING
                case T_CONSTANT_ENCAPSED_STRING:
                    if ($requireTrapped) {
                        $this->_requiredFiles[] = $value ."\n";
                        $requireTrapped = false;
                    }
                    continue;

                // namespace
                case T_NAMESPACE:
                    $namespaceTrapped = true;
                    continue;
                    
                // use
                case T_USE:
                    $useTrapped = true;
                    $this->_uses[$useIndex] = array(
                        'namespace' => '',
                        'as' => ''
                        );
                    continue;
                    
                // use (as)
                case T_AS:
                    $useAsTrapped = true;
                    continue;
                    
                // Functions
                case T_FUNCTION:
                    if ($openBraces == 0) {
                        $functionTrapped = true;
                    }
                    break;

                // Classes
                case T_CLASS:
                case T_INTERFACE:
                    $classTrapped = true;
                    break;

                // All types of requires
                case T_REQUIRE:
                case T_REQUIRE_ONCE:
                case T_INCLUDE:
                case T_INCLUDE_ONCE:
                    $requireTrapped = true;
                    break;

                // Default case: do nothing
                default:
                    break;
            }
        }
        
        // cleanup uses
        foreach ($this->_uses as $useIndex => $useInfo) {
            $this->_uses[$useIndex]['namespace'] = rtrim($this->_uses[$useIndex]['namespace'], '\\');
            $this->_uses[$useIndex]['as'] = rtrim($this->_uses[$useIndex]['as'], '\\');
            
            if ($this->_uses[$useIndex]['as'] == '') {
                if (($lastSeparator = strrpos($this->_uses[$useIndex]['namespace'], '\\')) !== false) {
                    $this->_uses[$useIndex]['asResolved'] = substr($this->_uses[$useIndex]['namespace'], $lastSeparator+1);
                } else {
                    $this->_uses[$useIndex]['asResolved'] = $this->_uses[$useIndex]['namespace'];
                }
            } else {
                $this->_uses[$useIndex]['asResolved'] = $this->_uses[$useIndex]['as'];
            }
            
        }
        

        $this->_endLine = count(explode("\n", $this->_contents));
    }

    /**
     * Validate / check a file level docblock
     *
     * @param  array $tokens Array of tokenizer tokens
     * @return void
     */
    protected function _checkFileDocBlock($tokens) {
        foreach ($tokens as $token) {
            $type    = $token[0];
            $value   = $token[1];
            $lineNum = $token[2];
            if(($type == T_OPEN_TAG) || ($type == T_WHITESPACE)) {
                continue;
            } elseif ($type == T_DOC_COMMENT) {
                $this->_docComment = $value;
                $this->_startLine  = $lineNum + substr_count($value, "\n") + 1;
                return;
            } else {
                // Only whitespace is allowed before file docblocks
                return;
            }
        }
    }
}
