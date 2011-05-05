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
 * @package    Zend_Feed_Reader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
* @namespace
*/
namespace Zend\Feed\Reader\Extension\WellFormedWeb;
use Zend\Feed\Reader\Extension;

/**
* @uses \Zend\Feed\Reader\Reader
* @uses \Zend\Feed\Reader\Extension\EntryAbstract
* @category Zend
* @package Zend_Feed_Reader
* @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
class Entry extends Extension\AbstractEntry
{
    /**
     * Get the entry comment Uri
     *
     * @return string|null
     */
    public function getCommentFeedLink()
    {
        $name = 'commentRss';
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $data = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/wfw:' . $name . ')');

        if (!$data) {
            $data = null;
        }

        $this->_data[$name] = $data;

        return $data;
    }

    /**
     * Register Slash namespaces
     *
     * @return void
     */
    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('wfw', 'http://wellformedweb.org/CommentAPI/');
    }
}
