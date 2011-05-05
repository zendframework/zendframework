<?php
namespace ZendTest\Log\TestAsset;

use Zend\Controller\Request\Http as HttpRequest;

class FirePhpHeader extends HttpRequest
{
    public function getHeader($header)
    {
        if ($header == 'User-Agent') {
            return 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14 FirePHP/0.1.0';
        }
    }
}