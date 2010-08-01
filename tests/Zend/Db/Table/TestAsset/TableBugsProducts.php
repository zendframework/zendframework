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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Db\Table\TestAsset;


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TableBugsProducts extends \Zend\Db\Table\AbstractTable
{
    protected $_name    = 'zfbugs_products';

    protected $_referenceMap    = array(
        'Bug' => array(
            'columns'           => 'bug_id', // Deliberate non-array value
            'refTableClass'     => '\ZendTest\Db\Table\TestAsset\TableBugs',
            'refColumns'        => array('bug_id'),
            'onDelete'          => -1, // Deliberate false value
            'onUpdate'          => -1 // Deliberate false value
        ),
        'Product' => array(
            'columns'           => array('product_id'),
            'refTableClass'     => '\ZendTest\Db\Table\TestAsset\TableProducts',
            'refColumns'        => array('product_id'),
            'onDelete'          => self::CASCADE,
            'onUpdate'          => self::CASCADE
        )
    );

}
