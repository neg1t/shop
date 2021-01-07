<?php

session_name('session_id');
session_start();

include_once $_SERVER['DOCUMENT_ROOT'] . '/data/function.php';

$categories = getCategoriesFromDB();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Fashion</title>

  <meta name="description" content="Fashion - интернет-магазин">
  <meta name="keywords" content="Fashion, интернет-магазин, одежда, аксессуары">

  <meta name="theme-color" content="#393939">

  <link rel="preload" href="/src/fonts/opensans-400-normal.woff2" as="font">
  <link rel="preload" href="/src/fonts/roboto-400-normal.woff2" as="font">
  <link rel="preload" href="/src/fonts/roboto-700-normal.woff2" as="font">

  <link rel="icon" href="/src/img/favicon.png">
  <link rel="stylesheet" href="/src/css/style.min.css">

  <script defer src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script defer src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="/src/js/scripts.js" defer></script>
</head>
<body> 
<header class="page-header">
  <a class="page-header__logo" href="/">
    <img src="/src/img/logo.svg" alt="Fashion">
  </a>
  <nav class="page-header__menu">
    <ul class="main-menu main-menu--header">
      <?php getNavMenu() ?>
    </ul>
  </nav>
</header>