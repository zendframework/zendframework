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
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface IteratorInterface extends \Iterator
{

    const CURRENT_AS_SELF     = 0;
    const CURRENT_AS_KEY      = 1;
    const CURRENT_AS_VALUE    = 2;
    const CURRENT_AS_METADATA = 3;

    /**
     * Get storage instance
     *
     * @return StorageInterface
     */
    public function getStorage();

    /**
     * Get iterator mode
     *
     * @return int Value of IteratorInterface::CURRENT_AS_*
     */
    public function getMode();

    /**
     * Set iterator mode
     *
     * @param int $mode Value of IteratorInterface::CURRENT_AS_*
     * @return IteratorInterface Fluent interface
     */
    public function setMode($mode);
}
