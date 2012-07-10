<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Spreadsheets;

use Zend\GData\Spreadsheets;

/**
 * Concrete class for working with Atom entries.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Spreadsheets
 */
class SpreadsheetEntry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\Spreadsheets\SpreadsheetEntry';

    /**
     * Constructs a new Zend_Gdata_Spreadsheets_SpreadsheetEntry object.
     * @param DOMElement $element (optional) The DOMElement on which to base this object.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Spreadsheets::$namespaces);
        parent::__construct($element);
    }

    /**
     * Returns the worksheets in this spreadsheet
     *
     * @return \Zend\GData\Spreadsheets\WorksheetFeed The worksheets
     */
    public function getWorksheets()
    {
        $service = new Spreadsheets($this->getService()->getHttpClient());
        return $service->getWorksheetFeed($this);
    }

}
