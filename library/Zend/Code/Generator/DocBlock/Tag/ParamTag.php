<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Generator\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionTagInterface;

class ParamTag extends AbstractTypeableTag implements TagInterface
{
    /**
     * @var string
     */
    protected $variableName = null;

    /**
     * @param string $variableName
     * @param array $types
     * @param string $description
     */
    public function __construct($variableName = null, $types = array(), $description = null)
    {
        if (!empty($variableName)) {
            $this->setVariableName($variableName);
        }

        parent::__construct($types, $description);
    }

    /**
     * @param  ReflectionTagInterface $reflectionTagReturn
     * @return ReturnTag
     * @deprecated Use TagManager::createTag() instead
     */
    public static function fromReflection(ReflectionTagInterface $reflectionTagReturn)
    {
        // @todo TagManager
    }

    /**
     * @return string
     */
    public function getName()
    {
       return 'param';
    }

    /**
     * @param string $variableName
     * @return ParamTag
     */
    public function setVariableName($variableName)
    {
        $this->variableName = $variableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * @param string $datatype
     * @return ReturnTag
     * @deprecated Use setTypes() instead
     */
    public function setDatatype($datatype)
    {
        return $this->setTypes($datatype);
    }

    /**
     * @return string
     * @deprecated Use getTypes() instead
     */
    public function getDatatype()
    {
        return implode('|', $this->getTypes());
    }

    /**
     * @param  string $paramName
     * @return ParamTag
     * @deprecated Use setVariableName() instead
     */
    public function setParamName($paramName)
    {
        return $this->setVariableName($paramName);
    }

    /**
     * @return string
     * @deprecated Use getVariableName() instead
     */
    public function getParamName()
    {
        return $this->getVariableName();
    }

    /**
     * @return string
     */
    public function generate()
    {
        $output = '@param'
            . ((!empty($this->types)) ? ' ' . implode('|', $this->types) : 'unknown')
            . ((!empty($this->variableName)) ? ' $' . $this->variableName : '')
            . ((!empty($this->description)) ? ' ' . $this->description : '');

        return $output;
    }
}
