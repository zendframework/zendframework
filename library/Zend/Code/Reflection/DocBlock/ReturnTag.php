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
 * @uses       \Zend\Code\Reflection\Exception
 * @uses       \Zend\Code\Reflection\ReflectionDocblockTag
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReturnTag implements Tag
{
    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * @return string
     */
    public function getName()
    {
        return 'return';
    }

    /**
     * Constructor
     *
     * @param  string $tagDocblockLine
     * @return void
     */
    public function initialize($tagDocblockLine)
    {
        $matches = array();
        preg_match('#([\w|\\\]+)(?:\s+(.*))?#', $tagDocblockLine, $matches);

        $this->type = $matches[1];

        if (isset($matches[2])) {
            $this->description = $matches[2];
        }
    }

    /**
     * Get return variable type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
