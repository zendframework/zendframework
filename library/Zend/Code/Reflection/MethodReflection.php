<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Reflection;

use ReflectionMethod as PhpReflectionMethod;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Scanner\AnnotationScanner;
use Zend\Code\Scanner\CachingFileScanner;

class MethodReflection extends PhpReflectionMethod implements ReflectionInterface
{
    /**
     * Constant use in @MethodReflection to display prototype as an array
     */
    const PROTOTYPE_AS_ARRAY = 'prototype_as_array';

    /**
     * Constant use in @MethodReflection to display prototype as a string
     */
    const PROTOTYPE_AS_STRING = 'prototype_as_string';

    /**
     * @var AnnotationScanner
     */
    protected $annotations = null;

    /**
     * Retrieve method DocBlock reflection
     *
     * @return DocBlockReflection|false
     */
    public function getDocBlock()
    {
        if ('' == $this->getDocComment()) {
            return false;
        }

        $instance = new DocBlockReflection($this);

        return $instance;
    }

    /**
     * @param  AnnotationManager $annotationManager
     * @return AnnotationScanner
     */
    public function getAnnotations(AnnotationManager $annotationManager)
    {
        if (($docComment = $this->getDocComment()) == '') {
            return false;
        }

        if ($this->annotations) {
            return $this->annotations;
        }

        $cachingFileScanner = $this->createFileScanner($this->getFileName());
        $nameInformation    = $cachingFileScanner->getClassNameInformation($this->getDeclaringClass()->getName());

        if (!$nameInformation) {
            return false;
        }

        $this->annotations = new AnnotationScanner($annotationManager, $docComment, $nameInformation);

        return $this->annotations;
    }

    /**
     * Get start line (position) of method
     *
     * @param  bool $includeDocComment
     * @return int
     */
    public function getStartLine($includeDocComment = false)
    {
        if ($includeDocComment) {
            if ($this->getDocComment() != '') {
                return $this->getDocBlock()->getStartLine();
            }
        }

        return parent::getStartLine();
    }

    /**
     * Get reflection of declaring class
     *
     * @return ClassReflection
     */
    public function getDeclaringClass()
    {
        $phpReflection  = parent::getDeclaringClass();
        $zendReflection = new ClassReflection($phpReflection->getName());
        unset($phpReflection);

        return $zendReflection;
    }

    /**
     * Get method prototype
     *
     * @return array
     */
    public function getPrototype($format = MethodReflection::PROTOTYPE_AS_ARRAY)
    {
        $returnType = 'mixed';
        $docBlock = $this->getDocBlock();
        if ($docBlock) {
            $return = $docBlock->getTag('return');
            $returnTypes = $return->getTypes();
            $returnType = count($returnTypes) > 1 ? implode('|', $returnTypes) : $returnTypes[0];
        }

        $declaringClass = $this->getDeclaringClass();
        $prototype = array(
            'namespace'  => $declaringClass->getNamespaceName(),
            'class'      => substr($declaringClass->getName(), strlen($declaringClass->getNamespaceName()) + 1),
            'name'       => $this->getName(),
            'visibility' => ($this->isPublic() ? 'public' : ($this->isPrivate() ? 'private' : 'protected')),
            'return'     => $returnType,
            'arguments'  => array(),
        );

        $parameters = $this->getParameters();
        foreach ($parameters as $parameter) {
            $prototype['arguments'][$parameter->getName()] = array(
                'type'     => $parameter->getType(),
                'required' => !$parameter->isOptional(),
                'by_ref'   => $parameter->isPassedByReference(),
                'default'  => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
            );
        }

        if ($format == MethodReflection::PROTOTYPE_AS_STRING) {
            $line = $prototype['visibility'] . ' ' . $prototype['return'] . ' ' . $prototype['name'] . '(';
            $args = array();
            foreach ($prototype['arguments'] as $name => $argument) {
                $argsLine = ($argument['type'] ? $argument['type'] . ' ' : '') . ($argument['by_ref'] ? '&' : '') . '$' . $name;
                if (!$argument['required']) {
                    $argsLine .= ' = ' . var_export($argument['default'], true);
                }
                $args[] = $argsLine;
            }
            $line .= implode(', ', $args);
            $line .= ')';

            return $line;
        }

        return $prototype;
    }

    /**
     * Get all method parameter reflection objects
     *
     * @return ParameterReflection[]
     */
    public function getParameters()
    {
        $phpReflections  = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance = new ParameterReflection(array(
                $this->getDeclaringClass()->getName(),
                $this->getName()),
                $phpReflection->getName()
            );
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);

        return $zendReflections;
    }

    /**
     * Get method contents
     *
     * @param  bool $includeDocBlock
     * @return string|bool
     */
    public function getContents($includeDocBlock = true)
    {
        $fileName = $this->getFileName();

        if ((class_exists($this->class) && !$fileName) || ! file_exists($fileName)) {
            return ''; // probably from eval'd code, return empty
        }

        $lines = array_slice(
            file($fileName, FILE_IGNORE_NEW_LINES),
            $this->getStartLine() - 1,
            ($this->getEndLine() - ($this->getStartLine() - 1)),
            true
        );

        $functionLine = implode("\n", $lines);
        $name         = preg_quote($this->getName());
        preg_match('#[(public|protected|private|abstract|final|static)\s*]*function\s+' . $name . '\s*\([^\)]*\)\s*{([^{}]+({[^}]+})*[^}]+)?}#s', $functionLine, $matches);

        if (!isset($matches[0])) {
            return false;
        }

        $content    = $matches[0];
        $docComment = $this->getDocComment();

        return $includeDocBlock && $docComment ? $docComment . "\n" . $content : $content;
    }

    /**
     * Get method body
     *
     * @return string|bool
     */
    public function getBody()
    {
        $fileName = $this->getDeclaringClass()->getFileName();

        if (false === $fileName || ! file_exists($fileName)) {
            return '';
        }

        $lines = array_slice(
            file($fileName, FILE_IGNORE_NEW_LINES),
            $this->getStartLine() - 1,
            ($this->getEndLine() - ($this->getStartLine() - 1)),
            true
        );

        $functionLine = implode("\n", $lines);
        $name = preg_quote($this->getName());
        preg_match('#[(public|protected|private|abstract|final|static)\s*]*function\s+' . $name . '\s*\([^\)]*\)\s*{([^{}]+({[^}]+})*[^}]+)}#s', $functionLine, $matches);

        if (!isset($matches[1])) {
            return false;
        }

        $body = $matches[1];

        return $body;
    }

    public function toString()
    {
        return parent::__toString();
    }

    public function __toString()
    {
        return parent::__toString();
    }

    /**
     * Creates a new FileScanner instance.
     *
     * By having this as a seperate method it allows the method to be overridden
     * if a different FileScanner is needed.
     *
     * @param  string $filename
     *
     * @return FileScanner
     */
    protected function createFileScanner($filename)
    {
        return new CachingFileScanner($filename);
    }
}
