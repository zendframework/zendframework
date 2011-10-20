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
namespace Zend\Code\Generator\Docblock\Tag;

/**
 * @uses       \Zend\Code\Generator\Docblock\Tag
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReturnTag extends \Zend\Code\Generator\Docblock\Tag
{

    /**
     * @var string
     */
    protected $_datatype = null;

    /**
     * fromReflection()
     *
     * @param \Zend\Code\Reflection\ReflectionDocblockTag $reflectionTagReturn
     * @return \Zend\Code\Generator\Docblock\Tag\ReturnTag
     */
    public static function fromReflection(\Zend\Code\Reflection\ReflectionDocblockTag $reflectionTagReturn)
    {
        $returnTag = new self();

        $returnTag->setName('return');
        $returnTag->setDatatype($reflectionTagReturn->getType()); // @todo rename
        $returnTag->setDescription($reflectionTagReturn->getDescription());

        return $returnTag;
    }

    /**
     * setDatatype()
     *
     * @param string $datatype
     * @return \Zend\Code\Generator\Docblock\Tag\ReturnTag
     */
    public function setDatatype($datatype)
    {
        $this->_datatype = $datatype;
        return $this;
    }

    /**
     * getDatatype()
     *
     * @return string
     */
    public function getDatatype()
    {
        return $this->_datatype;
    }


    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        $output = '@return ' . $this->_datatype . ' ' . $this->description;
        return $output;
    }

}
