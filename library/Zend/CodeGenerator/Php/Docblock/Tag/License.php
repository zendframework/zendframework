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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\CodeGenerator\Php\Docblock\Tag;

/**
 * @uses       \Zend\CodeGenerator\Php\PhpDocblockTag
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class License extends \Zend\CodeGenerator\Php\PhpDocblockTag
{

    /**
     * @var string
     */
    protected $_url = null;

    /**
     * @var string
     */
    protected $_description = null;

    /**
     * fromReflection()
     *
     * @param \Zend\Reflection\ReflectionDocblockTag $reflectionTagReturn
     * @return \Zend\CodeGenerator\Php\Docblock\Tag\License
     */
    public static function fromReflection(\Zend\Reflection\ReflectionDocblockTag $reflectionTagLicense)
    {
        $returnTag = new \self();

        $returnTag->setName('license');
        $returnTag->setUrl($reflectionTagLicense->getUrl());
        $returnTag->setDescription($reflectionTagLicense->getDescription());

        return $returnTag;
    }

    /**
     * setUrl()
     *
     * @param string $url
     * @return \Zend\CodeGenerator\Php\Docblock\Tag\License
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * getUrl()
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }


    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        $output = '@license ' . $this->_url . ' ' . $this->_description . self::LINE_FEED;
        return $output;
    }

}
