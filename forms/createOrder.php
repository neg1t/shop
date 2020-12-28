<?php

include $_SERVER['DOCUMENT_ROOT'] . '/data/function.php';

if ($_POST) {
    $src = htmlentities(mysqli_real_escape_string(getConnection(), $_POST['src']));
    $productQuery = mysqli_query(getConnection(), "SELECT id, price FROM products WHERE photo = '$src'");
    $product = mysqli_fetch_assoc($productQuery);

    $user = checkUserInDB($_POST);
    $valid = orderValidation($_POST);

    if ($valid === 'Success') {
        if (!$user) {
            $user = createNewUser($_POST);
            if(createOrder($_POST, $product, $user)) {
                echo 'Ok';
            } else {
                echo 'Не удалось оформить Ваш заказ';
            }
        } else {
            if(createOrder($_POST, $product, $user)) {
                echo 'Ok';
            } else {
                echo 'Не удалось оформить Ваш заказ';
            }
        }
    } else {
        echo $valid;
    }
}