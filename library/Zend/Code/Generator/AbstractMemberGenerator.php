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
     * @const int Flags for construction usage
     */
    const FLAG_ABSTRACT  = 0x01;
    const FLAG_FINAL     = 0x02;
    const FLAG_STATIC    = 0x04;
    const FLAG_PUBLIC    = 0x10;
    const FLAG_PROTECTED = 0x20;
    const FLAG_PRIVATE   = 0x40;
    /**#@-*/

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
    protected $docblock = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var int
     */
    protected $flags = self::FLAG_PUBLIC;

    public function setFlags($flags)
    {

        if (is_array($flags)) {
            $flagsArray = $flags;
            $flags = 0x00;
            foreach ($flagsArray as $flag) {
                $flags |= $flag;
            }
        }
        // check that visibility is one of three
        $this->flags = $flags;
        return $this;
    }

    public function addFlag($flag)
    {
        $this->setFlags($this->flags | $flag);
        return $this;
    }

    public function removeFlag($flag)
    {
        $this->setFlags($this->flags & ~$flag);
        return $this;
    }


    /**
     * setDocblock() Set the docblock
     *
     * @param DocblockGenerator|string $docblock
     * @return AbstractMemberGenerator
     */
    public function setDocblock($docblock)
    {
        if (is_string($docblock)) {
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
        return (($isAbstract) ? $this->addFlag(self::FLAG_ABSTRACT) : $this->removeFlag(self::FLAG_ABSTRACT));
    }

    /**
     * isAbstract()
     *
     * @return bool
     */
    public function isAbstract()
    {
        return ($this->flags & self::FLAG_ABSTRACT);
    }

    /**
     * setFinal()
     *
     * @param bool $isFinal
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setFinal($isFinal)
    {
        return (($isFinal) ? $this->addFlag(self::FLAG_FINAL) : $this->removeFlag(self::FLAG_FINAL));
    }

    /**
     * isFinal()
     *
     * @return bool
     */
    public function isFinal()
    {
        return ($this->flags & self::FLAG_FINAL);
    }

    /**
     * setStatic()
     *
     * @param bool $isStatic
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setStatic($isStatic)
    {
        return (($isStatic) ? $this->addFlag(self::FLAG_STATIC) : $this->removeFlag(self::FLAG_STATIC));
    }

    /**
     * isStatic()
     *
     * @return bool
     */
    public function isStatic()
    {
        return ($this->flags & self::FLAG_STATIC); // is FLAG_STATIC in flags
    }

    /**
     * setVisibility()
     *
     * @param string $visibility
     * @return AbstractMemberGenerator
     */
    public function setVisibility($visibility)
    {
        switch ($visibility) {
            case self::VISIBILITY_PUBLIC:
                $this->removeFlag(self::FLAG_PRIVATE | self::FLAG_PROTECTED); // remove both
                $this->addFlag(self::FLAG_PUBLIC);
                break;
            case self::VISIBILITY_PROTECTED:
                $this->removeFlag(self::FLAG_PUBLIC | self::FLAG_PRIVATE); // remove both
                $this->addFlag(self::FLAG_PROTECTED);
                break;
            case self::VISIBILITY_PRIVATE:
                $this->removeFlag(self::FLAG_PUBLIC | self::FLAG_PROTECTED); // remove both
                $this->addFlag(self::FLAG_PRIVATE);
                break;
        }
        return $this;
    }

    /**
     * getVisibility()
     *
     * @return const
     */
    public function getVisibility()
    {
        switch (true) {
            case ($this->flags & self::FLAG_PROTECTED):
                return self::VISIBILITY_PROTECTED;
            case ($this->flags & self::FLAG_PRIVATE):
                return self::VISIBILITY_PRIVATE;
            default:
                return self::VISIBILITY_PUBLIC;
        }
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
