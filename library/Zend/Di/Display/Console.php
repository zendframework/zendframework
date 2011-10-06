<?php
namespace Zend\Di\Display;

use Zend\Di\Di;

class Console
{

    public function render(Di $di)
    {

        echo 'Definitions: ' . PHP_EOL;
        foreach ($di->definitions() as $definition) {
            foreach ($definition->getClasses() as $class) {
                echo PHP_EOL . '  Parameters For Class: ' . $class . PHP_EOL;
                foreach ($definition->getMethods($class) as $methodName => $methodIsRequired) {
                    foreach ($definition->getMethodParameters($class, $methodName) as $fqName => $pData) {
                        echo '    ' . $pData[0] . ' [type: ';
                        echo ($pData[1]) ? $pData[1] : 'scalar';
                        echo ($pData[2] === true && $methodIsRequired) ? ', required' : ', not required';
                        echo ', injection-method: ' . $methodName;
                        echo ' fq-name: ' . $fqName;
                        echo ']' . PHP_EOL;
                    }
                }
            }
        }

        echo PHP_EOL . 'Instance Configuration Info:' . PHP_EOL;

        echo PHP_EOL . '  Aliases:' . PHP_EOL;

        $configuredTypes = array();
        foreach ($di->instanceManager()->getAliases() as $alias => $class) {
            echo '    ' . $alias . ' [type: ' . $class . ']' . PHP_EOL;
            $configuredTypes[] = $alias;
        }

        echo PHP_EOL . '  Classes:' . PHP_EOL;

        foreach ($di->instanceManager()->getClasses() as $class) {
            echo '    ' . $class . PHP_EOL;
            $configuredTypes[] = $class;
        }

        echo PHP_EOL . '  Configurations:' . PHP_EOL;
        
        foreach ($configuredTypes as $type) {
            $info = $di->instanceManager()->getConfiguration($type);
            echo '    ' . $type . PHP_EOL;
            foreach ($info['parameters'] as $param => $value) {
                echo '      ' . $param . ' = ' . $value . PHP_EOL;
            }
        }

    }

}