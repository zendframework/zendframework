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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Reflection;

use Zend\Code\Reflection,
    Zend\Code\NameInformation,
    Zend\Code\Scanner\CachingFileScanner;

/**
 * @uses       Reflector
 * @uses       \Zend\Loader
 * @uses       \Zend\Code\Reflection\ReflectionClass
 * @uses       \Zend\Code\Reflection\Exception
 * @uses       \Zend\Code\Reflection\ReflectionFunction
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FileReflection implements Reflection
{
    /**
     * @var string
     */
    protected $filePath        = null;

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
    protected $namespaces      = array();

    /**
     * @var string[]
     */
    protected $uses            = array();

    /**
     * @var string[]
     */
    protected $requiredFiles   = array();

    /**
     * @var ReflectionClass[]
     */
    protected $classes         = array();

    /**
     * @var FunctionReflection[]
     */
    protected $functions       = array();

    /**
     * @var string
     */
    //protected $contents        = null;

    /**
     * Constructor
     *
     * @param string $file
     * @return FileReflection
     */
    public function __construct($file)
    {
        $fileName = $file;

        if (($fileRealPath = realpath($fileName)) === false) {
            $fileRealPath = stream_resolve_include_path($fileName);
        }

        if (!$fileRealPath || !in_array($fileRealPath, get_included_files())) {
            throw new Exception\RuntimeException('File ' . $file . ' must be required before it can be reflected');
        }

        $this->filePath = $fileRealPath;
        $this->reflect();
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
        // @todo get file name from path
        return $this->filePath;
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
     * @return DocBlockReflection
     */
    public function getDocblock()
    {
        if (!($docComment = $this->getDocComment())) {
            return false;
        }
        $instance = new DocBlockReflection($docComment);
        return $instance;
    }

    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * getNamespace()
     *
     * @return string
     */
    public function getNamespace()
    {
        if (count($this->namespaces) > 0) {
            return $this->namespaces[0];
        }
        return null;
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
     * @return array Array of \Zend\Code\Reflection\ReflectionClass instances
     */
    public function getClasses()
    {
        $classes = array();
        foreach ($this->classes as $class) {
            $instance = new ClassReflection($class);
            $classes[] = $instance;
        }
        return $classes;
    }

    /**
     * Return the reflection functions of the functions found inside this file
     *
     * @return array Array of Zend_Reflection_Functions
     */
    public function getFunctions()
    {
        $functions = array();
        foreach ($this->functions as $function) {
            $instance = new FunctionReflection($function);
            $functions[] = $instance;
        }
        return $functions;
    }

    /**
     * Retrieve the reflection class of a given class found in this file
     *
     * @param  null|string $name
     * @return \Zend\Code\Reflection\ReflectionClass
     * @throws \Zend\Code\Reflection\Exception for invalid class name or invalid reflection class
     */
    public function getClass($name = null)
    {
        if ($name === null) {
            reset($this->classes);
            $selected = current($this->classes);
            $instance = new ClassReflection($selected);
            return $instance;
        }

        if (in_array($name, $this->classes)) {
            $instance = new ClassReflection($name);
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
     * Uses Zend\Code\Scanner\FileScanner to gather file information
     *
     * @return void
     */
    protected function reflect()
    {
        $scanner = new CachingFileScanner($this->filePath);
        $this->docComment = $scanner->getDocComment();
        $this->requiredFiles = $scanner->getIncludes();
        $this->classes = $scanner->getClassNames();
        $this->namespaces = $scanner->getNamespaces();
        $this->uses = $scanner->getUses();
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
