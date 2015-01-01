<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Constraint;

class UniqueKey extends AbstractConstraint
{
    /**
     * @var string
     */
    protected $specification = 'CONSTRAINT UNIQUE KEY %s(...)';

    /**
     * @param  array $columns
     * @param  null|string $name
     */
    public function __construct($columns, $name = null)
    {
        $this->setColumns($columns);
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $colCount = count($this->columns);

        $values   = array();
        $values[] = ($this->name) ? $this->name : '';

        $newSpecTypes = array(self::TYPE_IDENTIFIER);
        $newSpecParts = array();

        for ($i = 0; $i < $colCount; $i++) {
            $newSpecParts[] = '%s';
            $newSpecTypes[] = self::TYPE_IDENTIFIER;
        }

        $newSpec = str_replace('...', implode(', ', $newSpecParts), $this->specification);

        return array(array(
            $newSpec,
            array_merge($values, $this->columns),
            $newSpecTypes,
        ));
    }
}
