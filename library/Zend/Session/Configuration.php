<?php

namespace Zend\Session;

interface Configuration
{
    public function setSavePath($path);
    public function getSavePath();
    public function setName($name);
    public function getName();
    public function setSaveHandler($saveHandler);
    public function getSaveHandler();
    public function setGcProbability($gcProbability);
    public function getGcProbability();
    public function setGcDivisor($gcDivisor);
    public function getGcDivisor();
    public function setGcMaxlifetime($gcMaxlifetime);
    public function getGcMaxlifetime();
    public function setSerializeHandler($serializeHandler);
    public function getSerializeHandler();
    public function setCookieLifetime($cookieLifetime);
    public function getCookieLifetime();
    public function setCookiePath($cookiePath);
    public function getCookiePath();
    public function setCookieDomain($cookieDomain);
    public function getCookieDomain();
    public function setCookieSecure($cookieSecure);
    public function getCookieSecure();
    public function setCookieHttponly($cookieHTTPOnly);
    public function getCookieHTTPOnly();
    public function setUseCookies($flag);
    public function getUseCookies();
    public function setUseOnlyCookies($flag);
    public function getUseOnlyCookies();
    public function setRefererCheck($referer_check);
    public function getRefererCheck();
    public function setEntropyFile($path);
    public function getEntropyFile();
    public function setEntropyLength($entropyLength);
    public function getEntropyLength();
    public function setCacheLimiter($cacheLimiter);
    public function getCacheLimiter();
    public function setCacheExpire($cacheExpire);
    public function getCacheExpire();
    public function setUseTransSid($flag);
    public function getUseTransSid();
    public function setHashFunction($hashFunction);
    public function getHashFunction();
    public function setHashBitsPerCharacter($hashBitsPerCharacter);
    public function getHashBitsPerCharacter();
    public function setUrlRewriterTags($urlRewriterTags);
    public function getUrlRewriterTags();
    public function setRememberMeSeconds($seconds);
    public function getRememberMeSeconds();
    public function setOptions(array $options);
}
