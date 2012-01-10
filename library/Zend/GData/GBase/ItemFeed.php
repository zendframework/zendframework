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
 * @package    Zend_Gdata
 * @subpackage GBase
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\GBase;

/**
 * Represents the Google Base Customer Items Feed
 *
 * @link http://code.google.com/apis/base/
 *
 * @uses       \Zend\GData\GBase\Feed
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GBase
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ItemFeed extends \Zend\GData\Feed
{
    /**
     * The classname for individual item feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend\GData\GBase\ItemEntry';
}
