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
namespace Zend\CodeGenerator\Php\PhpMember;
use Zend\CodeGenerator\Php,
    Zend\CodeGenerator\Php\Exception;

/**
 * @uses       \Zend\CodeGenerator\Php\AbstractPhp
 * @uses       \Zend\CodeGenerator\PhpDocblock
 * @uses       \Zend\CodeGenerator\Php\Exception
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractMember extends Php\AbstractPhp
{

    /**#@+
     * @param const string
     */
    const VISIBILITY_PUBLIC    = 'public';
    const VISIBILITY_PROTECTED = 'protected';
    const VISIBILITY_PRIVATE   = 'private';
    /**#@-*/

    /**
     * @var \Zend\CodeGenerator\PhpDocblock
     */
    protected $_docblock   = null;

    /**
     * @var bool
     */
    protected $_isAbstract = false;

    /**
     * @var bool
     */
    protected $_isFinal    = false;

    /**
     * @var bool
     */
    protected $_isStatic   = false;

    /**
     * @var const
     */
    protected $_visibility = self::VISIBILITY_PUBLIC;

    /**
     * @var string
     */
    protected $_name = null;

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
            $docblock = new Php\PhpDocblock($docblock);
        } elseif (!$docblock instanceof Php\PhpDocblock) {
            throw new Exception\InvalidArgumentException('setDocblock() is expecting either a string, array or an instance of Zend_CodeGenerator_Php_Docblock');
        }

        $this->_docblock = $docblock;
        return $this;
    }

    /**
     * getDocblock()
     *
     * @return \Zend\CodeGenerator\PhpDocblock
     */
    public function getDocblock()
    {
        return $this->_docblock;
    }

    /**
     * setAbstract()
     *
     * @param bool $isAbstract
     * @return \Zend\CodeGenerator\Php\PhpMember\AbstractMember
     */
    public function setAbstract($isAbstract)
    {
        $this->_isAbstract = ($isAbstract) ? true : false;
        return $this;
    }

    /**
     * isAbstract()
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->_isAbstract;
    }

    /**
     * setFinal()
     *
     * @param bool $isFinal
     * @return \Zend\CodeGenerator\Php\PhpMember\AbstractMember
     */
    public function setFinal($isFinal)
    {
        $this->_isFinal = ($isFinal) ? true : false;
        return $this;
    }

    /**
     * isFinal()
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->_isFinal;
    }

    /**
     * setStatic()
     *
     * @param bool $isStatic
     * @return \Zend\CodeGenerator\Php\PhpMember\AbstractMember
     */
    public function setStatic($isStatic)
    {
        $this->_isStatic = ($isStatic) ? true : false;
        return $this;
    }

    /**
     * isStatic()
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->_isStatic;
    }

    /**
     * setVisitibility()
     *
     * @param const $visibility
     * @return \Zend\CodeGenerator\Php\PhpMember\AbstractMember
     */
    public function setVisibility($visibility)
    {
        $this->_visibility = $visibility;
        return $this;
    }

    /**
     * getVisibility()
     *
     * @return const
     */
    public function getVisibility()
    {
        return $this->_visibility;
    }

    /**
     * setName()
     *
     * @param string $name
     * @return \Zend\CodeGenerator\Php\PhpMember\AbstractMember
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}
