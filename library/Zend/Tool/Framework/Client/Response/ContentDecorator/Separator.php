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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Client\Response\ContentDecorator;
use Zend\Tool\Framework\Client\Response\ContentDecorator;

/**
 * @uses       \Zend\Tool\Framework\Client\Response\ContentDecorator
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Separator implements ContentDecorator
{

    /**
     * @var string
     */
    protected $_separator = PHP_EOL;

    /**
     * getName() - name of the decorator
     *
     * @return string
     */
    public function getName()
    {
        return 'separator';
    }

    /**
     * setSeparator()
     *
     * @param string $separator
     * @return \Zend\Tool\Framework\Client\Response\ContentDecorator\Separator
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }

    /**
     * getSeparator()
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    public function decorate($content, $decoratorValue)
    {
        $run = 1;
        if (is_bool($decoratorValue) && $decoratorValue === false) {
            return $content;
        }

        if (is_int($decoratorValue)) {
            $run = $decoratorValue;
        }

        for ($i = 0; $i < $run; $i++) {
            $content .= $this->_separator;
        }

        return $content;
    }

}
