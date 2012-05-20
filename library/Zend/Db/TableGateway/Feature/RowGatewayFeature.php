<?php

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\RowGateway\RowGateway,
    Zend\Db\RowGateway\RowGatewayInterface,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Exception;

class RowGatewayFeature extends AbstractFeature
{

    /**
     * @var array
     */
    protected $constructorArguments = array();

    /**
     * @param null $primaryKey
     */
    public function __construct()
    {
        $this->constructorArguments = func_get_args();
    }

    public function postInitialize()
    {
        $args = $this->constructorArguments;

        if (isset($args[0])) {
            if (is_string($args[0])) {
                $primaryKey = $args[0];
                $rowGatewayPrototype = new RowGateway($primaryKey, $this->tableGateway->table, $this->tableGateway->adapter, $this->tableGateway->sql);
                $this->tableGateway->resultSetPrototype->setRowObjectPrototype($rowGatewayPrototype);
            } elseif ($args[0] instanceof RowGatewayInterface) {
                $rowGatewayPrototype = $args[0];
                $this->tableGateway->resultSetPrototype->setRowObjectPrototype($rowGatewayPrototype);
            }
        } else {
            // get from metadata feature
            $metadata = $this->tableGateway->featureSet->getFeatureByClassName('Zend\Db\TableGateway\Feature\MetadataFeature');
            if ($metadata === false || !isset($metadata->sharedData['metadata'])) {
                throw new Exception\RuntimeException(
                    'No information was provided to the RowGatewayFeature and/or no MetadataFeature could be consulted to find the primary key necessary for RowGateway object creation.'
                );
            }
            $primaryKey = $metadata->sharedData['metadata']['primaryKey'];
            $rowGatewayPrototype = new RowGateway($primaryKey, $this->tableGateway->table, $this->tableGateway->adapter, $this->tableGateway->sql);
            $this->tableGateway->resultSetPrototype->setRowObjectPrototype($rowGatewayPrototype);
        }
    }

}
