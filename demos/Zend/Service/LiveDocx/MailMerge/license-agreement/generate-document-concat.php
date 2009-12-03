#!/usr/bin/php
<?php

set_time_limit(0);

require_once dirname(__FILE__) . '/../../common.php';

/**
 * Concatenating PDF files locally - advanced
 * 
 * In the case that you wish to generate one output document that contains
 * several thousand populated templates, it is more efficient to use LiveDocx to
 * generate a large number of singular PDF files and then to concatenate them
 * locally, than it is to use the backend service directly.
 * 
 * As the size of the output document in such cases can be several hundred
 * megabytes in size, it would take a long time to transfer all data from the
 * backend service to the local server. Hence, a local concatenation approach is
 * more desirable and considerably faster.
 * 
 * In this example, the backend service is used to populate a template and
 * create a large number of documents (see variable $iterations). Then, using a 
 * 3rd party external command line tool - either pdftk (http://is.gd/4KO72) or 
 * ghostscript (http://is.gd/4LK3N) - the singular PDF files are concatenated
 * together locally to create one large output PDF file.
 *
 * NOTE: This sample application depends upon either pdftk or ghostscript being
 *       install on your system. Both are available for Linux and Microsoft
 *       Windows. Please take a look at the constants EXEC_PDFTK and
 *       EXEC_GHOSTSCRIPT. You may need to redefine these, if you are running 
 *       Windows, or if your Linux distribution installs the tools at a different
 *       location. The specified paths are correct for Debian 5.0.3.
 */

define('EXEC_PDFTK',       '/usr/bin/pdftk');
define('EXEC_GHOSTSCRIPT', '/usr/bin/gs');

define('PROCESSOR_PDFTK',       1);
define('PROCESSOR_GHOSTSCRIPT', 2);

// -----------------------------------------------------------------------------

// Processor to use for concatenation.
//
// There are 2 options (only):
//  
// o PROCESSOR_PDFTK
//   - Faster
//   - Requires little memory (RAM)
//   - No reduction in file size
//  
// o PROCESSOR_GHOSTSCRIPT
//   - Slower
//   - Requires lots of memory (RAM)
//   - Reduction in file size
//  
// If you have both installed on your system, PROCESSOR_PDFTK is recommended.

$processor = PROCESSOR_PDFTK;

// Number of documents (populated with random strings) to concatenate.

$iterations = 3;

// -----------------------------------------------------------------------------

// Logger to output status messages

$logger = new Zend_Log(new Zend_Log_Writer_Stream('php://output'));

Zend_Registry::set('logger', $logger);

// -----------------------------------------------------------------------------

// Create temporary directory

$tempDirectory = sys_get_temp_dir() . '/' . md5(rand(1, 10000) . __FILE__);

if (is_dir($tempDirectory)) {
    recursiveRemoveDirectory($tempDirectory);
}

$logger->log(sprintf('Making temporary directory %s.', $tempDirectory), Zend_Log::INFO);

mkdir($tempDirectory);

// -----------------------------------------------------------------------------

// Generate temporary documents

$tempFilenames = array();

$date = new Zend_Date();
$date->setLocale(Demos_Zend_Service_LiveDocx_Helper::LOCALE);

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME)
            ->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

$phpLiveDocx->setLocalTemplate('template.docx');

for ($iteration = 1; $iteration <= $iterations; $iteration ++) {
    
    $tempFilename = sprintf('%s/%010s.pdf', $tempDirectory, $iteration);
    $tempFilenames[] = $tempFilename;
    
    $phpLiveDocx->assign('software', randomString())
                ->assign('licensee', randomString())
                ->assign('company',  randomString())
                ->assign('date',     $date->now()->get(Zend_Date::DATE_LONG))
                ->assign('time',     $date->now()->get(Zend_Date::TIME_LONG))
                ->assign('city',     randomString())
                ->assign('country',  randomString());
        
    $phpLiveDocx->createDocument();
    
    file_put_contents($tempFilename, $phpLiveDocx->retrieveDocument('pdf'));
    
    $logger->log(sprintf('Generating temporary document %s.', $tempFilename), Zend_Log::INFO);
}

unset($phpLiveDocx, $date);

// -----------------------------------------------------------------------------

// Concatenate temporary documents and write output document

$outputFilename = './document-concat.pdf';

$logger->log('Concatenating temporary documents...', Zend_Log::INFO);

if (true === concatenatePdfFilenames($tempFilenames, $outputFilename, $processor)) {
    $logger->log(sprintf('...DONE. Saved output document as %s.', $outputFilename), Zend_Log::INFO);
} else {
    $logger->log(sprintf('...ERROR.'), Zend_Log::ERR);
}

// -----------------------------------------------------------------------------

// Delete temporary directory

$logger->log(sprintf('Deleting temporary directory %s.', $tempDirectory), Zend_Log::INFO);

if (is_dir($tempDirectory)) {
    recursiveRemoveDirectory($tempDirectory);
}

// =============================================================================

// Helper functions

/**
 * Create a random string
 * 
 * @param $length
 * @return string
 */
function randomString()
{
    $ret = '';
    
    $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    
    $poolLen   = strlen($pool);
    $stringLen = rand(5, 25);
    
    for ($i = 0; $i < $stringLen; $i ++) {
        $pos = (rand() % $poolLen);
        $ret .= $pool{$pos};
    }
    
    return $ret;
}

/**
 * Recursively remove directory
 * 
 * @param $dir
 * @return void
 */
function recursiveRemoveDirectory($dir)
{
    $files = glob($dir . '*', GLOB_MARK);
    
    foreach ($files as $file) {
        if ('/' === substr($file, - 1)) {
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    
    if (is_dir($dir)) {
        rmdir($dir);
    }
}

/**
 * Concatenate the files in passed array $inputFilenames into one file
 * $outputFilename, using concatenation processor (external 3rd party command
 * line tool) specified in $processor
 * 
 * @param $inputFilenames
 * @param $outputFilename
 * @param $processor
 * @return boolean
 */
function concatenatePdfFilenames($inputFilenames, $outputFilename, $processor = EXEC_PDFTK)
{
    $ret = false;
    
    $logger = Zend_Registry::get('logger');
    
    if (! (is_file(EXEC_PDFTK) || is_file(EXEC_GHOSTSCRIPT))) {
        $logger->log('Either pdftk or ghostscript are required for this sample application.', Zend_Log::CRIT);
        exit();
    }
    
    if (is_file($outputFilename)) {
        unlink($outputFilename);
    }    
    
    switch ($processor) {

        case PROCESSOR_PDFTK :
            $format  = '%s %s cat output %s';
            $command = sprintf($format, EXEC_PDFTK, implode($inputFilenames, ' '), $outputFilename);
        break;        
        
        case PROCESSOR_GHOSTSCRIPT :
            $format  = '%s -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dAutoFilterColorImages=false ';
            $format .= '-dAutoFilterGrayImages=false -dAutoFilterMonoImages=false ';
            $format .= '-dColorImageFilter=/FlateEncode -dCompatibilityLevel=1.3 -dEmbedAllFonts=true ';
            $format .= '-dGrayImageFilter=/FlateEncode -dMaxSubsetPct=100 -dMonoImageFilter=/CCITTFaxEncode ';
            $format .= '-dSubsetFonts=true -sOUTPUTFILE=%s %s';
            $command = sprintf($format, EXEC_GHOSTSCRIPT, $outputFilename, implode($inputFilenames, ' '));
        break;
            
        default:
            $logger->log('Invalid concatenation processor - use PROCESSOR_PDFTK or PROCESSOR_GHOSTSCRIPT only.', Zend_Log::CRIT);
            exit();
        break;
    }
    
    $command = escapeshellcmd($command);
    
    exec($command);
    
    if (is_file($outputFilename) && filesize($outputFilename) > 0) {
        $ret = true;   
    }

    return $ret;        
}