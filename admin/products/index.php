<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ob_start();
include $_SERVER['DOCUMENT_ROOT'] . '/templates/header.php';

if (!isset($_SESSION['ut'])) {
    ob_clean();
    header('Location: /');
} elseif ($_SESSION['ut'] === 'operator') {
    ob_clean();
    header('Location: /admin/orders/');
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$prodPerPage = 10;
$startProd = ($page * $prodPerPage) - $prodPerPage;

$productsList = getProductsList($startProd, $prodPerPage);
$prodCount = $productsList['count'];
$pageCount = ceil($prodCount / $prodPerPage);

$products = $productsList['products'];
?>

<main class="page-products">
  <h1 class="h h--1">Товары</h1>
  <a class="page-products__button button" href="/admin/products/add/">Добавить товар</a>
  <div class="page-products__header">
    <span class="page-products__header-field">Название товара</span>
    <span class="page-products__header-field">ID</span>
    <span class="page-products__header-field">Цена</span>
    <span class="page-products__header-field">Категория</span>
    <span class="page-products__header-field">Новинка</span>
  </div>
  <ul class="page-products__list">
  <?php foreach ($products as $product) : ?>
    <li class="product-item page-products__item">
        <b class="product-item__name"><?= $product['name'] ?></b>
        <span class="product-item__field js-product-id"><?= $product['id'] ?></span>
        <span class="product-item__field"><?= $product['price'] ?></span>
        <span class="product-item__field"><?= $product['category']  ?></span>
        <span class="product-item__field"><?= $product['new'] === '1' ? 'Да' : 'Нет' ?></span>
        <a href="/admin/products/edit/?edit_id=<?= $product['id'] ?>" class="product-item__edit" aria-label="Редактировать"></a>
        <button class="product-item__delete" name="asd" type="submit"></button>
    </li>
    <?php endforeach; ?>
  </ul>

  <ul class="shop__paginator paginator" style="margin-top: 20px; display: flex; justify-content: center;">
  <?php for ($i = 1; $i <= $pageCount; $i++) : ?>
  <li>
    <a class="paginator__item" href=""><?= $i ?></a>
  </li>
  <?php endfor; ?>
</ul>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php' ?>