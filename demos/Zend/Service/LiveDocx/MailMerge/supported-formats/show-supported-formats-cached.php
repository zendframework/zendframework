<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

use Zend\Cache\StorageFactory;
use Zend\Service\LiveDocx\Helper;
use Zend\Service\LiveDocx\MailMerge;

Helper::printLine(
    PHP_EOL . 'Template, Document and Image Formats' .
    PHP_EOL .
    PHP_EOL . 'The following formats are supported by LiveDocx:' .
    PHP_EOL .
    PHP_EOL . '(Note these method calls are cached for maximum performance. The supported formats change very infrequently, hence, they are good candidates to be cached.)' .
    PHP_EOL .
    PHP_EOL
);

$cacheId = md5(__FILE__);

$cache = array(
    'adapter' => 'FileSystem',
    'options' => array(
        'cache_dir' => __DIR__ . '/cache',
    ),
);

if (!is_dir($cache['options']['cache_dir'])) {
    mkdir($cache['options']['cache_dir']);
}

$cache = StorageFactory::factory($cache);

if (! $formats = $cache->getItem($cacheId)) {

    // Cache miss. Connect to backend service (expensive).

    $mailMerge = new MailMerge();

    $mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
              ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

    $formats = new \StdClass();

    $formats->template = $mailMerge->getTemplateFormats();
    $formats->document = $mailMerge->getDocumentFormats();
    $formats->image    = $mailMerge->getImageExportFormats();

    $cache->setItem($formats, $cacheId);

    unset($mailMerge);

} else {

    // Cache hit. Continue.

}

unset($cache);

printf("Supported TEMPLATE file formats (input)  : %s%s",
    Helper::arrayDecorator($formats->template), PHP_EOL);

printf("Supported DOCUMENT file formats (output) : %s%s",
    Helper::arrayDecorator($formats->document), PHP_EOL);

printf("Supported IMAGE file formats (output)    : %s%s",
    Helper::arrayDecorator($formats->image), PHP_EOL);

print(PHP_EOL);
