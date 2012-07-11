<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Spreadsheets\Extension;

/**
 * Concrete class for working with colCount elements.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Spreadsheets
 */
class ColCount extends \Zend\GData\Extension
{

    protected $_rootElement = 'colCount';
    protected $_rootNamespace = 'gs';

    /**
     * Constructs a new Zend_Gdata_Spreadsheets_Extension_ColCount element.
     * @param string $text (optional) Text contents of the element.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Spreadsheets::$namespaces);
        parent::__construct();
        $this->_text = $text;
    }
}
