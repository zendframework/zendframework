<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\Metadata\Metadata;
use Zend\Db\Metadata\MetadataInterface;
use Zend\Db\TableGateway\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 */
class MetadataFeature extends AbstractFeature
{

    /**
     * @var MetadataInterface
     */
    protected $metadata = null;

    /**
     * Constructor
     *
     * @param Adapter $slaveAdapter
     */
    public function __construct(MetadataInterface $metadata = null)
    {
        if ($metadata) {
            $this->metadata = $metadata;
        }
        $this->sharedData['metadata'] = array(
            'primaryKey' => null,
            'columns' => array()
        );
    }

    public function initialize()
    {
        if ($this->metadata == null) {
            $this->metadata = new Metadata($this->tableGateway->adapter);
        }

        // localize variable for brevity
        $t = $this->tableGateway;
        $m = $this->metadata;

        // get column named
        $columns = $m->getColumnNames($t->table);
        $t->columns = $columns;

        // set locally
        $this->sharedData['metadata']['columns'] = $columns;

        // process primary key
        $pkc = null;

        foreach ($m->getConstraints($t->table) as $constraint) {
            /** @var $constraint \Zend\Db\Metadata\Object\ConstraintObject */
            if ($constraint->getType() == 'PRIMARY KEY') {
                $pkc = $constraint;
                break;
            }
        }

        if ($pkc === null) {
            throw new Exception\RuntimeException('A primary key for this column could not be found in the metadata.');
        }

        if (count($pkc->getKeys()) == 1) {
            $pkck = $pkc->getKeys();
            $primaryKey = $pkck[0]->getColumnName();
        } else {
            $primaryKey = array();
            foreach ($pkc->getKeys() as $key) {
                /** @var $key \Zend\Db\Metadata\Object\ConstraintKeyObject */
                $primaryKey[] = $key->getColumnName();
            }
        }

        $this->sharedData['metadata']['primaryKey'] = $primaryKey;

        $this->isInitialized = true;
    }

    /**
     * after initialization, retrieve the original adapter as "master"
     */
    public function postInitialize()
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
    }

}
