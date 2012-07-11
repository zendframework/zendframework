<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Search
 */

namespace Zend\Search\Lucene\Search\Highlighter;

use Zend\Search\Lucene\Document;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
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
