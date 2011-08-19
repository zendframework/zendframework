<?php

$top_dir  = realpath(__DIR__ . '/../');
$temp_dir = realpath(__DIR__ . '/../tmp');

$output = null;


if (!file_exists($temp_dir)) {
    cli_print('Creating a tmp directory at ' . $temp_dir . ' to work in ... ', 'green');
	mkdir($temp_dir);
}

$output = null;
exec('which svn', $output);
if (empty($output)) {
	cli_print('Error: svn is required to check out the Phd tools!', 'red');
}

if (!file_exists($temp_dir . '/phd')) {
	cli_print('Checking out PhD via SVN ...', 'green');
	exec('svn co http://framework.zend.com/svn/framework/build-tools/trunk/build-tools/docs/ ' . $temp_dir . '/phd', $output);
	cli_nl();
}

$output = null;
exec('which xsltproc', $output);
if (empty($output)) {
	cli_print('Error: xsltproc is required!', 'red');
}

$command = 'xsltproc --xinclude ' . $temp_dir . '/phd/db4-upgrade.xsl '
    . $top_dir . '/documentation/manual/en/manual2.xml.in > '
    . $temp_dir . '/manual2.full.xml | tee -a '
    . $temp_dir . '/manual2-err.txt';
cli_print('Running: ' . $command, 'green');
cli_nl();
system($command);
cli_nl();


$command = $temp_dir . '/phd/pear/phd -g \'phpdotnet\phd\Highlighter_GeSHi\' --xinclude -f zfpackage -d '
    . $temp_dir . '/manual2.full.xml -o ' . $temp_dir . '/manual-html';
cli_print('Running: ' . $command, 'green');
cli_nl();
system($command);
cli_nl();

cli_print('[DONE]', 'green');
cli_nl();

cli_print('HTML Manual located in ' . $temp_dir . '/manual-html/zf-package-chunked-xhtml/manual.html', 'green');
cli_nl();




/** FUNCTIONS **/

function cli_print($message, $color) {
    static $is_color = null;
    if ($is_color === null) $is_color = (function_exists('posix_isatty'));
    
    list($prefix, $postfix) = array('', '');
    
    if ($is_color) {
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

function cli_nl() {
    echo "\r\n";
}