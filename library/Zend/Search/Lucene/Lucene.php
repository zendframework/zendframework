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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Search\Lucene;

use Zend\Search\Lucene\Exception\UnsupportedMethodCallException;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Lucene
{
    /**
     * Default field name for search
     *
     * Null means search through all fields
     *
     * @var string
     */
    private static $_defaultSearchField = null;

    /**
     * Result set limit
     *
     * 0 means no limit
     *
     * @var integer
     */
    private static $_resultSetLimit = 0;

    /**
     * Terms per query limit
     *
     * 0 means no limit
     *
     * @var integer
     */
    private static $_termsPerQueryLimit = 1024;

    /**
     * Create index
     *
     * @param mixed $directory
     * @return \Zend\Search\Lucene\SearchIndexInterface
     */
    public static function create($directory)
    {
        return new Index($directory, true);
    }

    /**
     * Open index
     *
     * @param mixed $directory
     * @return \Zend\Search\Lucene\SearchIndexInterface
     */
    public static function open($directory)
    {
        return new Index($directory, false);
    }

    /**
     * @throws \Zend\Search\Lucene\Exception\UnsupportedMethodCallException
     */
    public function __construct()
    {
        throw new UnsupportedMethodCallException('\Zend\Search\Lucene class is the only container for static methods. Use Lucene::open() or Lucene::create() methods.');
    }

    /**
     * Set default search field.
     *
     * Null means, that search is performed through all fields by default
     *
     * Default value is null
     *
     * @param string $fieldName
     */
    public static function setDefaultSearchField($fieldName)
    {
        self::$_defaultSearchField = $fieldName;
    }

    /**
     * Get default search field.
     *
     * Null means, that search is performed through all fields by default
     *
     * @return string
     */
    public static function getDefaultSearchField()
    {
        return self::$_defaultSearchField;
    }

    /**
     * Set result set limit.
     *
     * 0 (default) means no limit
     *
     * @param integer $limit
     */
    public static function setResultSetLimit($limit)
    {
        self::$_resultSetLimit = $limit;
    }

    /**
     * Get result set limit.
     *
     * 0 means no limit
     *
     * @return integer
     */
    public static function getResultSetLimit()
    {
        return self::$_resultSetLimit;
    }

    /**
     * Set terms per query limit.
     *
     * 0 means no limit
     *
     * @param integer $limit
     */
    public static function setTermsPerQueryLimit($limit)
    {
        self::$_termsPerQueryLimit = $limit;
    }

    /**
     * Get result set limit.
     *
     * 0 (default) means no limit
     *
     * @return integer
     */
    public static function getTermsPerQueryLimit()
    {
        return self::$_termsPerQueryLimit;
    }
}
