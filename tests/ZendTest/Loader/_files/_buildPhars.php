<?php
ini_set('phar.readonly', '0');

// Executable
// .phar
buildModulePhar('PharModule');
buildModulePhar('PharModuleMap');
// .phar.gz
buildModulePhar('PharModuleGz', Phar::PHAR, Phar::GZ);
// .phar.bz2
buildModulePhar('PharModuleBz2', Phar::PHAR, Phar::BZ2);
// .phar.tar
buildModulePhar('PharModulePharTar', Phar::TAR, Phar::NONE);
// .phar.tar.gz
buildModulePhar('PharModulePharTarGz', Phar::TAR, Phar::GZ);
// .phar.tar.bz2
buildModulePhar('PharModulePharTarBz2', Phar::TAR, Phar::BZ2);
// .phar.zip
buildModulePhar('PharModulePharZip', Phar::ZIP);

// Non-executable
// .tar
buildModulePhar('PharModuleTar', Phar::TAR, Phar::NONE, false);
// .tar.gz
buildModulePhar('PharModuleTarGz', Phar::TAR, Phar::GZ, false);
// .tar.bz2
buildModulePhar('PharModuleTarBz2', Phar::TAR, Phar::BZ2, false);
// .zip
buildModulePhar('PharModuleZip', Phar::ZIP, Phar::NONE, false);

// Fake Module
buildModulePhar('PharModuleFake', Phar::ZIP, Phar::NONE, false, 'fake');
buildModulePhar('PharModuleNestedFake', Phar::TAR, Phar::GZ, false, 'nestedfake');
// Nested Module
buildModulePhar('PharModuleNested', Phar::TAR, Phar::GZ, false, 'nested');
// Explicitly loaded phar
buildModulePhar('PharModuleExplicit');

function buildModulePhar($name, $format = Phar::PHAR, $compression = Phar::NONE, $executable = true, $mode = 'normal')
{
    echo "Building {$name}...\t";
    $glob = glob($name.'.*');
    if (count($glob) > 0) {
        foreach ($glob as $file) {
            if (!is_dir($file)) {
                unlink($file);
            }
        }
    }
    $filename = $name . '.phar';
    $phar = new Phar($filename);
    switch ($mode) {
        case 'normal':
            $phar['Module.php'] = "<?php \n\nnamespace {$name};\n\nclass Module\n{}";
            break;
        case 'fake':
            $phar['Module.php'] = '<?php //no class here';
            break;
        case 'nested':
            $phar[$name . '/Module.php'] = "<?php \n\nnamespace {$name};\n\nclass Module\n{}";
            break;
        case 'nestedfake':
            $phar[$name . '/Module.php'] = '<?php // no class here';
            break;
    }
    if (false === $executable) {
        $phar->convertToData($format, $compression);
    } else {
        $phar->setDefaultStub('Module.php', 'Module.php');
        if ($format !== Phar::PHAR || $compression !== Phar::NONE) {
            $phar->convertToExecutable($format, $compression);
        }
    }
    if ($format !== Phar::PHAR || $compression !== Phar::NONE) {
        unlink($filename);
    }
    echo "Done!\n";
}
