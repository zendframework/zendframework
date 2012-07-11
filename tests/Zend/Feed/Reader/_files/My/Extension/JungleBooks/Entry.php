<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace My\Extension\JungleBooks;

use Zend\Feed\Reader\Extension;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
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
