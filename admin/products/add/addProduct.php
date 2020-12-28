<?php 

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include $_SERVER['DOCUMENT_ROOT'] . '/data/function.php';

$validation = productValidation($_POST, $_FILES);

if ($validation === 'Success') {
    $upload = uploadProduct($_POST, $_FILES);
    
    if ($upload === 'Success') {
        echo 'Ok';
    } else {
        echo $upload;
    }
} else {
    echo $validation;
}