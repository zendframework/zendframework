<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Books\Extension;

/**
 * Describes a books category
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Books
 */
class BooksCategory extends
    \Zend\GData\App\Extension\Category
{

    /**
     * Constructor for Zend_Gdata_Books_Extension_BooksCategory which
     * Describes a books category
     *
     * @param string|null $term An identifier representing the category within
     *        the categorization scheme.
     * @param string|null $scheme A string containing a URI identifying the
     *        categorization scheme.
     * @param string|null $label A human-readable label for display in
     *        end-user applications.
     */
    public function __construct($term = null, $scheme = null, $label = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Books::$namespaces);
        parent::__construct($term, $scheme, $label);
    }

}
