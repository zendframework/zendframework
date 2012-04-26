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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Search\Lucene\Index;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface TermsStreamInterface
{
    /**
     * Reset terms stream.
     */
    public function resetTermsStream();

    /**
     * Skip terms stream up to specified term preffix.
     *
     * Prefix contains fully specified field info and portion of searched term
     *
     * @param \Zend\Search\Lucene\Index\Term $prefix
     */
    public function skipTo(Term $prefix);

    /**
     * Scans terms dictionary and returns next term
     *
     * @return \Zend\Search\Lucene\Index\Term|null
     */
    public function nextTerm();

    /**
     * Returns term in current position
     *
     * @return \Zend\Search\Lucene\Index\Term|null
     */
    public function currentTerm();

    /**
     * Close terms stream
     *
     * Should be used for resources clean up if stream is not read up to the end
     */
    public function closeTermsStream();
}
