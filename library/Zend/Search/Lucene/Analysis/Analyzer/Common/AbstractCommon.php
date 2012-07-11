<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Search
 */

namespace Zend\Search\Lucene\Analysis\Analyzer\Common;

use Zend\Search\Lucene\Analysis;
use Zend\Search\Lucene\Analysis\Analyzer\AnalyzerInterface;
use Zend\Search\Lucene\Analysis\TokenFilter\TokenFilterInterface;

/**
 * AbstractCommon implementation of the analyzerfunctionality.
 *
 * There are several standard standard subclasses provided
 * by Analysis subpackage.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 */
abstract class AbstractCommon extends Analysis\Analyzer\AbstractAnalyzer
{
    /**
     * The set of Token filters applied to the Token stream.
     * Array of \Zend\Search\Lucene\Analysis\TokenFilter\TokenFilterInterface objects.
     *
     * @var array
     */
    private $_filters = array();

    /**
     * Add Token filter to the AnalyzerInterface
     *
     * @param \Zend\Search\Lucene\Analysis\TokenFilter\TokenFilterInterface $filter
     */
    public function addFilter(TokenFilterInterface $filter)
    {
        $this->_filters[] = $filter;
    }

    /**
     * Apply filters to the token. Can return null when the token was removed.
     *
     * @param \Zend\Search\Lucene\Analysis\Token $token
     * @return \Zend\Search\Lucene\Analysis\Token
     */
    public function normalize(Analysis\Token $token)
    {
        foreach ($this->_filters as $filter) {
            $token = $filter->normalize($token);

            // resulting token can be null if the filter removes it
            if ($token === null) {
                return null;
            }
        }

        return $token;
    }
}
