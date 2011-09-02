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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Reflection;

use Zend\Code\Reflection;

/**
 * @uses       Reflector
 * @uses       \Zend\Loader
 * @uses       \Zend\Code\Reflection\ReflectionClass
 * @uses       \Zend\Code\Reflection\Exception
 * @uses       \Zend\Code\Reflection\ReflectionFunction
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReflectionFile implements Reflection
{
    /**
     * @var string
     */
    protected $filepath        = null;

    /**
     * @var string
     */
    protected $docComment      = null;

    /**
     * @var int
     */
    protected $startLine       = 1;

    /**
     * @var int
     */
    protected $endLine         = null;

    /**
     * @var string
     */
    protected $namespace       = null;

    /**
     * @var string[]
     */
    protected $uses            = array();

    /**
     * @var string[]
     */
    protected $requiredFiles   = array();

    /**
     * @var \Zend\Code\Reflection\ReflectionClass[]
     */
    protected $classes         = array();

    /**
     * @var \Zend\Code\Reflection\ReflectionFunction[]
     */
    protected $functions       = array();

    /**
     * @var string
     */
    protected $contents        = null;

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
            throw new Exception\RuntimeException('File ' . $file . ' must be required before it can be reflected');
        }

        $this->_fileName = $fileRealpath;
        $this->contents = file_get_contents($this->_fileName);
        $this->reflect();
    }

    /**
     * Find realpath of file based on include_path
     *
     * @param  string $fileName
     * @return string|boolean On success, the resolved absolute filename is returned.
     *                        On failure, FALSE is returned.
     */
    public static function findRealpathInIncludePath($fileName)
    {
        return stream_resolve_include_path($fileName);
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
        return $this->startLine;
    }

    /**
     * Get the end line / number of lines
     *
     * @return int
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * Return the doc comment
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->docComment;
    }

    /**
     * Return the docblock
     *
     * @param  string $reflectionClass Reflection class to use
     * @return Zend_Reflection_Docblock
     */
    public function getDocblock($reflectionClass = 'Zend\Code\Reflection\ReflectionDocblock')
    {
        $instance = new $reflectionClass($this);
        if (!$instance instanceof ReflectionDocblock) {
            throw new Exception\InvalidArgumentException('Invalid reflection class specified; must extend Zend_Reflection_Docblock');
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
        return $this->namespace;
    }

    /**
     * getUses()
     *
     * @return array
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * Return the reflection classes of the classes found inside this file
     *
     * @param  string $reflectionClass Name of reflection class to use for instances
     * @return array Array of \Zend\Code\Reflection\ReflectionClass instances
     */
    public function getClasses($reflectionClass = 'Zend\Code\Reflection\ReflectionClass')
    {
        $classes = array();
        foreach ($this->classes as $class) {
            $instance = new $reflectionClass($class);
            if (!$instance instanceof ReflectionClass) {
                throw new Exception\InvalidArgumentException('Invalid reflection class provided; must extend Zend\Code\Reflection\ReflectionClass');
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
    public function getFunctions($reflectionClass = 'Zend\Code\Reflection\ReflectionFunction')
    {
        $functions = array();
        foreach ($this->functions as $function) {
            $instance = new $reflectionClass($function);
            if (!$instance instanceof ReflectionFunction) {
                throw new Exception\InvalidArgumentException('Invalid reflection class provided; must extend Zend\Code\Reflection\ReflectionFunction');
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
     * @return \Zend\Code\Reflection\ReflectionClass
     * @throws \Zend\Code\Reflection\Exception for invalid class name or invalid reflection class
     */
    public function getClass($name = null, $reflectionClass = 'Zend\Code\Reflection\ReflectionClass')
    {
        if ($name === null) {
            reset($this->classes);
            $selected = current($this->classes);
            $instance = new $reflectionClass($selected);
            if (!$instance instanceof ReflectionClass) {
                throw new Exception\InvalidArgumentException('Invalid reflection class given; must extend Zend_Reflection_Class');
            }
            return $instance;
        }

        if (in_array($name, $this->classes)) {
            $instance = new $reflectionClass($name);
            if (!$instance instanceof ReflectionClass) {
                throw new Exception\InvalidArgumentException('Invalid reflection class given; must extend Zend_Reflection_Class');
            }
            return $instance;
        }

        throw new Exception\InvalidArgumentException('Class by name ' . $name . ' not found.');
    }

    /**
     * Return the full contents of file
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    public function toString()
    {
        return ''; // @todo
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
    protected function reflect()
    {
        $contents = $this->contents;
        $tokens   = token_get_all($contents);

        $functionTrapped = false;
        $classTrapped    = false;
        $requireTrapped  = false;
        $namespaceTrapped = false;
        $useTrapped = false;
        $useAsTrapped = false;
        $useIndex = 0;
        $openBraces = 0;

        $this->checkFileDocBlock($tokens);

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
                        $this->functions[] = ($this->namespace) ? $this->namespace . $value : $value;
                        $functionTrapped = false;
                    } elseif ($classTrapped) {
                        $this->classes[] = ($this->namespace) ? $this->namespace . $value : $value;
                        $classTrapped = false;
                    } elseif ($namespaceTrapped) {
                        $this->namespace .= $value . '\\';
                    } elseif ($useAsTrapped) {
                        if (!isset($this->uses[$useIndex])) {
                            $this->uses[$useIndex] = array();
                        }
                        if (!isset($this->uses[$useIndex]['as'])) {
                            $this->uses[$useIndex]['as'] = '';
                        }
                        $this->uses[$useIndex]['as'] .= $value . '\\';
                    } elseif ($useTrapped) {
                        $this->uses[$useIndex]['namespace'] .= $value . '\\';
                    }
                    continue;

                // Required file names are T_CONSTANT_ENCAPSED_STRING
                case T_CONSTANT_ENCAPSED_STRING:
                    if ($requireTrapped) {
                        $this->requiredFiles[] = $value ."\n";
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
                    $this->uses[$useIndex] = array(
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
        foreach ($this->uses as $useIndex => $useInfo) {
            if (!isset($this->uses[$useIndex]['namespace'])) {
                $this->uses[$useIndex]['namespace'] = '';
            }
            $this->uses[$useIndex]['namespace'] = rtrim($this->uses[$useIndex]['namespace'], '\\');
            if (!isset($this->uses[$useIndex]['as'])) {
                $this->uses[$useIndex]['as'] = '';
            }
            $this->uses[$useIndex]['as'] = rtrim($this->uses[$useIndex]['as'], '\\');

            if ($this->uses[$useIndex]['as'] == '') {
                if (($lastSeparator = strrpos($this->uses[$useIndex]['namespace'], '\\')) !== false) {
                    $this->uses[$useIndex]['asResolved'] = substr($this->uses[$useIndex]['namespace'], $lastSeparator+1);
                } else {
                    $this->uses[$useIndex]['asResolved'] = $this->uses[$useIndex]['namespace'];
                }
            } else {
                $this->uses[$useIndex]['asResolved'] = $this->uses[$useIndex]['as'];
            }

        }


        $this->endLine = count(explode("\n", $this->contents));
    }

    /**
     * Validate / check a file level docblock
     *
     * @param  array $tokens Array of tokenizer tokens
     * @return void
     */
    protected function checkFileDocBlock($tokens) {
        foreach ($tokens as $token) {
            $type    = $token[0];
            $value   = $token[1];
            $lineNum = $token[2];
            if(($type == T_OPEN_TAG) || ($type == T_WHITESPACE)) {
                continue;
            } elseif ($type == T_DOC_COMMENT) {
                $this->docComment = $value;
                $this->startLine  = $lineNum + substr_count($value, "\n") + 1;
                return;
            } else {
                // Only whitespace is allowed before file docblocks
                return;
            }
        }
    }
}
