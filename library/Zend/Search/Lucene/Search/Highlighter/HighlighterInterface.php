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
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Search\Lucene\Search\Highlighter;

use Zend\Search\Lucene\Document;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface HighlighterInterface
{
    /**
     * Set document for highlighting.
     *
     * @param \Zend\Search\Lucene\Document\HTML $document
     */
    public function setDocument(Document\HTML $document);

    /**
     * Get document for highlighting.
     *
     * @return \Zend\Search\Lucene\Document\HTML $document
     */
    public function getDocument();

    /**
     * Highlight specified words (method is invoked once per subquery)
     *
     * @param string|array $words  Words to highlight. They could be organized using the array or string.
     */
    public function highlight($words);
}
