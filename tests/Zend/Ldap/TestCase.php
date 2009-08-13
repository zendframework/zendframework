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
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
/**
 * @see Zend_Ldap_Node
 */
require_once 'Zend/Ldap/Node.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
abstract class Zend_Ldap_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    protected function _createTestArrayData()
    {
        $data=array(
            'dn'          => 'cn=name,dc=example,dc=org',
            'cn'          => array('name'),
            'host'        => array('a', 'b', 'c'),
            'empty'       => array(),
            'boolean'     => array('TRUE', 'FALSE'),
            'objectclass' => array('account', 'top'),
        );
        return $data;
    }

    /**
     * @return Zend_Ldap_Node
     */
    protected function _createTestNode()
    {
        return Zend_Ldap_Node::fromArray($this->_createTestArrayData(), true);
    }
}
