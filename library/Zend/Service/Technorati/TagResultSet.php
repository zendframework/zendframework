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

namespace Zend\Service\Technorati;

use DomDocument;

/**
 * Represents a Technorati Tag query result set.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TagResultSet extends AbstractResultSet
{
    /**
     * Number of posts that match the tag.
     *
     * @var     int
     * @access  protected
     */
    protected $postsMatched;

    /**
     * Number of blogs that match the tag.
     *
     * @var     int
     * @access  protected
     */
    protected $blogsMatched;

    /**
     * Parses the search response and retrieve the results for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment for this object
     * @param   array $options      query options as associative array
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);

        $result = $this->xpath->query('/tapi/document/result/postsmatched/text()');
        if ($result->length == 1) $this->postsMatched = (int) $result->item(0)->data;

        $result = $this->xpath->query('/tapi/document/result/blogsmatched/text()');
        if ($result->length == 1) $this->blogsMatched = (int) $result->item(0)->data;

        $this->totalResultsReturned  = (int) $this->xpath->evaluate("count(/tapi/document/item)");
        /** @todo Validate the following assertion */
        $this->totalResultsAvailable = (int) $this->getPostsMatched();
    }


    /**
     * Returns the number of posts that match the tag.
     *
     * @return  int
     */
    public function getPostsMatched()
    {
        return $this->postsMatched;
    }

    /**
     * Returns the number of blogs that match the tag.
     *
     * @return  int
     */
    public function getBlogsMatched()
    {
        return $this->blogsMatched;
    }

    /**
     * Implements AbstractResultSet::current().
     *
     * @return TagResult current result
     */
    public function current()
    {
        return new TagResult($this->results->item($this->currentIndex));
    }
}
