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

namespace Zend\Search\Lucene;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TermStreamsPriorityQueue implements Index\TermsStreamInterface
{
    /**
     * Array of term streams (Zend\Search\Lucene\Index\TermsStreamInterface objects)
     *
     * @var array
     */
    protected $_termStreams;

    /**
     * Terms stream queue
     *
     * @var \Zend\Search\Lucene\Index\TermsPriorityQueue
     */
    protected $_termsStreamQueue = null;

    /**
     * Last Term in a terms stream
     *
     * @var \Zend\Search\Lucene\Index\Term
     */
    protected $_lastTerm = null;


    /**
     * Object constructor
     *
     * @param array $termStreams  array of term streams (\Zend\Search\Lucene\Index\TermsStreamInterface objects)
     */
    public function __construct(array $termStreams)
    {
        $this->_termStreams = $termStreams;

        $this->resetTermsStream();
    }

    /**
     * Reset terms stream.
     */
    public function resetTermsStream()
    {
        $this->_termsStreamQueue = new Index\TermsPriorityQueue();

        foreach ($this->_termStreams as $termStream) {
            $termStream->resetTermsStream();

            // Skip "empty" containers
            if ($termStream->currentTerm() !== null) {
                $this->_termsStreamQueue->put($termStream);
            }
        }

        $this->nextTerm();
    }

    /**
     * Skip terms stream up to specified term preffix.
     *
     * Prefix contains fully specified field info and portion of searched term
     *
     * @param \Zend\Search\Lucene\Index\Term $prefix
     */
    public function skipTo(Index\Term $prefix)
    {
        $termStreams = array();

        while (($termStream = $this->_termsStreamQueue->pop()) !== null) {
            $termStreams[] = $termStream;
        }

        foreach ($termStreams as $termStream) {
            $termStream->skipTo($prefix);

            if ($termStream->currentTerm() !== null) {
                $this->_termsStreamQueue->put($termStream);
            }
        }

        $this->nextTerm();
    }

    /**
     * Scans term streams and returns next term
     *
     * @return \Zend\Search\Lucene\Index\Term|null
     */
    public function nextTerm()
    {
        while (($termStream = $this->_termsStreamQueue->pop()) !== null) {
            if ($this->_termsStreamQueue->top() === null ||
                $this->_termsStreamQueue->top()->currentTerm()->key() !=
                            $termStream->currentTerm()->key()) {
                // We got new term
                $this->_lastTerm = $termStream->currentTerm();

                if ($termStream->nextTerm() !== null) {
                    // Put segment back into the priority queue
                    $this->_termsStreamQueue->put($termStream);
                }

                return $this->_lastTerm;
            }

            if ($termStream->nextTerm() !== null) {
                // Put segment back into the priority queue
                $this->_termsStreamQueue->put($termStream);
            }
        }

        // End of stream
        $this->_lastTerm = null;

        return null;
    }

    /**
     * Returns term in current position
     *
     * @return \Zend\Search\Lucene\Index\Term|null
     */
    public function currentTerm()
    {
        return $this->_lastTerm;
    }

    /**
     * Close terms stream
     *
     * Should be used for resources clean up if stream is not read up to the end
     */
    public function closeTermsStream()
    {
        while (($termStream = $this->_termsStreamQueue->pop()) !== null) {
            $termStream->closeTermsStream();
        }

        $this->_termsStreamQueue = null;
        $this->_lastTerm         = null;
    }
}
