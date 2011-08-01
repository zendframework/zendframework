<?php

namespace Zend\Http;

class ResponseHeaders extends Headers
{


    /**
     * Add a redirect header
     *
     * Creates and appends a redirect header. If a non-empty status code is 
     * given, it is passed to {@link setStatusCode()}.
     * 
     * @param  string $url 
     * @param  null|int $code 
     * @return Headers
     */
    public function setRedirect($url, $code = 302)
    {
        $this->addHeader(new Header('Location', $url, true));
        if (!empty($code)) {
            $this->setStatusCode($code);
        }
        return $this;
    }



    /**
     * Render headers
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->renderStatusLine() . "\r\n"
             . parent::__toString();
    }

    /**
     * Populate object from string
     * 
     * @param  string $string 
     * @return ResponseHeaders
     */
    public function fromString($string)
    {
        $headers = preg_split("/\r\n/", $string, 2);
        if (!preg_match(self::PATTERN_STATUS_LINE, $headers[0], $matches)) {
            return $this;
        }
        $version = $matches['version'];
        $status  = $matches['status'];
        $message = $matches['message'] ?: '';
        $this->setProtocolVersion($version)
             ->setStatusCode($status, $message);

        // If we have more headers, parse them
        if (count($headers) == 2) {
            parent::fromString($headers[1]);
        }

        return $this;
    }

    /**
     * Is this a redirect header?
     *
     * Returns true if we have a 3xx status code, or if a Location header is
     * present.
     * 
     * @return bool
     */
    public function isRedirect()
    {
        $code    = $this->getStatusCode();
        $headers = $this->get('Location');
        return (((300 <= $code) && (400 > $code)) 
                || ($headers && count($headers)));
    }

    /* Potential specialized mutators */
    public function expire()
    {
    }

    public function setClientTtl($seconds)
    {
    }

    public function setEtag($etag = null, $weak = false)
    {
    }

    public function setExpires($date = null)
    {
    }

    public function setLastModified($date = null)
    {
    }

    public function setMaxAge($value)
    {
    }

    public function setNotModified()
    {
    }

    public function setPrivate($value)
    {
    }

    public function setSharedMaxAge($value)
    {
    }

    public function setTtl($seconds)
    {
    }

    public function setVary($headers, $replace = true)
    {
    }


    /* Potential specialized conditionals */

    /**
     * Do we have a Vary header?
     * 
     * @return bool
     */
    public function hasVary()
    {
        return $this->has('Vary');
    }

    public function isCacheable()
    {
    }



    /**
     * Is the status code invalid?
     *
     * Because we validate status codes, this can never return true.
     * 
     * @return false
     */
    public function isInvalid()
    {
        return false;
    }



    public function isValidateable()
    {
    }

    public function mustRevalidate()
    {
    }


    /* Potential specialized accessors */
    public function getAge() 
    {
    }

    public function getEtag()
    {
    }

    public function getExpires()
    {
    }

    public function getLastModified()
    {
    }

    public function getMaxAge()
    {
    }

    public function getTtl()
    {
    }

    public function getVary()
    {
    }

}
