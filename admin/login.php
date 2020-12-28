<?php

session_name('session_id');
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/data/function.php';

getUserAuth($_POST);