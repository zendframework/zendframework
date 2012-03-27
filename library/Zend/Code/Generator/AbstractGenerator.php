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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Generator;
use Zend\Code\Generator
    /* Zend\Config */
    ;

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractGenerator implements Generator
{

    /**
     * Line feed to use in place of EOL
     *
     */
    const LINE_FEED = "\n";

    /**
     * @var bool
     */
    protected $isSourceDirty = true;

    /**
     * @var int|string 4 spaces by default
     */
    protected $indentation = '    ';

    /**
     * @var string
     */
    protected $sourceContent = null;

    /**
     * setSourceDirty()
     *
     * @param bool $isSourceDirty
     * @return \Zend\Code\Generator\AbstractPhp
     */
    public function setSourceDirty($isSourceDirty = true)
    {
        $this->isSourceDirty = ($isSourceDirty) ? true : false;
        return $this;
    }

    /**
     * isSourceDirty()
     *
     * @return bool
     */
    public function isSourceDirty()
    {
        return $this->isSourceDirty;
    }

    /**
     * setIndentation()
     *
     * @param string|int $indentation
     * @return \Zend\Code\Generator\AbstractPhp
     */
    public function setIndentation($indentation)
    {
        $this->indentation = $indentation;
        return $this;
    }

    /**
     * getIndentation()
     *
     * @return string|int
     */
    public function getIndentation()
    {
        return $this->indentation;
    }

    /**
     * setSourceContent()
     *
     * @param string $sourceContent
     */
    public function setSourceContent($sourceContent)
    {
        $this->sourceContent = $sourceContent;
        return;
    }

    /**
     * getSourceContent()
     *
     * @return string
     */
    public function getSourceContent()
    {
        return $this->sourceContent;
    }

//    /**
//     * __construct()
//     *
//     * @param array $options
//     */
//    public function __construct($options = array())
//    {
//        $this->_init();
//        if ($options != null) {
//            // use Zend_Config objects if provided
//            if ($options instanceof Config\Config) {
//                $options = $options->toArray();
//            }
//            // pass arrays to setOptions
//            if (is_array($options)) {
//                $this->setOptions($options);
//            }
//        }
//        $this->_prepare();
//    }
//
//    /**
//     * setConfig()
//     *
//     * @param \Zend\Config\Config $config
//     * @return \Zend\CodeGenerator\AbstractCodeGenerator
//     */
//    public function setConfig(Config\Config $config)
//    {
//        $this->setOptions($config->toArray());
//        return $this;
//    }
//
//    /**
//     * setOptions()
//     *
//     * @param array $options
//     * @return \Zend\CodeGenerator\AbstractCodeGenerator
//     */
//    public function setOptions(Array $options)
//    {
//        foreach ($options as $optionName => $optionValue) {
//            $methodName = 'set' . $optionName;
//            if (method_exists($this, $methodName)) {
//                $this->{$methodName}($optionValue);
//            }
//        }
//        return $this;
//    }

}
