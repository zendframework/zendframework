<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

use Zend\Loader\StandardAutoloader;
use Zend\Service\Flickr\Exception\ExceptionInterface as FlickrException;
use Zend\Service\Flickr\Flickr;

/**
 * Query Flickr for a tag and display all of the photos for
 * that tag.
 */

error_reporting(E_ALL);

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/library/Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

$flickr = new Flickr('your api key here');

$photos = $flickr->tagSearch('php');

foreach ($photos as $photo) {
    echo '<img src="' . $photo->Thumbnail->uri . '" /> <br />';
    echo $photo->title . "<br /> \n";
}
