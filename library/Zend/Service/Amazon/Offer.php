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
class Offer
{
    /**
     * @var string
     */
    public $MerchantId;

    /**
     * @var string
     */
    public $MerchantName;

    /**
     * @var string
     */
    public $GlancePage;

    /**
     * @var string
     */
    public $Condition;

    /**
     * @var string
     */
    public $OfferListingId;

    /**
     * @var string
     */
    public $Price;

    /**
     * @var string
     */
    public $CurrencyCode;

    /**
     * @var string
     */
    public $Availability;

    /**
     * @var boolean
     */
    public $IsEligibleForSuperSaverShipping = false;

    /**
     * Parse the given Offer element
     *
     * @param  DOMElement $dom
     * @return void
     */
    public function __construct(\DOMElement $dom)
    {
        $xpath = new \DOMXPath($dom->ownerDocument);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2011-08-01');
        $this->MerchantId = (string) $xpath->query('./az:Merchant/az:MerchantId/text()', $dom)->item(0)->data;
        $name = $xpath->query('./az:Merchant/az:Name/text()', $dom);
        if ($name->length == 1) {
          $this->MerchantName = (string) $name->item(0)->data;
        }
        $this->GlancePage = (string) $xpath->query('./az:Merchant/az:GlancePage/text()', $dom)->item(0)->data;
        $this->Condition = (string) $xpath->query('./az:OfferAttributes/az:Condition/text()', $dom)->item(0)->data;
        $this->OfferListingId = (string) $xpath->query('./az:OfferListing/az:OfferListingId/text()', $dom)->item(0)->data;
        $Price = $xpath->query('./az:OfferListing/az:Price/az:Amount', $dom);
        if ($Price->length == 1) {
            $this->Price = (int) $xpath->query('./az:OfferListing/az:Price/az:Amount/text()', $dom)->item(0)->data;
            $this->CurrencyCode = (string) $xpath->query('./az:OfferListing/az:Price/az:CurrencyCode/text()', $dom)->item(0)->data;
        }
        $availability = $xpath->query('./az:OfferListing/az:Availability/text()', $dom)->item(0);
        if($availability instanceof \DOMText) {
            $this->Availability = (string) $availability->data;
        }
        $result = $xpath->query('./az:OfferListing/az:IsEligibleForSuperSaverShipping/text()', $dom);
        if ($result->length >= 1) {
            $this->IsEligibleForSuperSaverShipping = (bool) $result->item(0)->data;
        }
    }
}
