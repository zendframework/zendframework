<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 */

namespace Zend\Search\Lucene\Index;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 */
class FieldInfo
{
    public $name;
    public $isIndexed;
    public $number;
    public $storeTermVector;
    public $normsOmitted;
    public $payloadsStored;

    public function __construct($name, $isIndexed, $number, $storeTermVector, $normsOmitted = false, $payloadsStored = false)
    {
        $this->name            = $name;
        $this->isIndexed       = $isIndexed;
        $this->number          = $number;
        $this->storeTermVector = $storeTermVector;
        $this->normsOmitted    = $normsOmitted;
        $this->payloadsStored  = $payloadsStored;
    }
}
