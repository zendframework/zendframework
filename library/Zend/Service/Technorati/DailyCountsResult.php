<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Technorati;

use DateTime;
use DomElement;

/**
 * Represents a single Technorati DailyCounts query result object.
 * It is never returned as a standalone object,
 * but it always belongs to a valid DailyCountsResultSet object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class DailyCountsResult extends AbstractResult
{
    /**
     * Date of count.
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Number of posts containing query on given date.
     *
     * @var     int
     * @access  protected
     */
    protected $count;


    /**
     * Constructs a new object object from DOM Document.
     *
     * @param   DomElement $dom the ReST fragment for this object
     */
    public function __construct(DomElement $dom)
    {
        $this->fields = array( 'date'   => 'date',
                               'count'  => 'count');
        parent::__construct($dom);

        // filter fields
        $this->date  = new DateTime($this->date);
        $this->count = (int) $this->count;
    }

    /**
     * Returns the date of count.
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Returns the number of posts containing query on given date.
     *
     * @return  int
     */
    public function getCount()
    {
        return $this->count;
    }
}
