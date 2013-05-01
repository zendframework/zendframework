<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\Adapter;

use Zend\Authentication\Adapter;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @group      Zend_Auth
 * @group      Zend_Db_Table
 */
class DbTableTest extends DbTable\CredentialTreatmentAdapterTest
{

    protected function _setupAuthAdapter()
    {
        $this->_adapter = new Adapter\DbTable($this->_db, 'users', 'username', 'password');
    }

}
