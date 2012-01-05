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
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Reflection\DocBlock;

use Zend\Code\Reflection\Exception;

/**
 * @uses       \Zend\Code\Reflection\ReflectionDocblockTag
 * @uses       \Zend\Code\Reflection\Exception
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ParamTag implements Tag
{
    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $variableName = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * @return string
     */
    public function getName()
    {
        return 'param';
    }

    /**
     * Initializer
     *
     * @param string $tagDocblockLine
     */
    public function initialize($tagDocblockLine)
    {
        $matches = array();
        preg_match('#([\w|\\\]+)(?:\s+(\$\S+)){0,1}(?:\s+(.*))?#s', $tagDocblockLine, $matches);

        $this->type = $matches[1];

        if (isset($matches[2])) {
            $this->variableName = $matches[2];
        }

        if (isset($matches[3])) {
            $this->description = preg_replace('#\s+#', ' ', $matches[3]);
        }
    }

    /**
     * Get parameter variable type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get parameter name
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
