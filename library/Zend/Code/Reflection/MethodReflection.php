<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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

        if (!$this->annotations) {
            $cachingFileScanner = new CachingFileScanner($this->getFileName());
            $nameInformation    = $cachingFileScanner->getClassNameInformation($this->getDeclaringClass()->getName());

            $this->annotations = new AnnotationScanner($annotationManager, $docComment, $nameInformation);
        }

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
     * @param  bool        $includeDocBlock
     * @return string|bool
     */
    public function getContents($includeDocBlock = true)
    {
        $fileName     = $this->getFileName();
        if (false === $fileName) {
            throw new Exception\InvalidArgumentException(
                'Cannot determine internals methods contents'
            );
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
        $fileName = $this->getFileName();
        if (false === $fileName) {
            throw new Exception\InvalidArgumentException(
                'Cannot determine internals functions body'
            );
        }

        $lines = array_slice(
            file($fileName, FILE_IGNORE_NEW_LINES),
            $this->getStartLine() - 1,
            ($this->getEndLine() - ($this->getStartLine() - 1)),
            true
        );

        $functionLine = implode("\n", $lines);
        preg_match('#[(public|protected|private|abstract|final|static)\s*]+function\s+' . $this->getName() . '\s*\([^\)]*\)\s*{([^{}]+({[^}]+})*[^}]+)}#s', $functionLine, $matches);

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
}
