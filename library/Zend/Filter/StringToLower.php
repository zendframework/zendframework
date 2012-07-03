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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StringToLower extends AbstractUnicode
{
    /**
     * @var array
     */
    protected $options = array(
        'encoding' => null,
    );

    /**
     * Constructor
     *
     * @param string|array|Traversable $options OPTIONAL
     */
    public function __construct($encodingOrOptions = null)
    {
        if ($encodingOrOptions !== null) {
            if (!static::isOptions($encodingOrOptions)){
                $this->setEncoding($encodingOrOptions);
            } else {
                $this->setOptions($encodingOrOptions);
            }
        }
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value, converting characters to lowercase as necessary
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if ($this->options['encoding'] !== null) {
            return mb_strtolower((string) $value,  $this->options['encoding']);
        }

        return strtolower((string) $value);
    }
}
