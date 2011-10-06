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
namespace Zend\Code\Generator;
use Zend\Code\Reflection\FileReflection;

/**
 * @uses       \Zend\Code\Generator\AbstractPhp
 * @uses       \Zend\Code\Generator\PhpClass
 * @uses       \Zend\Code\Generator\Exception
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FileGenerator extends AbstractGenerator
{

    /**
     * @var string
     */
    protected $filename = null;

    /**
     * @var \Zend\Code\GeneratorDocblock
     */
    protected $docblock = null;

    /**
     * @var array
     */
    protected $requiredFiles = array();

    /**
     * @var string
     */
    protected $namespace = null;

    /**
     * @var array
     */
    protected $uses = array();

    /**
     * @var array
     */
    protected $classes = array();

    /**
     * @var string
     */
    protected $body = null;

    /**
     * fromReflectedFilePath() - use this if you intend on generating code generation objects based on the same file.
     * This will keep previous changes to the file in tact during the same PHP process
     *
     * @param string $filePath
     * @param bool $includeIfNotAlreadyIncluded
     * @return FileGenerator
     */
    public static function fromReflectedFileName($filePath, $includeIfNotAlreadyIncluded = true)
    {
        $realpath = realpath($filePath);

        if ($realpath === false) {
            if ( ($realpath = FileReflection::findRealpathInIncludePath($filePath)) === false) {
                throw new Exception\InvalidArgumentException('No file for ' . $realpath . ' was found.');
            }
        }

        if ($includeIfNotAlreadyIncluded && !in_array($realpath, get_included_files())) {
            include $realpath;
        }

        $codeGenerator = self::fromReflection(($fileReflector = new FileReflection($realpath)));

        return $codeGenerator;
    }

    /**
     * fromReflection()
     *
     * @param FileReflection $fileReflection
     * @return FileGenerator
     */
    public static function fromReflection(FileReflection $fileReflection)
    {
        $file = new self();

        $file->setSourceContent($fileReflection->getContents());
        $file->setSourceDirty(false);

        $body = $fileReflection->getContents();

        foreach ($fileReflection->getClasses() as $class) {
            /* @var $class \Zend\Code\Reflection\ReflectionClass */
            $phpClass = ClassGenerator::fromReflection($class);
            $phpClass->setContainingFileGenerator($file);
            $file->setClass($phpClass);
            $classStartLine = $class->getStartLine(true);
            $classEndLine = $class->getEndLine();

            $bodyLines = explode("\n", $body);
            $bodyReturn = array();
            for ($lineNum = 1; $lineNum <= count($bodyLines); $lineNum++) {
                if ($lineNum == $classStartLine) {

                    $bodyReturn[] = str_replace(
                        '?',
                        $class->getName(),
                        '/* Zend_CodeGenerator_Php_File-ClassMarker: {?} */'
                    );

                    $lineNum = $classEndLine;
                } else {
                    $bodyReturn[] = $bodyLines[$lineNum - 1]; // adjust for index -> line conversion
                }
            }
            $body = implode("\n", $bodyReturn);
            unset($bodyLines, $bodyReturn, $classStartLine, $classEndLine);
        }

        $namespace = $fileReflection->getNamespace();
        if ($namespace != '') {
            $file->setNamespace($fileReflection->getNamespace());
        }

        $uses = $fileReflection->getUses();
        if ($uses) {
            $file->setUses($uses);
        }

        if (($fileReflection->getDocComment() != '')) {
            /* @var $docblock \DocBlockReflection\Code\Reflection\ReflectionDocblock */
            $docblock = $fileReflection->getDocblock();
            $file->setDocblock(DocblockGenerator::fromReflection($docblock));

            $bodyLines = explode("\n", $body);
            $bodyReturn = array();
            for ($lineNum = 1; $lineNum <= count($bodyLines); $lineNum++) {
                if ($lineNum == $docblock->getStartLine()) {
                    $bodyReturn[] = str_replace(
                        '?',
                        $class->getName(),
                        '/* Zend_CodeGenerator_Php_File-DocblockMarker */'
                    );
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

    public static function fromArray(array $values)
    {
        $fileGenerator = new static;
        foreach ($values as $name => $value) {
            switch ($name) {
                case 'filename':
                    $fileGenerator->filename = $value;
                    continue;
                case 'class':
                    $fileGenerator->setClass(($value instanceof ClassGenerator) ?: ClassGenerator::fromArray($value));
                    continue;
                default:
                    if (property_exists($fileGenerator, $name)) {
                        $fileGenerator->{$name} = $value;
                    } elseif (method_exists($fileGenerator, 'set' . $name)) {
                        $fileGenerator->{'set' . $name}($value);
                    }
            }
        }
        return $fileGenerator;
    }


    /**
     * setDocblock() Set the docblock
     *
     * @param DocblockGenerator|string $docblock
     * @return FileGenerator
     */
    public function setDocblock($docblock)
    {
        if (is_string($docblock)) {
            $docblock = array('shortDescription' => $docblock);
        }

        if (is_array($docblock)) {
            $docblock = new DocblockGenerator($docblock);
        } elseif (!$docblock instanceof DocblockGenerator) {
            throw new Exception\InvalidArgumentException('setDocblock() is expecting either a string, array or an instance of Zend_CodeGenerator_Php_Docblock');
        }

        $this->docblock = $docblock;
        return $this;
    }

    /**
     * Get docblock
     *
     * @return DocblockGenerator
     */
    public function getDocblock()
    {
        return $this->docblock;
    }

    /**
     * setRequiredFiles
     *
     * @param array $requiredFiles
     * @return FileGenerator
     */
    public function setRequiredFiles(array $requiredFiles)
    {
        $this->requiredFiles = $requiredFiles;
        return $this;
    }

    /**
     * getRequiredFiles()
     *
     * @return array
     */
    public function getRequiredFiles()
    {
        return $this->requiredFiles;
    }

    /**
     * setClasses()
     *
     * @param array $classes
     * @return FileGenerator
     */
    public function setClasses(array $classes)
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
        return $this->namespace;
    }

    /**
     * setNamespace()
     * 
     * @param $namespace
     * @return Zend\Code\Generator\PhpFile
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * getUses()
     * 
     * Returns an array with the first element the use statement, second is the as part.
     * If $withResolvedAs is set to true, there will be a third element that is the
     * "resolved" as statement, as the second part is not required in use statements
     *
     * @param bool $withResolvedAs
     * @return array
     */
    public function getUses($withResolvedAs = false)
    {
        $uses = $this->uses;
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
     * @param array $uses
     * @return Zend\Code\Generator\PhpFile
     */
    public function setUses(array $uses)
    {
        foreach ($uses as $use) {
            $this->setUse($use[0], $use[1]);
        }
        return $this;
    }

    /**
     * setUse()
     *
     * @param string $use
     * @param string $as
     * @return Zend\Code\Generator\PhpFile
     */
    public function setUse($use, $as = null)
    {
        $this->uses[] = array($use, $as);
        return $this;
    }

    /**
     * getClass()
     *
     * @param string $name
     * @return ClassGenerator
     */
    public function getClass($name = null)
    {
        if ($name == null) {
            reset($this->classes);
            return current($this->classes);
        }

        return $this->classes[$name];
    }

    /**
     * setClass()
     *
     * @param array|ClassGenerator $class
     * @return FileGenerator
     */
    public function setClass(ClassGenerator $class)
    {
        /*if (is_array($class)) {
            $class = new ClassGenerator($class);
            $className = $class->getName();
        } elseif ($class instanceof ClassGenerator) {
            $className = $class->getName();
        } else {
            throw new Exception\InvalidArgumentException('Expecting either an array or an instance of Zend_CodeGenerator_Php_Class');
        }*/

        // @todo check for dup here
        $className = $class->getName();
        $this->classes[$className] = $class;
        return $this;
    }

    /**
     * setFilename()
     *
     * @param string $filename
     * @return \FileGenerator\Code\Generator\PhpFile
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * getFilename()
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * getClasses()
     *
     * @return ClassGenerator[] Array of ClassGenerators
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * setBody()
     *
     * @param string $body
     * @return \FileGenerator\Code\Generator\PhpFile
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * getBody()
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
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

        foreach ($this->classes as $class) {
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
            return $this->sourceContent;
        }

        $output = '';

        // start with the body (if there), or open tag
        $body = $this->getBody();
        if (preg_match('#(?:\s*)<\?php#', $body) == false) {
            $output = '<?php' . self::LINE_FEED;
        }

        // if there are markers, put the body into the output
        if (preg_match('#/\* Zend_CodeGenerator_Php_File-(.*?)Marker:#', $body)) {
            $output .= $body;
            $body    = '';
        }

        // Add file docblock, if any
        if (null !== ($docblock = $this->getDocblock())) {
            $docblock->setIndentation('');

            if (preg_match('#/* Zend_CodeGenerator_Php_File-DocblockMarker */#', $output)) {
                $output = preg_replace('#/* Zend_CodeGenerator_Php_File-DocblockMarker */#', $docblock->generate(), $output, 1);
            } else {
                $output .= $docblock->generate() . self::LINE_FEED;
            }
        }

        // newline
        $output .= self::LINE_FEED;

        // namespace, if any
        if ($namespace = $this->getNamespace()) {
            $output .= "/** @namespace */" . self::LINE_FEED;
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
                $regex = str_replace('?', $class->getName(), '/* Zend_CodeGenerator_Php_File-ClassMarker: {?} */');
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
        if ($this->filename == '' || !is_writable(dirname($this->filename))) {
            throw new Exception\RuntimeException('This code generator object is not writable.');
        }
        file_put_contents($this->filename, $this->generate());
        return $this;
    }

}
