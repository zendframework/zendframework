<?php

namespace Zend\Http;

/**
 * Represents the HTTP Response headers
 *
 * Describes a collection of response headers. As such, it also ensures a status 
 * code and optional status message.
 */
interface HttpResponseHeaders extends HttpHeaders
{
    public function getStatusCode(); // 200, 301, etc.
    public function getStatusMessage(); 
    public function setStatusCode($code, $text = null);

    /* Sending headers, and testing sent status */
    public function renderStatusLine(); // render the "status line" portion of the header
    public function send();             // actually send headers
    public function sent();             // return boolean headers sent status


    /* Specialized response header(s) */
    public function setRedirect($url, $code = 302);

    /* Testing header status */
    public function isRedirect();       // 3XX status and/or Location header?
    public function isClientError();    // 4XX status?
    public function isEmpty();          // 201, 204, or 304 status?
    public function isForbidden();      // 403 status?
    public function isInformational();  // 1XX status?
    public function isInvalid();        // <100 or >= 600 status?
    public function isNotFound();       // 404 status?
    public function isOk();             // 200 status?
    public function isServerError();    // 5XX status?
    public function isSuccessful();     // 2XX status?

    /* Methods occurring below here need to be discussed */

    /* Potential specialized mutators * /
    public function expire();
    public function setClientTtl($seconds);
    public function setEtag($etag = null, $weak = false);
    public function setExpires($date = null);
    public function setLastModified($date = null);
    public function setMaxAge($value);
    public function setNotModified();
    public function setPrivate($value);
    public function setSharedMaxAge($value);
    public function setTtl($seconds);
    public function setVary($headers, $replace = true);

    /* Potential specialized conditionals * /
    public function hasVary();          // Vary header present? (should )
    public function isCacheable();
    public function isFresh();
    public function isNotModified(HttpRequest $request);
    public function isValidateable();
    public function mustRevalidate();

    /* Potential specialized accessors * /
    public function getAge() ;
    public function getEtag();
    public function getExpires();
    public function getLastModified();
    public function getMaxAge();
    public function getTtl();
    public function getVary();
     */
}
