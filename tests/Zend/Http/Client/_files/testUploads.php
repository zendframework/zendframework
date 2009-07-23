<?php

if (! empty($_FILES)) {
    foreach ($_FILES as $name => $file) {
        if (is_array($file['name'])) {
            foreach($file['name'] as $k => $v) {
                echo "$name $v {$file['type'][$k]} {$file['size'][$k]}\n";
            }
        } else {
            echo "$name {$file['name']} {$file['type']} {$file['size']}\n";
        }
    }
}