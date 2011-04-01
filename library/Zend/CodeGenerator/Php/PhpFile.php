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
 * @package    Zend_CodeGenerator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\CodeGenerator\Php;
use Zend\Reflection;

/**
 * @uses       \Zend\CodeGenerator\Php\AbstractPhp
 * @uses       \Zend\CodeGenerator\Php\PhpClass
 * @uses       \Zend\CodeGenerator\Php\Exception
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpFile extends AbstractPhp
{

    /**
     * @var array Array of \Zend\CodeGenerator\Php\PhpFile
     */
    protected static $_fileCodeGenerators = array();

    /**#@+
     * @var string
     */
    protected static $_markerDocblock = '/* Zend_CodeGenerator_Php_File-DocblockMarker */';
    protected static $_markerNamespace = '/* Zend_CodeGenerator_Php_File-NamespaceMarker */';
    protected static $_markerRequire = '/* Zend_CodeGenerator_Php_File-RequireMarker: {?} */';
    protected static $_markerClass = '/* Zend_CodeGenerator_Php_File-ClassMarker: {?} */';
    /**#@-*/

    /**
     * @var string
     */
    protected $_filename = null;

    /**
     * @var \Zend\CodeGenerator\PhpDocblock
     */
    protected $_docblock = null;

    /**
     * @var array
     */
    protected $_requiredFiles = array();

    /**
     * @var string
     */
    protected $_namespace = null;
    
    /**
     * @var array
     */
    protected $_uses = array();
    
    /**
     * @var array
     */
    protected $_classes = array();

    /**
     * @var string
     */
    protected $_body = null;

    /**
     * registerFileCodeGnereator()
     * 
     * A file code generator registry
     * 
     * @param PhpFile $fileCodeGenerator
     * @param string $fileName
     */
    public static function registerFileCodeGenerator(PhpFile $fileCodeGenerator, $fileName = null)
    {
        if ($fileName == null) {
            $fileName = $fileCodeGenerator->getFilename();
        }

        if ($fileName == '') {
            throw new Exception\InvalidArgumentException('FileName does not exist.');
        }

        // cannot use realpath since the file might not exist, but we do need to have the index
        // in the same DIRECTORY_SEPARATOR that realpath would use:
        $fileName = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $fileName);

        self::$_fileCodeGenerators[$fileName] = $fileCodeGenerator;

    }

    /**
     * fromReflectedFilePath() - use this if you intend on generating code generation objects based on the same file.
     * This will keep previous changes to the file in tact during the same PHP process
     *
     * @param string $filePath
     * @param bool $usePreviousCodeGeneratorIfItExists
     * @param bool $includeIfNotAlreadyIncluded
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public static function fromReflectedFileName($filePath, $usePreviousCodeGeneratorIfItExists = true, $includeIfNotAlreadyIncluded = true)
    {
        $realpath = realpath($filePath);

        if ($realpath === false) {
            if ( ($realpath = Reflection\file::findRealpathInIncludePath($filePath)) === false) {
                throw new Exception\InvalidArgumentException('No file for ' . $realpath . ' was found.');
            }
        }

        if ($usePreviousCodeGeneratorIfItExists && isset(self::$_fileCodeGenerators[$realpath])) {
            return self::$_fileCodeGenerators[$realpath];
        }

        if ($includeIfNotAlreadyIncluded && !in_array($realpath, get_included_files())) {
            include $realpath;
        }

        $codeGenerator = self::fromReflection(($fileReflector = new Reflection\ReflectionFile($realpath)));

        if (!isset(self::$_fileCodeGenerators[$fileReflector->getFileName()])) {
            self::$_fileCodeGenerators[$fileReflector->getFileName()] = $codeGenerator;
        }

        return $codeGenerator;
    }

    /**
     * fromReflection()
     *
     * @param \Zend\Reflection\ReflectionFile $reflectionFile
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public static function fromReflection(Reflection\ReflectionFile $reflectionFile)
    {
        $file = new self();

        $file->setSourceContent($reflectionFile->getContents());
        $file->setSourceDirty(false);

        $body = $reflectionFile->getContents();

        // @todo this whole area needs to be reworked with respect to how body lines are processed
        foreach ($reflectionFile->getClasses() as $class) {
            $phpClass = PhpClass::fromReflection($class);
            $phpClass->setPhpFile($file);
            $file->setClass($phpClass);
            $classStartLine = $class->getStartLine(true);
            $classEndLine = $class->getEndLine();

            $bodyLines = explode("\n", $body);
            $bodyReturn = array();
            for ($lineNum = 1; $lineNum <= count($bodyLines); $lineNum++) {
                if ($lineNum == $classStartLine) {
                    $bodyReturn[] = str_replace('?', $class->getName(), self::$_markerClass);  //'/* Zend_CodeGenerator_Php_File-ClassMarker: {' . $class->getName() . '} */';
                    $lineNum = $classEndLine;
                } else {
                    $bodyReturn[] = $bodyLines[$lineNum - 1]; // adjust for index -> line conversion
                }
            }
            $body = implode("\n", $bodyReturn);
            unset($bodyLines, $bodyReturn, $classStartLine, $classEndLine);
        }
        
        $namespace = $reflectionFile->getNamespace();
        if ($namespace != '') {
            $file->setNamespace($reflectionFile->getNamespace());
        }
        
        $uses = $reflectionFile->getUses();
        if ($uses) {
            $file->setUses($uses);
        }
        

        if (($reflectionFile->getDocComment() != '')) {
            $docblock = $reflectionFile->getDocblock();
            $file->setDocblock(PhpDocblock::fromReflection($docblock));

            $bodyLines = explode("\n", $body);
            $bodyReturn = array();
            for ($lineNum = 1; $lineNum <= count($bodyLines); $lineNum++) {
                if ($lineNum == $docblock->getStartLine()) {
                    $bodyReturn[] = str_replace('?', $class->getName(), self::$_markerDocblock);  //'/* Zend_CodeGenerator_Php_File-ClassMarker: {' . $class->getName() . '} */';
                    $lineNum = $docblock->getEndLine();
                } else {
                    $bodyReturn[] = $bodyLines[$lineNum - 1]; // adjust for index -> line conversion
                }
            }
            $body = implode("\n", $bodyReturn);
            unset($bodyLines, $bodyReturn, $classStartLine, $classEndLine);
        }

        $file->setBody($body);

        return $file;
    }

    /**
     * setDocblock() Set the docblock
     *
     * @param \Zend\CodeGenerator\PhpDocblock|array|string $docblock
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public function setDocblock($docblock)
    {
        if (is_string($docblock)) {
            $docblock = array('shortDescription' => $docblock);
        }

        if (is_array($docblock)) {
            $docblock = new PhpDocblock($docblock);
        } elseif (!$docblock instanceof PhpDocblock) {
            throw new Exception\InvalidArgumentException('setDocblock() is expecting either a string, array or an instance of Zend_CodeGenerator_Php_Docblock');
        }

        $this->_docblock = $docblock;
        return $this;
    }

    /**
     * Get docblock
     *
     * @return \Zend\CodeGenerator\PhpDocblock
     */
    public function getDocblock()
    {
        return $this->_docblock;
    }

    /**
     * setRequiredFiles
     *
     * @param array $requiredFiles
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public function setRequiredFiles($requiredFiles)
    {
        $this->_requiredFiles = $requiredFiles;
        return $this;
    }

    /**
     * getRequiredFiles()
     *
     * @return array
     */
    public function getRequiredFiles()
    {
        return $this->_requiredFiles;
    }

    /**
     * setClasses()
     *
     * @param array $classes
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public function setClasses(Array $classes)
    {
        foreach ($classes as $class) {
            $this->setClass($class);
        }
        return $this;
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
     * setNamespace()
     * 
     * @param $namespace
     * @return Zend\CodeGenerator\Php\PhpFile
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }
    
    /**
     * getUses()
     * 
     * Returns an array with the first element the use statement, second is the as part.
     * If $withResolvedAs is set to true, there will be a third element that is the 
     * "resolved" as statement, as the second part is not required in use statements
     * 
     * @param $withResolvedAs
     * @return array
     */
    public function getUses($withResolvedAs = false)
    {
        $uses = $this->_uses;
        if ($withResolvedAs) {
            for ($useIndex = 0; $useIndex < count($uses); $useIndex++) {
                if ($uses[$useIndex][1] == '') {
                    if (($lastSeparator = strrpos($uses[$useIndex][0], '\\')) !== false) {
                        $uses[$useIndex][2] = substr($uses[$useIndex][0], $lastSeparator+1);
                    } else {
                        $uses[$useIndex][2] = $uses[$useIndex][0];
                    }
                } else {
                    $uses[$useIndex][2] = $uses[$useIndex][1];
                }
            }
        }
        return $uses;
    }
    
    /**
     * setUses()
     * 
     * @param $uses
     * @return Zend\CodeGenerator\Php\PhpFile
     */
    public function setUses(Array $uses)
    {
        foreach ($uses as $use) {
            $this->setUse($use[0], $use[1]);
        }
        return $this;
    }
    
    /**
     * setUse()
     * 
     * @param $use
     * @param $as
     * @return Zend\CodeGenerator\Php\PhpFile
     */
    public function setUse($use, $as = null)
    {
        $this->_uses[] = array($use, $as);
        return $this;
    }
    
    /**
     * getClass()
     *
     * @param string $name
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function getClass($name = null)
    {
        if ($name == null) {
            reset($this->_classes);
            return current($this->_classes);
        }

        return $this->_classes[$name];
    }

    /**
     * setClass()
     *
     * @param \Zend\CodeGenerator\Php\PhpClass|array $class
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public function setClass($class)
    {
        if (is_array($class)) {
            $class = new PhpClass($class);
            $className = $class->getName();
        } elseif ($class instanceof PhpClass) {
            $className = $class->getName();
        } else {
            throw new Exception\InvalidArgumentException('Expecting either an array or an instance of Zend_CodeGenerator_Php_Class');
        }

        // @todo check for dup here

        $this->_classes[$className] = $class;
        return $this;
    }

    /**
     * setFilename()
     *
     * @param string $filename
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public function setFilename($filename)
    {
        $this->_filename = $filename;
        return $this;
    }

    /**
     * getFilename()
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * getClasses()
     *
     * @return array Array of \Zend\CodeGenerator\Php\PhpClass
     */
    public function getClasses()
    {
        return $this->_classes;
    }

    /**
     * setBody()
     *
     * @param string $body
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * getBody()
     *
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * isSourceDirty()
     *
     * @return bool
     */
    public function isSourceDirty()
    {
        if (($docblock = $this->getDocblock()) && $docblock->isSourceDirty()) {
            return true;
        }

        foreach ($this->_classes as $class) {
            if ($class->isSourceDirty()) {
                return true;
            }
        }

        return parent::isSourceDirty();
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        if ($this->isSourceDirty() === false) {
            return $this->_sourceContent;
        }

        $output = '';

        // start with the body (if there), or open tag
        if (preg_match('#(?:\s*)<\?php#', $this->getBody()) == false) {
            $output = '<?php' . self::LINE_FEED;
        }
        
        // if there are markers, put the body into the output
        $body = $this->getBody();
        if (preg_match('#/\* Zend_CodeGenerator_Php_File-(.*?)Marker:#', $body)) {
            $output .= $body;
            $body    = '';
        }

        // Add file docblock, if any
        if (null !== ($docblock = $this->getDocblock())) {
            $docblock->setIndentation('');
            $regex = preg_quote(self::$_markerDocblock, '#');
            if (preg_match('#'.$regex.'#', $output)) {
                $output  = preg_replace('#'.$regex.'#', $docblock->generate(), $output, 1);
            } else {
                $output .= $docblock->generate() . self::LINE_FEED;
            }
        }

        // newline
        $output .= self::LINE_FEED;

        // namespace, if any
        if ($namespace = $this->getNamespace()) {
            $output .= sprintf('namespace %s;%s', $namespace, str_repeat(self::LINE_FEED, 2));
        }

        // process required files
        // @todo marker replacement for required files
        $requiredFiles = $this->getRequiredFiles();
        if (!empty($requiredFiles)) {
            foreach ($requiredFiles as $requiredFile) {
                $output .= 'require_once \'' . $requiredFile . '\';' . self::LINE_FEED;
            }

            $output .= self::LINE_FEED;
        }

        // process import statements
        $uses = $this->getUses();
        if (!empty($uses)) {
            foreach ($uses as $use) {
                list($import, $alias) = $use;
                if (null === $alias) {
                    $output .= sprintf('use %s;%s', $import, self::LINE_FEED);
                } else {
                    $output .= sprintf('use %s as %s;%s', $import, $alias, self::LINE_FEED);
                }
            }
            $output.= self::LINE_FEED;
        }

        // process classes
        $classes = $this->getClasses();
        if (!empty($classes)) {
            foreach ($classes as $class) {
                $regex = str_replace('?', $class->getName(), self::$_markerClass);
                $regex = preg_quote($regex, '#');
                if (preg_match('#'.$regex.'#', $output)) {
                    $output = preg_replace('#'.$regex.'#', $class->generate(), $output, 1);
                } else {
                    $output .= $class->generate() . self::LINE_FEED;
                }
            }

        }

        if (!empty($body)) {

            // add an extra space betwee clsses and
            if (!empty($classes)) {
                $output .= self::LINE_FEED;
            }

            $output .= $body;
        }

        return $output;
    }

    public function write()
    {
        if ($this->_filename == '' || !is_writable(dirname($this->_filename))) {
            throw new Exception\RuntimeException('This code generator object is not writable.');
        }
        file_put_contents($this->_filename, $this->generate());
        return $this;
    }

}
