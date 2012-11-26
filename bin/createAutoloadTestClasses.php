<?php
/**
 * createAutoloadTestClasses.php
 *
 * A script for creating a hierarchy of classes for use with testing
 * autoloading. Each directory has classes from a to p; additional classes are
 * generated 2 levels deep, giving a total of 16^3 classes to use in
 * autoloading tests.
 */

function createClasses($depth, $namespace)
{
    foreach (range('a', 'p') as $letter) {
        // Create content for namespaced class
        $content = "<?php\nnamespace $namespace;\nclass $letter { }";

        // Write content to disk
        $dir = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        file_put_contents(
            $dir . DIRECTORY_SEPARATOR . $letter . '.php',
            $content
        );

        // If we still have depth, recurse and create more classes using the
        // current letter as a sub-namespace.
        if ($depth > 0) {
            $childDir = $dir . DIRECTORY_SEPARATOR . $letter;
            mkdir($childDir);
            createClasses($depth - 1, $namespace . '\\' . $letter);
        }
    }
}

// Use 'test' as the top-level namespace, and set a depth of "2" (will provide
// 3 levels of classes).
mkdir('test');
createClasses(2, 'test');
