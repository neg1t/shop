<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ob_start();
include $_SERVER['DOCUMENT_ROOT'] . '/templates/header.php';

if (!isset($_SESSION['ut']) || $_SESSION['ut'] !== 'admin') {
    ob_clean();
    header('Location: /');
}

$allowedTypes   = ['image/jpeg', 'image/jpg', 'image/png'];
$acceptTypes    = implode(', ', $allowedTypes);

if (!empty($_GET) && isset($_GET['edit_id'])) {
    $editingProductQuery = mysqli_query(getConnection(), "SELECT id, name, FLOOR(price) AS price, photo, sale, new FROM products WHERE id='".$_GET['edit_id']."'");
    $prod = mysqli_fetch_assoc($editingProductQuery);
    
    $prodCategoriesQuery = mysqli_query(getConnection(), "SELECT c_p.product_id AS id, c.name AS category FROM category_product AS c_p
                                                            LEFT JOIN categories AS c ON c.id = c_p.category_id
                                                            WHERE c_p.product_id = '".$_GET['edit_id']."'");
    $prodCategories = mysqli_fetch_all($prodCategoriesQuery, MYSQLI_ASSOC);

    $selectedCategories = [];
    foreach ($prodCategories as $prodCategory) {
        array_push($selectedCategories, $prodCategory['category']);
    }
}

?>

<main class="page-add">
  <h1 class="h h--1">Редактирование товара</h1>
  <form class="custom-form" id="js-form-edit" method="post" enctype="multipart/form-data">
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Данные о товаре</legend>
      <label for="product-name" class="custom-form__input-wrapper page-add__first-wrapper">
        <input placeholder="Название товара" type="text" class="custom-form__input" name="product-name" id="product-name" value='<?= $prod['name'] ?>'>
      </label>
      <label for="product-price" class="custom-form__input-wrapper">
        <input placeholder="Цена товара" type="text" class="custom-form__input" name="product-price" id="product-price" value='<?= $prod['price'] ?>'>
      </label>
    </fieldset>
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Фотография товара</legend>
      <ul class="add-list">
        <li class="add-list__item add-list__item--add">
          <input type="file" accept="<?= $acceptTypes ?>" name="product-photo" id="product-photo" hidden="">
          <label for="product-photo">Добавить фотографию</label>
        </li>
        <li id="img-from-DB" class="add-list__item add-list__item--active">
          <img src="/src/img/products/<?= $prod['photo'] ?>" alt="Изображение товара">
        </li>
      </ul>
    </fieldset>
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Раздел</legend>
      <div class="page-add__select">
        <select name="category[]" class="custom-form__select" multiple="multiple">
          <option hidden="">Название раздела</option>
          <?php foreach ($categories as $category) : ?>
            <option value="<?= $category['name'] ?>" <?= in_array($category['name'], $selectedCategories) ? 'selected' : '' ?>><?= $category['name'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <input type="checkbox" name="new" id="new" class="custom-form__checkbox" <?= $prod['new'] === '1' ? 'checked' : '' ?> >
      <label for="new" class="custom-form__checkbox-label">Новинка</label>
      <input type="checkbox" name="sale" id="sale" class="custom-form__checkbox" <?= $prod['sale'] === '1' ? 'checked' : '' ?> >
      <label for="sale" class="custom-form__checkbox-label">Распродажа</label>
    </fieldset>
    <button class="button" id="js-edit-product-btn" type="submit">Изменить товар</button>
  </form>
  <section class="shop-page__popup-end page-add__popup-end" hidden="">
    <div class="shop-page__wrapper shop-page__wrapper--popup-end">
      <h2 class="h h--1 h--icon shop-page__end-title">Товар успешно изменен</h2>
    </div>
  </section>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php' ?>