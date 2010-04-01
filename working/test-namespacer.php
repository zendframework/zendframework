<?php

// global vars
$test_dir_path = null;
$test_file_path = null;
$test_file_path_relative = null;
$test_tokens = null;
$interesting_tokens = array();
$interesting_token_index = array();
$interesting_information = array();
$map = array();
$map_index = array();


setup();
analyze();

echo generate();

function setup() {
    global $test_dir_path, $test_file_path, $test_file_path_relative, $test_tokens, $map, $map_index;
    
    $test_dir_path = $_SERVER['argv'][1];
    $test_file_path_relative = $_SERVER['argv'][2];
    
    if (!file_exists($test_dir_path)) {
        die('library directory not found');
    }
    
    $test_dir_path = realpath($test_dir_path);
    
    if (!file_exists($test_dir_path . '/' . $test_file_path_relative)) {
        die('test file in library not found');
    }
    
    $test_file_path = realpath($test_dir_path . '/' . $test_file_path_relative);
    
    $test_file_contents = file_get_contents($test_file_path);
        
    if (substr($test_file_contents, 0, 5) !== '<?php') {
        die('A php file is required, start tag not found in file.');
    }
    
    $test_tokens = token_get_all($test_file_contents);
    
    $map_xml = simplexml_load_file('./PHPNamespacer-MappedClasses.xml');
    
    $i=0;
    $map_index = array();

    foreach ($map_xml->children() as $mapped_class) {
        $i++;
        $map[$i]['original_relative_file_path'] = (string) $mapped_class->originalRelativeFilePath;
        $map[$i]['original_class_name'] = (string) $mapped_class->originalClassName;
        $map[$i]['new_relative_file_path'] = (string) $mapped_class->newRelativeFilePath;
        $map[$i]['new_namespace'] = (string) $mapped_class->newNamespace;
        $map[$i]['new_class_name'] = (string) $mapped_class->newClassName;
        $map[$i]['new_fully_qualified_name'] = (string) $mapped_class->newFullyQualifiedName;
        
        $map_index['original_class_name'][$map[$i]['original_class_name']] = $i;
        $map_index['new_fully_qualified_name'][$map[$i]['new_fully_qualified_name']] = $i;
    } 

}

function analyze() {
    global $interesting_information, $test_tokens;
    
    $context = null;
    //$found_class = false;

    foreach ($test_tokens as $test_token_index => $test_token) {
        
        if (is_array($test_token)) {
            $test_token_name = token_name($test_token[0]);
            $test_token_value = $test_token[1];
        } else {
            $test_token_name = 'string:' . $test_token;
        }
        
        if ($test_token_index >= 3 && $context == 'INSIDE_OPEN_TAG') {
            $context = null;
        }
        
        // mostly for debugging
        $surrounding_tokens = array();
        $surrounding_tokens[-2] = (isset($test_tokens[$test_token_index - 2])) ? $test_tokens[$test_token_index - 2] : null;
        $surrounding_tokens[-1] = (isset($test_tokens[$test_token_index - 1])) ? $test_tokens[$test_token_index - 1] : null;
        $surrounding_tokens[1]  = (isset($test_tokens[$test_token_index + 1])) ? $test_tokens[$test_token_index + 1] : null;
        $surrounding_tokens[2]  = (isset($test_tokens[$test_token_index + 2])) ? $test_tokens[$test_token_index + 2] : null;
        
        switch ($test_token_name) {
            case 'T_OPEN_TAG':
                if ($test_token_index < 3) {
                    $context = 'INSIDE_OPEN_TAG';
                }
                break;
            case 'T_DOC_COMMENT':
                if ($context == 'INSIDE_OPEN_TAG') {
                    register_interesting_token('top_of_file', $test_token_index + 1);
                    $context = null;
                }
                register_interesting_token('docblock', $test_token_index);
                break;

            case 'T_INTERFACE':
                $context = 'INSIDE_CLASS_DECLARATION';
                $interesting_information['is_interface'] = true;
            case 'T_CLASS':
                $context = 'INSIDE_CLASS_DECLARATION';
                //$found_class = true;
                break;
            case 'T_ABSTRACT':
                $interesting_information['is_abstract'] = true;
                break;
            case 'T_EXTENDS':
            case 'T_IMPLEMENTS':
                $context = 'INSIDE_CLASS_SIGNATURE';
                break;
            case 'T_NEW':
                $context = 'INSIDE_NEW_ASSIGNMENT';
                break;
            case 'T_FUNCTION':
                $context = 'INSIDE_FUNCTION_SIGNATURE_START';
                break;
            case 'T_CATCH':
                $context = 'INSIDE_CATCH_STATEMENT';
                break;
            case 'string:{':
                $context = null;
                break;
            case 'string:(':
                if ($context == 'INSIDE_FUNCTION_SIGNATURE_START') {
                    $context = 'INSIDE_FUNCTION_SIGNATURE';
                }
                break;
            case 'string:)':
                if ($context == 'INSIDE_FUNCTION_SIGNATURE') {
                    $context = null;
                }
                break;
            case 'T_DOUBLE_COLON':
                if (!in_array($test_tokens[$test_token_index-1][1], array('self', 'parent', 'static'))) {
                    register_interesting_token('consumed_classes', $test_token_index - 1);
                }
                break;
            case 'T_INSTANCEOF':
                if (!in_array($test_tokens[$test_token_index+2][1], array('self', 'parent', 'static'))) {
                    register_interesting_token('consumed_classes', $test_token_index + 2);
                }                    
                
            case 'T_STRING':
                switch ($context) {
                    case 'INSIDE_CLASS_DECLARATION':
                        //$interesting_information['class_name'] = $test_token_value;
                        register_interesting_token('class_name', $test_token_index);
                        $context = null;
                        break;
                    case 'INSIDE_CLASS_SIGNATURE':
                        //if (strtolower($surrounding_tokens[-1][1]) == '')
                        register_interesting_token('consumed_classes', $test_token_index);
                        break;
                    case 'INSIDE_NEW_ASSIGNMENT':
                        register_interesting_token('consumed_classes', $test_token_index);
                        $context = null;
                        break;
                    case 'INSIDE_FUNCTION_SIGNATURE':
                        $safeWords = array('true', 'false', 'null', 'self', 'parent', 'static');
                        $previousToken = $surrounding_tokens[-1];
                        if (in_array($test_token[1], $safeWords)
                            || (is_array($previousToken) && $previousToken[1] == '::')) {
                            break;
                        }
                        register_interesting_token('consumed_classes', $test_token_index);
                        break;
                    case 'INSIDE_CATCH_STATEMENT':
                        register_interesting_token('consumed_classes', $test_token_index);
                        $context = null;
                        break;
                }
                
                break;
                
        }
    }
}

function register_interesting_token($reason, $token_number)
{
    global $interesting_tokens, $interesting_token_index, $test_tokens;
    
    $token = $test_tokens[$token_number];
    
    if (count($token) != 3) {
        return;
    }
    
    $tokenObj = new \ArrayObject(
        array(
            'number' => $token_number,
            'id' => $token[0],
            'value' => $token[1],
            'line' => $token[2],
            'name' => token_name($token[0])
            ),
        \ArrayObject::ARRAY_AS_PROPS
        );
    
    if (!isset($interesting_tokens[$reason])) {
        $interesting_tokens[$reason] = array();
    }
    $interesting_tokens[$reason][] = $tokenObj;
    
    if (!isset($interesting_token_index[$token_number])) {
        $interesting_token_index[$token_number] = array();
    }
    
    $interesting_token_index[$token_number][] = $reason;
}

function generate() {
    global $test_file_path_relative, 
        $interesting_information, $interesting_tokens, $interesting_token_index,
        $map, $test_tokens;
    
    $new_namespace = substr($test_file_path_relative, 0, strrpos($test_file_path_relative, '/'));
    $new_namespace = str_replace('/', '\\', $new_namespace);
    $new_namespace = preg_replace('#^Zend\\\\#', 'ZendTest\\', $new_namespace);
    
    // determine consumed classes
    $consumed_class_count = array();
    if (isset($interesting_tokens['consumed_classes'])) {
        foreach ($interesting_tokens['consumed_classes'] as $consumed_class_token) {
            $consumed_class_name_original = $consumed_class_token['value'];
            $consumed_class_info = get_map_by_original_class_name($consumed_class_token['value']);
            if ($consumed_class_info) {
                $consumed_class_name_new_full = $consumed_class_info['new_fully_qualified_name'];
                if (!isset($consumed_class_count[$consumed_class_name_new_full])) $consumed_class_count[$consumed_class_name_new_full] = 0;
                $consumed_class_count[$consumed_class_name_new_full]++; 
            }
        }
    }
    
    // compute uses
    if ($consumed_class_count) {
        $uses['declarations'] = $uses['translations'] = $uses = array();
        foreach ($consumed_class_count as $consumed_class_name => $consumed_class_occurances) {
            if ($consumed_class_occurances == 1) continue;
            if ((strpos($consumed_class_name, '\\') !== false) && (strpos($consumed_class_name, $new_namespace) !== 0)) {
                $consumed_class_info = get_map_by_new_fully_qualified_name($consumed_class_name);
                //var_dump($consumed_class_name, $consumed_class_info);
                if ($consumed_class_info) {
                    if (strpos($consumed_class_info['new_namespace'], '\\') && !in_array($consumed_class_info['new_namespace'], $uses['declarations'])) {
                        $uses['declarations'][] = $ccn = $consumed_class_info['new_namespace'];
                    }
                    $uses['translations'][$consumed_class_name] = substr($ccn, strrpos($ccn, '\\')+1) . '\\'
                        . str_replace($ccn . '\\', '', $consumed_class_info['new_fully_qualified_name']);
                }
            }
        }
    }

    $new_tokens = array();
    
    foreach ($test_tokens as $test_token_index => $test_token) {
        
        if (!array_key_exists($test_token_index, $interesting_token_index)) {
            $new_tokens[] = $test_token;
            continue;
        }

        // This token is interesting for some reason
        $interesting_reasons_by_token = $interesting_token_index[$test_token_index];
        
        foreach ($interesting_reasons_by_token as $interesting_reason) {
        
            switch ($interesting_reason) {
                case 'top_of_file':
                    $content = 'namespace ' . $new_namespace . ';' . "\n";
                    if (isset($uses['declarations']) && $uses['declarations']) {
                        foreach ($uses['declarations'] as $uses_declaration) {
                            $content .= 'use ' . $uses_declaration . ';' . "\n"; 
                        }
                    }
                    $new_tokens[] = "\n\n/**\n * @namespace\n */\n" . $content . "\n";
                    break;
                case 'class_name':
                    $new_class_name = $test_token[1];
                    if (strpos($new_class_name, '_')) {
                        $new_class_name = substr($new_class_name, strrpos($new_class_name, '_')+1);
                    }
                    $new_tokens[] = $new_class_name;                    
                    break;
                case 'consumed_classes':
                    $test_token_consumed_class_name = $test_token[1];
                    log_info('Processing consumed class name ' . $test_token_consumed_class_name);
                    
                    $consumed_class_token_info = get_map_by_original_class_name($test_token_consumed_class_name);
                    
                    if ($consumed_class_token_info) {
                        $new_consumed_class_name = $consumed_class_token_info['new_fully_qualified_name'];
                        
                        $new_consumed_class_name_namespace = substr($new_consumed_class_name, 0, strrpos($new_consumed_class_name, '\\'));
                        if (in_array($new_consumed_class_name_namespace, $uses['declarations'])) {
                            $new_consumed_class_name = substr($new_consumed_class_name_namespace, strrpos($new_consumed_class_name_namespace, '\\')+1)
                                . substr($new_consumed_class_name, strrpos($new_consumed_class_name, '\\'));
                        } else {
                            $new_consumed_class_name = '\\' . $new_consumed_class_name;
                        }
                        
                    } else {
                        $new_consumed_class_name = '\\' . $test_token_consumed_class_name;
                    }

                    $new_tokens[] = $new_consumed_class_name;
                    break;
                default:
                    $new_tokens[] = $test_token;
                    break;
            }

        }
        
    }

    $content = '';
    foreach ($new_tokens as $token) {
        $content .= (is_array($token)) ? $token[1] : $token;
    }
    
    return $content;
}

function get_map_by_original_class_name($original_class_name) {
    global $map, $map_index;
    return $map[$map_index['original_class_name'][$original_class_name]];
}

function get_map_by_new_fully_qualified_name($new_fully_qualified_name) {
    global $map, $map_index;
    return $map[$map_index['new_fully_qualified_name'][$new_fully_qualified_name]];
}

function log_info($string) {
    file_put_contents('php://STDERR', $string . "\n");
}

