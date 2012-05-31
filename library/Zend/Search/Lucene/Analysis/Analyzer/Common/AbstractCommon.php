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

namespace Zend\Search\Lucene\Analysis\Analyzer\Common;

use Zend\Search\Lucene\Analysis\Analyzer\AnalyzerInterface,
    Zend\Search\Lucene\Analysis,
    Zend\Search\Lucene\Analysis\TokenFilter\TokenFilterInterface;

/**
 * AbstractCommon implementation of the analyzerfunctionality.
 *
 * There are several standard standard subclasses provided
 * by Analysis subpackage.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
