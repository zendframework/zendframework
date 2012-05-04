<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace Zend\Paginator\ScrollingStyle;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ScrollingStyleInterface
{
    /**
     * Returns an array of "local" pages given a page number and range.
     *
     * @param  \Zend\Paginator\Paginator $paginator
     * @param  integer $pageRange (Optional) Page range
     * @return array
     */
    public function getPages(\Zend\Paginator\Paginator $paginator, $pageRange = null);
}
