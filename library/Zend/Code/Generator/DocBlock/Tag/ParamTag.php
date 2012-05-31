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
 * @package    Zend_Code_Generator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\DocBlock\Tag;
use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionDocBlockTag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ParamTag extends Tag
{

    /**
     * @var string
     */
    protected $datatype = null;

    /**
     * @var string
     */
    protected $paramName = null;

    /**
     * fromReflection()
     *
     * @param ReflectionDocBlockTag $reflectionTagParam
     * @return ParamTag
     */
    public static function fromReflection(ReflectionDocBlockTag $reflectionTagParam)
    {
        $paramTag = new self();

        $paramTag->setName('param');
        $paramTag->setDatatype($reflectionTagParam->getType()); // @todo rename
        $paramTag->setParamName($reflectionTagParam->getVariableName());
        $paramTag->setDescription($reflectionTagParam->getDescription());

        return $paramTag;
    }

    /**
     * setDatatype()
     *
     * @param string $datatype
     * @return ParamTag
     */
    public function setDatatype($datatype)
    {
        $this->datatype = $datatype;
        return $this;
    }

    /**
     * getDatatype
     *
     * @return string
     */
    public function getDatatype()
    {
        return $this->datatype;
    }

    /**
     * setParamName()
     *
     * @param string $paramName
     * @return ParamTag
     */
    public function setParamName($paramName)
    {
        $this->paramName = $paramName;
        return $this;
    }

    /**
     * getParamName()
     *
     * @return string
     */
    public function getParamName()
    {
        return $this->paramName;
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        $output = '@param '
            . (($this->datatype != null) ? $this->datatype : 'unknown')
            . (($this->paramName != null) ? ' $' . $this->paramName : '')
            . (($this->description != null) ? ' ' . $this->description : '');
        return $output;
    }

}
