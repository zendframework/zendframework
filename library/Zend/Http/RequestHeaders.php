<?php

namespace Zend\Http;

class RequestHeaders extends Headers
{
    protected static $headerClasses = array(
        'allow'              => 'Zend\Http\Header\Allow',
        'cachecontrol'       => 'Zend\Http\Header\CacheControl',
        'connection'         => 'Zend\Http\Header\Connection',
        'contentencoding'    => 'Zend\Http\Header\ContentEncoding',
        'contentlanguage'    => 'Zend\Http\Header\ContentLanguage',
        'contentlength'      => 'Zend\Http\Header\ContentLength',
        'contentlocation'    => 'Zend\Http\Header\ContentLocation',
        'contentmd5'         => 'Zend\Http\Header\ContentMD5',
        'contenttype'        => 'Zend\Http\Header\ContentType',
        'contentrange'       => 'Zend\Http\Header\ContentRange',
        'date'               => 'Zend\Http\Header\Date',
        'expires'            => 'Zend\Http\Header\Expires',
        'keepalive'          => 'Zend\Http\Header\KeepAlive',
        'lastmodified'       => 'Zend\Http\Header\LastModified',
        'trailer'            => 'Zend\Http\Header\Trailer',
        'transferencoding'   => 'Zend\Http\Header\TransferEncoding',
        'pragma'             => 'Zend\Http\Header\Pragma',
        'upgrade'            => 'Zend\Http\Header\Upgrade',
        'via'                => 'Zend\Http\Header\Via',
        'warning'            => 'Zend\Http\Header\Warning',
        'wwwauthenticate'    => 'Zend\Http\Header\WWWAuthenticate',
        'acceptcharset'      => 'Zend\Http\Header\AcceptCharset',
        'acceptencoding'     => 'Zend\Http\Header\AcceptEncoding',
        'accept'             => 'Zend\Http\Header\Accept',
        'acceptlanguage'     => 'Zend\Http\Header\AcceptLanguage',
        'authorization'      => 'Zend\Http\Header\Authorization',
        'expect'             => 'Zend\Http\Header\Expect',
        'from'               => 'Zend\Http\Header\From',
        'host'               => 'Zend\Http\Header\Host',
        'ifmatch'            => 'Zend\Http\Header\IfMatch',
        'ifmodifiedsince'    => 'Zend\Http\Header\IfModifiedSince',
        'ifnonematch'        => 'Zend\Http\Header\IfNoneMatch',
        'ifrange'            => 'Zend\Http\Header\IfRange',
        'ifunmodifiedsince'  => 'Zend\Http\Header\IfUnmodifiedSince',
        'maxforwards'        => 'Zend\Http\Header\MaxForwards',
        'proxyauthorization' => 'Zend\Http\Header\ProxyAuthorization',
        'range'              => 'Zend\Http\Header\Range',
        'referer'            => 'Zend\Http\Header\Referer',
        'te'                 => 'Zend\Http\Header\TE',
        'useragent'          => 'Zend\Http\Header\UserAgent',
        'cookie'             => 'Zend\Http\Header\Cookie',
    );

}
