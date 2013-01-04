<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

use Zend\Loader\StandardAutoloader;
use Zend\Feed\Reader\Reader;

/**
 * Consume an RSS feed and display all of the titles and
 * associated links within.
 */

require_once dirname(dirname(dirname(__DIR__))).'/library/Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();


$rss = Reader::import('http://news.google.com/?output=rss');

foreach ($rss as $item) {
    echo '<p>' . $item->getTitle() . '<br />', "\n";
    echo $item->getLink()  . '</p>';
}
