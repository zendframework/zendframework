<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Amazon;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 */
class ListmaniaList
{
    /**
     * @var string
     */
    public $ListId;

    /**
     * @var string
     */
    public $ListName;

    /**
     * Assigns values to properties relevant to ListmaniaList
     *
     * @param  DOMElement $dom
     * @return void
     */
    public function __construct(\DOMElement $dom)
    {
        $xpath = new \DOMXPath($dom->ownerDocument);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2011-08-01');
        foreach (array('ListId', 'ListName') as $el) {
            $this->$el = (string) $xpath->query("./az:$el/text()", $dom)->item(0)->data;
        }
    }
}
