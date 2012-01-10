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
 * @package    Zend_Gdata
 * @subpackage Spreadsheets
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\Spreadsheets;

use Zend\GData\Spreadsheets;

/**
 * Concrete class for working with Atom entries.
 *
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\Spreadsheets
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Spreadsheets
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $service = new Spreadsheets($this->getHttpClient());
        return $service->getWorksheetFeed($this);
    }

}
