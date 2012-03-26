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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace My\Extension\JungleBooks;
use Zend\Feed\Reader\Extension;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Entry extends Extension\AbstractEntry
{

    public function getIsbn()
    {
        if (isset($this->_data['isbn'])) {
            return $this->_data['isbn'];
        }
        $isbn = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/jungle:isbn)');
        if (!$isbn) {
            $isbn = null;
        }
        $this->_data['isbn'] = $title;
        return $this->_data['isbn'];
    }

    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('jungle', 'http://example.com/junglebooks/rss/module/1.0/');
    }
}
