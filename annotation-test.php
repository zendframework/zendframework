<?php
require_once 'vendor/autoload.php';

use Zend\Code\Annotation;
use Zend\Code\Reflection;

/**
 * @Annotation
 */
class Foo
{
    public $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }
}

class Bar implements Annotation\AnnotationInterface
{
    public function initialize($content)
    {
        $this->content = $content;
    }
}

class Inject
{
    /**
     * Foo Bar
     *
     * This is the long description
     *
     * @Foo(bar="baz")
     * @Bar(baz)
     */
    protected $value;
}

$manager = new Annotation\AnnotationManager();
$gParser = new Annotation\Parser\GenericAnnotationParser();
$gParser->registerAnnotation(new Bar());
$dParser = new Annotation\Parser\DoctrineAnnotationParser();
$dParser->registerAnnotation('Foo');
$manager->register($gParser);
$manager->register($dParser);

$r = new Reflection\ClassReflection('Inject');
foreach ($r->getProperties() as $prop) {
    $annotations = $prop->getAnnotations($manager);
    foreach ($annotations as $annotation) {
        echo var_export($annotation, 1), "\n";
    }
}
