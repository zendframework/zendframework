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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\Technorati;

use DomDocument;

/**
 * Represents a Technorati TopTags or BlogPostTags queries result set.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TagsResultSet extends ResultSet
{
    /**
     * Constructs a new object object from DOM Document.
     *
     * @param   DomDocument $dom the ReST fragment for this object
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);

        $this->totalResultsReturned  = (int) $this->xpath->evaluate("count(/tapi/document/item)");
        $this->totalResultsAvailable = (int) $this->totalResultsReturned;
    }

    /**
     * Implements ResultSet::current().
     *
     * @return TagsResult current result
     */
    public function current()
    {
        return new TagsResult($this->results->item($this->currentIndex));
    }
}
