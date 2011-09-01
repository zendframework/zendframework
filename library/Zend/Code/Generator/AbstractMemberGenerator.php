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
use Zend\Code\Generator,
    Zend\Code\Generator\Exception;

/**
 * @uses       \Zend\Code\Generator\AbstractPhp
 * @uses       \Zend\Code\GeneratorDocblock
 * @uses       \Zend\Code\Generator\Exception
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractMemberGenerator extends AbstractGenerator
{

    /**#@+
     * @param const string
     */
    const VISIBILITY_PUBLIC    = 'public';
    const VISIBILITY_PROTECTED = 'protected';
    const VISIBILITY_PRIVATE   = 'private';
    /**#@-*/

    /**
     * @var \Zend\Code\GeneratorDocblock
     */
    protected $docblock   = null;

    /**
     * @var bool
     */
    protected $isAbstract = false;

    /**
     * @var bool
     */
    protected $isFinal    = false;

    /**
     * @var bool
     */
    protected $isStatic   = false;

    /**
     * @var const
     */
    protected $visibility = self::VISIBILITY_PUBLIC;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * setDocblock() Set the docblock
     *
     * @param DocblockGenerator|array|string $docblock
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
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
     * getDocblock()
     *
     * @return DocblockGenerator
     */
    public function getDocblock()
    {
        return $this->docblock;
    }

    /**
     * setAbstract()
     *
     * @param bool $isAbstract
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setAbstract($isAbstract)
    {
        $this->isAbstract = ($isAbstract) ? true : false;
        return $this;
    }

    /**
     * isAbstract()
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->isAbstract;
    }

    /**
     * setFinal()
     *
     * @param bool $isFinal
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setFinal($isFinal)
    {
        $this->isFinal = ($isFinal) ? true : false;
        return $this;
    }

    /**
     * isFinal()
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->isFinal;
    }

    /**
     * setStatic()
     *
     * @param bool $isStatic
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setStatic($isStatic)
    {
        $this->isStatic = ($isStatic) ? true : false;
        return $this;
    }

    /**
     * isStatic()
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->isStatic;
    }

    /**
     * setVisibility()
     *
     * @param const $visibility
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * getVisibility()
     *
     * @return const
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * setName()
     *
     * @param string $name
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
