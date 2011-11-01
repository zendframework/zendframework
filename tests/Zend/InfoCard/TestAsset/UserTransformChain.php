<?php

namespace ZendTest\Infocard\TestAsset;
use Zend\InfoCard\XML\Security\Transform\TransformChain;

class UserTransformChain extends TransformChain
{
    protected function _findClassbyURI($uri)
    {
        switch($uri) {
            case 'http://www.w3.org/2000/09/xmldsig#enveloped-signature':
                return 'ZendTest\InfoCard\XML\Security\Transform\EnvelopedSignatureUsersNotExists';
            case 'http://www.w3.org/2001/10/xml-exc-c14n#':
                return 'ZendTest\InfoCard\XML\Security\Transform\XMLExcC14NUsersNotExists';
            default:
                throw new Security\Exception\InvalidArgumentException("Unknown or Unsupported Transformation Requested");
        }
    }
}
