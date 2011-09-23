<?php
// Executable
// .phar
buildModulePhar('PharModule');
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


function buildModulePhar($name, $format = Phar::PHAR, $compression = Phar::NONE, $executable = true)
{
    echo "Building {$name}...\t";
    $glob = glob($name.'.*');
    if (count($glob) && !is_dir($glob[0])) unlink($glob[0]);
    $filename = $name . '.phar';
    $phar = new Phar($filename);
    $phar['Module.php'] = "<?php \n\nnamespace $name;\n\nclass Module\n{}";
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
