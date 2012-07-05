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
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

use Zend\Loader\StandardAutoloader;
use Zend\Feed\Reader\Reader;

/**
 * Consume an RSS feed and display all of the titles and
 * associated links within.
 */

require_once dirname(dirname(dirname(__DIR__))).'/library/Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader;
$loader->register();


$rss = Reader::import('http://news.google.com/?output=rss');

foreach ($rss as $item) {

    echo "<p>" . $item->getTitle() . "<br />", "\n";
    echo $item->getLink()  . "</p>";
}
