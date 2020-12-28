<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once $_SERVER['DOCUMENT_ROOT'] . '/data/function.php';

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$prodPerPage = 6;
$startProd = ($page * $prodPerPage) - $prodPerPage;

$filteredProducts = getFilteredProducts($_GET, $startProd, $prodPerPage);
$prodCount = $filteredProducts['count'];
$pageCount = ceil($prodCount / $prodPerPage);

$products = $filteredProducts['products'];

?>

<section class="shop__sorting">
  <div class="shop__sorting-item custom-form__select-wrapper">
    <select class="custom-form__select js-sort-select" name="category">
      <option hidden="">Сортировка</option>
      <option class="custom-form__option" value="price" <?= isset($_GET['sorting']) && ($_GET['sorting'] === 'price_asc' || $_GET['sorting'] === 'price_desc') ? 'selected' : '' ?> >По цене</option>
      <option class="custom-form__option" value="name"  <?= isset($_GET['sorting']) && ($_GET['sorting'] === 'name_asc' || $_GET['sorting'] === 'name_desc') ? 'selected' : '' ?> >По названию</option>
    </select>
  </div>
  <div class="shop__sorting-item custom-form__select-wrapper">
    <select class="custom-form__select js-order-select" name="prices">
      <option hidden="">Порядок</option>
      <option value="asc" <?= isset($_GET['sorting']) && ($_GET['sorting'] === 'price_asc' || $_GET['sorting'] === 'name_asc') ? 'selected' : '' ?>>По возрастанию</option>
      <option value="desc" <?= isset($_GET['sorting']) && ($_GET['sorting'] === 'name_desc' || $_GET['sorting'] === 'price_desc') ? 'selected' : '' ?>>По убыванию</option>
    </select>
  </div>
  <p class="shop__sorting-res">Найдено <span class="res-sort"><?= $prodCount ?></span> <?= num_word($prodCount, array('модель', 'модели', 'моделей')) ?></p>
</section>

<section class="shop__list">
  <?php foreach($products as $product) : ?>
  <article class="shop__item product" tabindex="0">
    <div class="product__image">
      <img src="/src/img/products/<?= $product['photo'] ?>" alt="<?= $product['name'] ?>">
    </div>
    <p class="product__name"><?= $product['name'] ?></p>
    <span class="product__price"><?= $product['price'] . ' руб.' ?></span>
  </article>
  <?php endforeach; ?>
</section>

<ul class="shop__paginator paginator">
  <?php for ($i = 1; $i <= $pageCount; $i++) : ?>
  <li>
    <a class="paginator__item" href=""><?= $i ?></a>
  </li>
  <?php endfor; ?>
</ul>