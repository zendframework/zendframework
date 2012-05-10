<?php

namespace Zend\Db\Sql\Platform;

use Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Sql\SqlInterface,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Platform\PlatformInterface;

class AbstractPlatform implements PlatformDecoratorInterface, PreparableSqlInterface, SqlInterface
{
    /**
     * @var object
     */
    protected $subject = null;

    /**
     * @var PlatformDecoratorInterface[]
     */
    protected $decorators = array();

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setTypeDecorator($type, PlatformDecoratorInterface $decorator)
    {
        $this->decorators[$type] = $decorator;
    }

    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * @param Adapter $adapter
     * @return StatementInterface
     */
    public function prepareStatement(Adapter $adapter, StatementInterface $statement)
    {
        if (!$this->subject instanceof PreparableSqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Zend\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }

        $decoratorForType = false;
        foreach ($this->decorators as $type => $decorator) {
            if ($this->subject instanceof $type && $decorator instanceof PreparableSqlInterface) {
                /** @var $decoratorForType PreparableSqlInterface */
                $decoratorForType = $decorator;
                break;
            }
        }
        if ($decoratorForType) {
            $decoratorForType->setSubject($this->subject);
            $decoratorForType->prepareStatement($adapter, $statement);
        } else {
            $this->subject->prepareStatement($adapter, $statement);
        }
    }

    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        if (!$this->subject instanceof SqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Zend\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }

        $decoratorForType = false;
        foreach ($this->decorators as $type => $decorator) {
            if ($this->subject instanceof $type && $decorator instanceof SqlInterface) {
                /** @var $decoratorForType SqlInterface */
                $decoratorForType = $decorator;
                break;
            }
        }
        if ($decoratorForType) {
            $decoratorForType->setSubject($this->subject);
            return $decoratorForType->getSqlString($adapterPlatform);
        } else {
            return $this->subject->getSqlString($adapterPlatform);
        }
    }

}
