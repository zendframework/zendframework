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
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Spreadsheets
 */
class WorksheetFeed extends \Zend\GData\Feed
{

    /**
     * Constructs a new Zend_Gdata_Spreadsheets_WorksheetFeed object.
     * @param DOMElement $element (optional) The DOMElement on whick to base this element.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Spreadsheets::$namespaces);
        parent::__construct($element);
    }

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend\GData\Spreadsheets\WorksheetEntry';

    /**
     * The classname for the feed.
     *
     * @var string
     */
    protected $_feedClassName = 'Zend\GData\Spreadsheets\WorksheetFeed';

}
