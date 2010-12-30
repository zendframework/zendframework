<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


use Zend\Service\LiveDocx\Helper;
use Zend\Service\LiveDocx\MailMerge;

Helper::printLine(
    PHP_EOL . 'Checking For Remotely Stored Images' .
    PHP_EOL .
    PHP_EOL
);

$mailMerge = new MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

print('Checking whether an image is available... ');
if (true === $mailMerge->imageExists('image-01.png')) {
    print('EXISTS. ');
} else {
    print('DOES NOT EXIST. ');
}
print('DONE' . PHP_EOL);

print(PHP_EOL);

unset($mailMerge);