<?php
namespace Zend\Di\Display;

use Zend\Di\Di;

class Console
{

    /**
     * @var Di
     */
    protected $di = null;
    protected $runtimeClasses = array();

    public static function export(Di $di, array $runtimeClasses = array())
    {
        $console = new static($di);
        $console->addRuntimeClasses($runtimeClasses);
        $console->render($di);
    }

    public function __construct(Di $di = null)
    {
        $this->di = ($di) ?: new Di;
    }

    public function addRuntimeClasses(array $runtimeClasses)
    {
        foreach ($runtimeClasses as $runtimeClass) {
            $this->addRuntimeClass($runtimeClass);
        }
    }

    public function addRuntimeClass($runtimeClass)
    {
        $this->runtimeClasses[] = $runtimeClass;
    }


    public function render()
    {

        $knownClasses = array();

        echo 'Definitions' . PHP_EOL . PHP_EOL;

        foreach ($this->di->definitions() as $definition) {
            $this->renderDefinition($definition);
            foreach ($definition->getClasses() as $class) {
                $knownClasses[] = $class;
                $this->renderClassDefinition($class);
            }
            if (count($definition->getClasses()) == 0) {
                echo PHP_EOL .'    No Classes Found' . PHP_EOL . PHP_EOL;
            }
        }

        if ($this->runtimeClasses)
        echo '  Runtime classes:' . PHP_EOL;

        $unKnownRuntimeClasses = array_diff($this->runtimeClasses, $knownClasses);
        foreach ($unKnownRuntimeClasses as $runtimeClass) {
            //$definition = $this->di->definitions()->getDefinitionForClass($runtimeClass);
            $this->renderClassDefinition($runtimeClass);
        }


        echo PHP_EOL . 'Instance Configuration Info:' . PHP_EOL;

        echo PHP_EOL . '  Aliases:' . PHP_EOL;

        $configuredTypes = array();
        foreach ($this->di->instanceManager()->getAliases() as $alias => $class) {
            echo '    ' . $alias . ' [type: ' . $class . ']' . PHP_EOL;
            $configuredTypes[] = $alias;
        }

        echo PHP_EOL . '  Classes:' . PHP_EOL;

        foreach ($this->di->instanceManager()->getClasses() as $class) {
            echo '    ' . $class . PHP_EOL;
            $configuredTypes[] = $class;
        }

        echo PHP_EOL . '  Configurations:' . PHP_EOL;
        
        foreach ($configuredTypes as $type) {
            $info = $this->di->instanceManager()->getConfiguration($type);
            echo '    ' . $type . PHP_EOL;

            if ($info['parameters']) {
                echo '      parameters:' . PHP_EOL;
                foreach ($info['parameters'] as $param => $value) {
                    echo '        ' . $param . ' = ' . $value . PHP_EOL;
                }
            }

            if ($info['injections']) {
                echo '      injections:' . PHP_EOL;
                foreach ($info['injections'] as $injection => $value) {
                    var_dump($injection, $value);
                }
            }
        }

    }

    protected function renderDefinition($definition)
    {
        echo '  Definition Type: ' . get_class($definition) . PHP_EOL;
        $r = new \ReflectionClass($definition);
        foreach ($r->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED) as $property) {
            $property->setAccessible(true);
            echo '    internal property: ' . $property->getName();
            $value = $property->getValue($definition);
            if (is_object($value)) {
                echo ' instance of ' . get_class($value);
            } else {
                echo ' = ' . $value;
            }
            echo PHP_EOL;
        }
    }

    protected function renderClassDefinition($class)
    {
        $definitions = $this->di->definitions();

        echo PHP_EOL . '    Parameters For Class: ' . $class . PHP_EOL;
        foreach ($definitions->getMethods($class) as $methodName => $methodIsRequired) {
            foreach ($definitions->getMethodParameters($class, $methodName) as $fqName => $pData) {
                echo '      ' . $pData[0] . ' [type: ';
                echo ($pData[1]) ? $pData[1] : 'scalar';
                echo ($pData[2] === true && $methodIsRequired) ? ', required' : ', not required';
                echo ', injection-method: ' . $methodName;
                echo ' fq-name: ' . $fqName;
                echo ']' . PHP_EOL;
            }
        }
        echo PHP_EOL;
    }

}