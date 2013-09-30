<?php
namespace Namespaced\TypeHint {

    use OtherNamespace\ParameterClass;

    class Bar {

        public function method(ParameterClass $object)
        {
        }
    }
}

namespace OtherNamespace {

    class ParameterClass {

    }
}
