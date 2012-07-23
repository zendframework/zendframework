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
class CustomerReview
{
    /**
     * @var string
     */
    public $Rating;

    /**
     * @var string
     */
    public $HelpfulVotes;

    /**
     * @var string
     */
    public $CustomerId;

    /**
     * @var string
     */
    public $TotalVotes;

    /**
     * @var string
     */
    public $Date;

    /**
     * @var string
     */
    public $Summary;

    /**
     * @var string
     */
    public $Content;

    /**
     * Assigns values to properties relevant to CustomerReview
     *
     * @param  DOMElement $dom
     * @return void
     */
    public function __construct(\DOMElement $dom)
    {
        $xpath = new \DOMXPath($dom->ownerDocument);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2011-08-01');
        foreach (array('Rating', 'HelpfulVotes', 'CustomerId', 'TotalVotes', 'Date', 'Summary', 'Content') as $el) {
            $result = $xpath->query("./az:$el/text()", $dom);
            if ($result->length == 1) {
                $this->$el = (string) $result->item(0)->data;
            }
        }
    }
}
