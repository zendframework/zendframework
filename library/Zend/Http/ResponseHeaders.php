<?php

namespace Zend\Http;

class ResponseHeaders extends Headers
{
    protected static $headerClasses = array(
        'acceptranges'       => 'Zend\Http\Header\AcceptRanges',
        'age'                => 'Zend\Http\Header\Age',
        'authenticationinfo' => 'Zend\Http\Header\AuthenticationInfo',
        'allow'              => 'Zend\Http\Header\Allow',
        'cachecontrol'       => 'Zend\Http\Header\CacheControl',
        'connection'         => 'Zend\Http\Header\Connection',
        'contentdisposition' => 'Zend\Http\Header\ContentDisposition',
        'contentencoding'    => 'Zend\Http\Header\ContentEncoding',
        'contentlanguage'    => 'Zend\Http\Header\ContentLanguage',
        'contentlength'      => 'Zend\Http\Header\ContentLength',
        'contentlocation'    => 'Zend\Http\Header\ContentLocation',
        'contentmd5'         => 'Zend\Http\Header\ContentMD5',
        'contenttype'        => 'Zend\Http\Header\ContentType',
        'contentrange'       => 'Zend\Http\Header\ContentRange',
        'date'               => 'Zend\Http\Header\Date',
        'etag'               => 'Zend\Http\Header\Etag',
        'expires'            => 'Zend\Http\Header\Expires',
        'keepalive'          => 'Zend\Http\Header\KeepAlive',
        'lastmodified'       => 'Zend\Http\Header\LastModified',
        'location'           => 'Zend\Http\Header\Location',
        'pragma'             => 'Zend\Http\Header\Pragma',
        'proxyauthenticate'  => 'Zend\Http\Header\ProxyAuthenticate',
        'refresh'            => 'Zend\Http\Header\Refresh',
        'retryafter'         => 'Zend\Http\Header\RetryAfter',
        'server'             => 'Zend\Http\Header\Server',
        'trailer'            => 'Zend\Http\Header\Trailer',
        'transferencoding'   => 'Zend\Http\Header\TransferEncoding',
        'upgrade'            => 'Zend\Http\Header\Upgrade',
        'vary'               => 'Zend\Http\Header\Vary',
        'via'                => 'Zend\Http\Header\Via',
        'warning'            => 'Zend\Http\Header\Warning',
        'wwwauthenticate'    => 'Zend\Http\Header\WWWAuthenticate',
        'setcookie'          => 'Zend\Http\Header\SetCookie'
    );

}
