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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Feed/Reader/Extension/FeedAbstract.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class My_FeedReader_Extension_JungleBooks_Feed extends Zend_Feed_Reader_Extension_FeedAbstract
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
