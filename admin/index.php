<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include $_SERVER['DOCUMENT_ROOT'] . '/templates/header.php';

?>

<main class="page-authorization">
  <h1 class="h h--1">Авторизация</h1>
  <form class="custom-form js-login-form" method="post">
    <input type="email" name="email" class="custom-form__input" required="">
    <input type="password" name="password" class="custom-form__input" required="">
    <button class="button js-login-button" name="login" type="submit">Войти в личный кабинет</button>
  </form>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php' ?>
