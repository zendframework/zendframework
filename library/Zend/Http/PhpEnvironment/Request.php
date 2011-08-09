<?php

namespace Zend\Http\PhpEnvironment;

use Zend\Http\Request as HttpRequest,
    Zend\Stdlib\Parameters;

class Request extends HttpRequest
{
    public function __construct()
    {
        $this->setEnv(new Parameters($_ENV));
        $this->setPost(new Parameters($_POST));
        $this->setQuery(new Parameters($_GET));
        $this->setServer(new Parameters($_SERVER));
        if ($_COOKIE) {
            $this->setCookies(new Parameters($_COOKIE));
        }
        if ($_FILES) {
            $this->setFiles(new Parameters($_FILES));
        }
    }
}