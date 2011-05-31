<?php

namespace Zend\Di\Generator;

interface Introspector
{
    public function setConfiguration(array $configuration);
    public function setManagedDefinitions(ManagedDefinitions $managedDefinitions);
    public function setTypeRegistry(TypeRegistry $typeRegistry);
    public function introspect();
}
