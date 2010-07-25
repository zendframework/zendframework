<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Service\LiveDocx\Helper;
use Zend\Service\LiveDocx\MailMerge;

Helper::printLine(
    PHP_EOL . 'Downloading Remotely Stored Templates' .
    PHP_EOL .
    PHP_EOL
);

$mailMerge = new MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

$counter = 1;
foreach ($mailMerge->listTemplates() as $result) {
    printf('%d) %s', $counter, $result['filename']);
    $template = $mailMerge->downloadTemplate($result['filename']);
    file_put_contents('downloaded-' . $result['filename'], $template);
    print(' - DOWNLOADED.' . PHP_EOL);
    $counter++;
}

print(PHP_EOL);

unset($mailMerge);
