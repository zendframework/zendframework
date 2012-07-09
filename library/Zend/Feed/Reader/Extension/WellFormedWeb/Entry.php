<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Reader\Extension\WellFormedWeb;

use Zend\Feed\Reader\Extension;

/**
* @category Zend
* @package Zend_Feed_Reader
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
