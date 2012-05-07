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
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Search\Lucene\Analysis\Analyzer;

/**
 * An AnalyzerInterface is used to analyze text.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface AnalyzerInterface
{
    /**
     * Tokenize text to terms
     * Returns array of Zend\Search\Lucene\Analysis\Token objects
     *
     * Tokens are returned in UTF-8 (internal Zend_Search_Lucene encoding)
     *
     * @param string $data
     * @return array
     */
    public function tokenize($data, $encoding = '');

    /**
     * Tokenization stream API
     * Set input
     *
     * @param string $data
     */
    public function setInput($data, $encoding = '');

    /**
     * Reset token stream
     */
    public function reset();

    /**
     * Tokenization stream API
     * Get next token
     * Returns null at the end of stream
     *
     * Tokens are returned in UTF-8 (internal Zend_Search_Lucene encoding)
     *
     * @return \Zend\Search\Lucene\Analysis\Token|null
     */
    public function nextToken();
}
