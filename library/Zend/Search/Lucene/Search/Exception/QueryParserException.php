<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Search
 */

namespace Zend\Search\Lucene\Search\Exception;

use Zend\Search\Lucene\Exception;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 *
 * Special exception type, which may be used to intercept wrong user input
 */
class QueryParserException
    extends Exception\UnexpectedValueException
    implements ExceptionInterface
{}

