<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Feed\Reader;


interface ReaderImportInterface
{
    public static function import($uri, $etag = null, $lastModified = null);

    public static function importRemoteFeed($uri, Http\ClientInterface $client);

    public static function importString($string);

    public static function importFile($filename);


}