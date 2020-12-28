<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include $_SERVER['DOCUMENT_ROOT'] . '/data/function.php';

$validation = productValidation($_POST, $_FILES, $_GET);

if ($validation === 'Success') {
    $edit = editProduct($_POST, $_GET, $_FILES);
    
    if ($edit === 'Success') {
        echo 'Ok';
    } else {
        echo $edit;
    }
} else {
    echo $validation;
}