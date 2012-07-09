<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\WindowsAzure\TestAsset;

use Zend\Service\WindowsAzure\Storage\TableEntity;

/**
 * Test entity
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 */
class Entity extends TableEntity
{
    /**
     * @azure Name
     */
    public $FullName;

    /**
     * @azure Age Edm.Int64
     */
    public $Age;

    /**
     * @azure Visible Edm.Boolean
     */
    public $Visible = false;
}
