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
class Feed extends Extension\AbstractFeed
{

    public function getDaysPopularBookLink()
    {
        if (isset($this->_data['dayPopular'])) {
            return $this->_data['dayPopular'];
        }
        $dayPopular = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/jungle:dayPopular)');
        if (!$dayPopular) {
            $dayPopular = null;
        }
        $this->_data['dayPopular'] = $dayPopular;
        return $this->_data['dayPopular'];
    }

    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('jungle', 'http://example.com/junglebooks/rss/module/1.0/');
    }
}
