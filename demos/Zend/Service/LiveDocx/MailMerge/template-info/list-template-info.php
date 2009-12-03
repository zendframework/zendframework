#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


system('clear');

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(
    PHP_EOL . 'Field and Block Field Names (merge fields)' .
    PHP_EOL . 
    PHP_EOL . 'The following templates contain the listed field or block field names:' .
    PHP_EOL .
    PHP_EOL)
);

$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();

$phpLiveDocx->setUsername(Demos_Zend_Service_LiveDocx_Helper::USERNAME)
            ->setPassword(Demos_Zend_Service_LiveDocx_Helper::PASSWORD);

// -----------------------------------------------------------------------------

$templateName = 'template-1-text-field.docx';

$phpLiveDocx->setLocalTemplate($templateName);

printf('Field names in %s:%s', $templateName, PHP_EOL);

$fieldNames = $phpLiveDocx->getFieldNames();
foreach ($fieldNames as $fieldName) {
    printf('- %s%s', $fieldName, PHP_EOL);   
}

// -----------------------------------------------------------------------------

$templateName = 'template-2-text-fields.doc';

$phpLiveDocx->setLocalTemplate($templateName);

printf('%sField names in %s:%s', PHP_EOL, $templateName, PHP_EOL);

$fieldNames = $phpLiveDocx->getFieldNames();
foreach ($fieldNames as $fieldName) {
    printf('- %s%s', $fieldName, PHP_EOL);     
}

// -----------------------------------------------------------------------------

$templateName = 'template-block-fields.doc';

$phpLiveDocx->setLocalTemplate($templateName);

printf('%sField names in %s:%s', PHP_EOL, $templateName, PHP_EOL);

$fieldNames = $phpLiveDocx->getFieldNames();
foreach ($fieldNames as $fieldName) {
    printf('- %s%s', $fieldName, PHP_EOL);     
}

printf('%sBlock names in %s:%s', PHP_EOL, $templateName, PHP_EOL);

$blockNames = $phpLiveDocx->getBlockNames();
foreach ($blockNames as $blockName) {
    printf('- %s%s', $blockName, PHP_EOL);    
}

printf('%sBlock field names in %s:%s', PHP_EOL, $templateName, PHP_EOL);

foreach ($blockNames as $blockName) {
    $blockFieldNames = $phpLiveDocx->getBlockFieldNames($blockName);
    foreach ($blockFieldNames as $blockFieldName) {
        printf('- %s::%s%s', $blockName, $blockFieldName, PHP_EOL);          
    }
}

print(PHP_EOL);

// -----------------------------------------------------------------------------

unset($phpLiveDocx);