<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';


$templateFilename     = 'maintemplate.docx';
$subTemplate1Filename = 'subtemplate1.docx';
$subTemplate2Filename = 'subtemplate2.docx';

$templateFilesnames   = array();
$templateFilesnames[] = $templateFilename;
$templateFilesnames[] = $subTemplate1Filename;
$templateFilesnames[] = $subTemplate2Filename;