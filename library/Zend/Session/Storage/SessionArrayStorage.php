<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Session\Storage;

use ArrayIterator;
use IteratorAggregate;
use Zend\Session\Exception;

if (version_compare(PHP_VERSION, '5.3.3') > 0) {
    /**
     * Session storage in $_SESSION
     */
    class SessionArrayStorage extends AbstractSessionArrayStorage
    {
        /**
         * Get Offset
         *
         * @param  mixed $key
         * @return mixed
         */
        public function &__get($key)
        {
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            }

            return null;
        }

        /**
         * Offset Get
         *
         * @param  mixed $key
         * @return mixed
         */
        public function &offsetGet($key)
        {
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            }

            return null;
        }
    }
} else {
    class SessionArrayStorage extends AbstractSessionArrayStorage
    {

    }
}
