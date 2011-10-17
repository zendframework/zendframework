<?php
namespace cli;

$topDir  = realpath(__DIR__ . '/../');
$tempDir = $topDir . '/tmp';

if (!file_exists($tempDir)) {
    colorPrint('Creating a tmp directory at ' . $tempDir . ' to work in ... ', 'green');
	mkdir($tempDir);
}

$output = null;
exec('which svn', $output);
if (empty($output)) {
	colorPrint('Error: svn is required to check out the Phd tools!', 'red');
}

if (!file_exists($tempDir . '/phd')) {
	colorPrint('Checking out PhD via SVN ...', 'green');
	exec('svn co http://framework.zend.com/svn/framework/build-tools/trunk/build-tools/docs/ ' . $tempDir . '/phd', $output);
	nl();
}

$output = null;
exec('which xsltproc', $output);
if (empty($output)) {
	colorPrint('Error: xsltproc is required!', 'red');
}

$command = 'xsltproc --xinclude ' . $tempDir . '/phd/db4-upgrade.xsl '
    . $topDir . '/documentation/manual/en/manual.xml.in > '
    . $tempDir . '/manual.full.xml | tee -a '
    . $tempDir . '/manual-err.txt';
colorPrint('Running: ' . $command, 'green');
nl();
system($command);
nl();


$command = $tempDir . '/phd/pear/phd -g \'phpdotnet\phd\Highlighter_GeSHi\' --xinclude -f zfpackage -d '
    . $tempDir . '/manual.full.xml -o ' . $tempDir . '/manual-html';
colorPrint('Running: ' . $command, 'green');
nl();
system($command);
nl();

colorPrint('[DONE]', 'green');
nl();

colorPrint('HTML Manual located in ' . $tempDir . '/manual-html/zf-package-chunked-xhtml/manual.html', 'green');
nl();

/** FUNCTIONS **/

function colorPrint($message, $color) {
    static $isColor = null;
    if ($isColor === null) {
        $isColor = (function_exists('posix_isatty'));
    }
    
    list($prefix, $postfix) = array('', '');
    
    if ($isColor) {
        switch ($color) {
            case 'green':
                list($prefix, $postfix) = array("\033[32m", "\033[37m");
                break;
            case 'red':
                list($prefix, $postfix) = array("\033[31m", "\033[37m");
                break;
        }
    }
    
    echo $prefix . $message . $postfix;
}

function nl() {
    echo "\r\n";
}
