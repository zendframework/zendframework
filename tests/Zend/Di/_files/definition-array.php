<?php return array (
  'My\\DbAdapter' => 
  array (
    'superTypes' => 
    array (
    ),
    'instantiator' => '__construct',
    'injectionMethods' => 
    array (
      '__construct' => 
      array (
        'username' => NULL,
        'password' => NULL,
      ),
    ),
  ),
  'My\\EntityA' => 
  array (
    'superTypes' => 
    array (
    ),
    'instantiator' => NULL,
    'injectionMethods' => 
    array (
    ),
  ),
  'My\\Mapper' => 
  array (
    'superTypes' => 
    array (
      0 => 'ArrayObject',
    ),
    'instantiator' => '__construct',
    'injectionMethods' => 
    array (
      'setDbAdapter' => 
      array (
        'dbAdapter' => 'My\\DbAdapter',
      ),
    ),
  ),
  'My\\RepositoryA' => 
  array (
    'superTypes' => 
    array (
    ),
    'instantiator' => '__construct',
    'injectionMethods' => 
    array (
      'setMapper' => 
      array (
        'mapper' => 'My\\Mapper',
      ),
    ),
  ),
  'My\\RepositoryB' => 
  array (
    'superTypes' => 
    array (
      0 => 'My\\RepositoryA',
    ),
    'instantiator' => NULL,
    'injectionMethods' => 
    array (
    ),
  ),
);