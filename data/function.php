<?php


function getConnection() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/data/config.php';
    static $db;

    if (empty($db)){
        $db = mysqli_connect(HOST, USER, PASSWORD, DBNAME);
    }

    return $db;
}



function deletePhoto($photo)
{
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/src/img/products/';
    $pathInfo  = pathinfo($photo);

    unlink($uploadDir . $pathInfo['basename']);
}

function deleteCategories($id)
{
    mysqli_query(getConnection(), "DELETE FROM category_product WHERE product_id = '$id'");
}

function deleteOrders($id)
{
    mysqli_query(getConnection(), "DELETE FROM orders WHERE product_id = '$id'");
}

function deleteProduct($data)
{
    $productId   = htmlentities(mysqli_real_escape_string(getConnection(), $data['productId']));
    $photo       = mysqli_fetch_assoc(mysqli_query(getConnection(), "SELECT photo FROM products WHERE id = '$productId'"))['photo'];

    $ordersQuery = mysqli_query(getConnection(), "SELECT id FROM orders WHERE product_id = '$productId' AND status = 'Не обработан'");
    $productInOrders = mysqli_fetch_all($ordersQuery, MYSQLI_ASSOC);

    if (count($productInOrders) !== 0) {
        $products = [];    
        foreach ($productInOrders as $prod) {
            foreach ($prod as $p) {
                array_push($products, $p);
            }
        }
        $products = implode(', ', $products);
        echo 'Не удается удалить товар, он нахоодится в следующих заказах ' . $products;
    } else {
        //удаляем товары с категорий и заказов
        deleteCategories($productId);
        deleteOrders($productId);
        $query = mysqli_query(getConnection(), "DELETE FROM products WHERE id = '$productId'");

        if ($query) {
            deletePhoto($photo);
            echo 'Ok';
        } else {
            echo 'Не удалось удалить продукт';
        }
    }
}

function productValidation($data, $file, $get = 'default')
{
    if (!empty($data)) {
        $productName = $data['product-name'];
        $productPrice = $data['product-price'];
        $category = isset($data['category']) ? $data['category'] : '';
    
        if (empty($productName)) {
            return 'Вы не ввели название товара';
        } elseif (empty($productPrice)) {
            return 'Вы не ввели цену товара';
        } elseif (!preg_match('/^\d+$/', $productPrice)) {
            return 'Не вверный формат цены товара';
        } elseif (empty($category)) {
            return 'Не выбрана ни одна категория для данного товара';
        }
    }
    
    if (isset($get['loadPhoto']) && $get['loadPhoto'] === '1' || $get === 'default') {
        if (!empty($file['product-photo']['name'])) {
            $allowedTypes   = ['image/jpeg', 'image/jpg', 'image/png'];
            $filePath =  $file['product-photo']['tmp_name'];
            $infoOpen = finfo_open(FILEINFO_MIME_TYPE);
            $infoFile = finfo_file($infoOpen, $filePath);
        
            if (!in_array($infoFile, $allowedTypes)) {
                return 'Неверный формат файла';
            }
        } else {
            return 'Вы не загрузили картинку товара';
        }
    }

    return 'Success';
}

function uploadProduct($data, $file)
{
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/src/img/products/';
    $fileName = htmlentities(mysqli_real_escape_string(getConnection(), $file['product-photo']['name']));
    $filePath = $file['product-photo']['tmp_name'];

    $name = htmlentities(mysqli_real_escape_string(getConnection(), $data['product-name']));
    $price = htmlentities(mysqli_real_escape_string(getConnection(), $data['product-price']));
    $new = isset($data['new']) && $data['new'] === 'on' ? 1 : 0;
    $sale = isset($data['sale']) && $data['sale'] === 'on' ? 1 : 0;

    // Если картинка с таким названием уже существует, дабвляем к имени data('U')
    $pathName = pathinfo($fileName, PATHINFO_FILENAME);
    $pathExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $nameWithDate = $pathName . '_' . date('U') . '.' . $pathExtension;
    $uploadFileName = file_exists($uploadDir . $fileName) ? $nameWithDate : $fileName;

    if (move_uploaded_file($filePath, $uploadDir . $uploadFileName)) {
        $query = mysqli_query(getConnection(), "INSERT INTO products SET name = '".$name."', price = '".$price."', photo = '".$uploadFileName."', sale = '$sale', new = '$new'");
        if ($query) {
            $lastId = mysqli_insert_id(getConnection());
            $chosenCategories = $data['category'];
    
            foreach ($chosenCategories as $category) {
                $categoryQuery = mysqli_query(getConnection(), "SELECT id from categories WHERE name = '".$category."'");
                $categoryId = mysqli_fetch_assoc($categoryQuery)['id'];
                mysqli_query(getConnection(), "INSERT into category_product (product_id, category_id) VALUES ('$lastId', '$categoryId')");
            }

            return 'Success';
        } else {
            return 'Не удалось загрузить товар в базу данных';
        }
    } else {
        return 'Не удалось загрузить файл';
    }
}

function orderValidation($data) 
{
    if (empty($data['surname'])) {
        return 'Введите Фамилию';
    } elseif (empty($data['name'])) {
        return 'Введите Имя';
    } elseif (empty($data['thirdName'])) {
        return 'Введите Отчество';
    } elseif (empty($data['phone'])) {
        return 'Введите номер телефона';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return 'Некорректный email';
    }

    if ($data['delivery'] === 'dev-yes') {
        if (empty($data['city'])) {
            return 'Введите город достваки';
        } elseif (empty($data['street'])) {
            return 'Введите улицу достваки';
        } elseif (empty($data['home'])) {
            return 'Введите дом достваки';
        } elseif (empty($data['aprt'])) {
            return 'Введите квартиру достваки';
        }
    }

    return 'Success';
}

function checkUserInDB($data)
{
    $mail  = htmlentities(mysqli_real_escape_string(getConnection(), $data['email']));

    $query = mysqli_query(getConnection(), "SELECT * FROM users WHERE login = '$mail'");
    $user  = mysqli_fetch_assoc($query);

    if ($user) {
        return $user['id'];
    } else {
        return false;
    }
}

function createNewUser($data)
{
    $mail = htmlentities(mysqli_real_escape_string(getConnection(), $data['email']));
    $name = htmlentities(mysqli_real_escape_string(getConnection(), $data['name']));
    $surname = htmlentities(mysqli_real_escape_string(getConnection(), $data['surname']));
    $thirdname = htmlentities(mysqli_real_escape_string(getConnection(), $data['thirdName']));
    $phone = htmlentities(mysqli_real_escape_string(getConnection(), $data['phone']));

    //add new user in DataBase
    $query = mysqli_query(getConnection(), "INSERT INTO users (login, name, surname, thirdname, phone) 
                                            values ('$mail', '$name', '$surname', '$thirdname', '$phone')");
    if ($query) {
        $userId = mysqli_insert_id(getConnection());
        mysqli_query(getConnection(), "INSERT INTO group_user (user_id, group_id) VALUES ('$userId', 3)");
        return $userId;
    } else {
        return false;
    }
}

function createOrder($data, $product, $user)
{
    $minPrice       = 2000;
    $deliveryPrice  = 280;
    $productId      = $product['id'];
    $price          = $product['price'];
    $city           = htmlentities(mysqli_real_escape_string(getConnection(), $data['city']));
    $street         = htmlentities(mysqli_real_escape_string(getConnection(), $data['street']));
    $home           = htmlentities(mysqli_real_escape_string(getConnection(), $data['home']));
    $aprt           = htmlentities(mysqli_real_escape_string(getConnection(), $data['aprt']));
    $pay            = htmlentities(mysqli_real_escape_string(getConnection(), $data['pay']));
    $comment        = htmlentities(mysqli_real_escape_string(getConnection(), $data['comment']));
    $delivery       = htmlentities(mysqli_real_escape_string(getConnection(), $data['delivery'])) === 'dev-no' ? 'Самовывоз' : 'Доставка';
    $pay            = htmlentities(mysqli_real_escape_string(getConnection(), $data['pay'])) === 'card' ? 'Карта' : 'Наличные';

    if ($delivery === 'Самовывоз') {
        $result = mysqli_query(getConnection(), "INSERT INTO orders (product_id, user_id, delivery_type, payment, price, comment)
                                                VALUES ('$productId', '$user', '$delivery', '$pay', '$price', '$comment')");
    } else {
        if ($price <= $minPrice && $delivery === 'Доставка') {
            $price += $deliveryPrice;
        }
        $result = mysqli_query(getConnection(), "INSERT INTO orders (product_id, user_id, delivery_type, city, street, home, apart, payment, price, comment)
                                                values ('$productId', '$user', '$delivery', '$city', '$street', '$home', '$aprt', '$pay', '$price', '$comment')");
    }

    if ($result) {
        return true;
    } else {
        return false;
    }
}

function getOrders()
{
    $query = mysqli_query(getConnection(), "SELECT o.id, o.price, concat(u.name, ' ', u.surname, ' ', u.thirdname) as user, u.phone, o.delivery_type AS delivery,
                                                    o.payment,o.status, concat('г. ', o.city, ', ул. ', o.street, ', д. ', o.home, ', кв. ', o.apart) AS address,
                                                    o.comment FROM orders AS o
                                            LEFT JOIN products AS p ON o.product_id = p.id
                                            LEFT JOIN users AS u ON o.user_id = u.id
                                            ORDER BY status ASC, created_at DESC");
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

function getCategoriesFromDB() 
{
    $categories = [];

    $query = mysqli_query(getConnection(), "SELECT * FROM categories");

    while ($row = mysqli_fetch_assoc($query)) {
        array_push($categories, [
        'id' => $row['id'],
        'name' => $row['name'],
        'path' => $row['path'],
        ]);
    }
    return $categories;
}



function getCategories($categories)
{
    $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

    if($url === '/' || $url === '/index.php') : ?>
        <li>
        <a class="filter__list-item active" href="/">Все</a>
        </li>
        <?php foreach ($categories as $categ) : ?>
        <li>
            <a class="filter__list-item" href="<?= $categ['path'] ?>"><?= $categ['name'] ?></a>
        </li>
        <?php endforeach; 
        else: ?>
        <li>
            <a class="filter__list-item" href="/">Все</a>
        </li>
        <?php foreach ($categories as $categ) : 
            if ($url === $categ['path']) : ?>
            <li>
                <a class="filter__list-item active" href="<?= $categ['path'] ?>"><?= $categ['name'] ?></a>
            </li>
            <?php else : ?>
                <li>
                <a class="filter__list-item" href="<?= $categ['path'] ?>"><?= $categ['name'] ?></a>
                </li>
            <?php endif; 
        endforeach; 
    endif; 
}



function editProduct($data, $get, $file)
{
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/src/img/products/';
    $fileName = htmlentities(mysqli_real_escape_string(getConnection(), $file['product-photo']['name']));
    $filePath = $file['product-photo']['tmp_name'];
    $productId = htmlentities(mysqli_real_escape_string(getConnection(), $get['edit_id']));
    $photo = mysqli_fetch_assoc(mysqli_query(getConnection(), "SELECT photo FROM products WHERE id = '".$productId."'"))['photo'];
    //Данные из формы (без картинки)
    $name = htmlentities(mysqli_real_escape_string(getConnection(), $data['product-name']));
    $price = htmlentities(mysqli_real_escape_string(getConnection(), $data['product-price']));
    $new = isset($data['new']) && $data['new'] === 'on' ? 1 : 0;
    $sale = isset($data['sale']) && $data['sale'] === 'on' ? 1 : 0;

    // Если картинка с таким названием уже существует, дабвляем к имени _data('U')
    $pathName = pathinfo($fileName, PATHINFO_FILENAME);
    $pathExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $nameWithDate = $pathName . '_' . date('U') . '.' . $pathExtension;
    $uploadFileName = file_exists($uploadDir . $fileName) ? $nameWithDate : $fileName;

    //Для проверки чтобы данного продукта не было в заказах
    $orders = [];
    $ordersQuery = mysqli_fetch_all(mysqli_query(getConnection(), "SELECT id FROM orders WHERE product_id = '$productId' AND status = 'Не обработан'"), MYSQLI_ASSOC);
    if (count($ordersQuery) !== 0) {
        foreach ($ordersQuery as $order) {
            foreach ($order as $o) {
                array_push($orders, $o);
            }
        }
        $orders = implode(', ', $orders);
    }
    
    if (count($orders) === 0) {
        if ($file['product-photo']['size'] === 0) {
            $editProductQuery = mysqli_query(getConnection(), "UPDATE products SET name = '".$name."', price = '".$price."', sale = '".$sale."', new = '".$new."' WHERE id = '".$productId."'");
        } else {
            if (move_uploaded_file($filePath, $uploadDir . $uploadFileName)) {
                deletePhoto($photo);
                $editProductQuery = mysqli_query(getConnection(), "UPDATE products SET name = '".$name."', price = '".$price."', photo = '".$uploadFileName."', sale = '".$sale."', new = '".$new."' WHERE id = '".$productId."'");
            } else {
                return 'Не удалось загрузить файл';
            }
        }
        
        if ($editProductQuery) {
            deleteCategories($productId);
            $chosenCategories = $data['category'];
        
            foreach ($chosenCategories as $category) {
                $categoryQuery = mysqli_query(getConnection(), "SELECT id from categories WHERE name = '".$category."'");
                $categoryId = mysqli_fetch_assoc($categoryQuery)['id'];
        
                mysqli_query(getConnection(), "INSERT into category_product (product_id, category_id) VALUES ('$productId', '$categoryId')");
            }
            return 'Success';
        } else {
            return 'Не удалось загрузить товар в базу данных';
        }
    } else {
        return 'Нельзя изменить товар, он находится в следующих заказах ' . $orders;
    }
}

function addConditionToWhereQuery($where, $condition)
{
    if ($where !== '') {
        $where .= " AND $condition";
    } else {
        $where = $condition;
    }

    return $where;
}

function getProductsList($start, $prodsPerPage)
{
    $limit    = " LIMIT $start, $prodsPerPage";

    $productsCount = mysqli_fetch_row(mysqli_query(getConnection(), "SELECT COUNT(*) FROM products as p"))[0];
    $products = mysqli_fetch_all(mysqli_query(getConnection(), "SELECT p.id, p.name, p.price, p.new, if (COUNT(p.id) > 1, group_concat(distinct c.name order by c.name ASC SEPARATOR ', '), c.name) AS category
                                                                FROM category_product AS cp
                                                                JOIN products AS p ON cp.product_id = p.id
                                                                JOIN categories AS c ON cp.category_id = c.id
                                                                GROUP BY p.id ORDER BY id DESC" . $limit), MYSQLI_ASSOC);

    return ['products' => $products, 'count' => $productsCount];
}

function getFilteredProducts($get, $start, $prodsPerPage)
{
    if (!empty($get['title_url'])) {
        $url = parse_url($get['title_url'], PHP_URL_PATH);
    } else {
        $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    }

    $where    = '';
    $orderBy  = '';
    $limit    = " LIMIT $start, $prodsPerPage";

    if (!empty($get)) {
        if (isset($get['sale']) && $get['sale'] === 'on') $where = addConditionToWhereQuery($where, "p.sale = 1");
        if (isset($get['new']) && $get['new'] === 'on') $where = addConditionToWhereQuery($where, "p.new = 1");
        if (isset($get['min'])) $where = addConditionToWhereQuery($where, "p.price >= " . $get['min']);
        if (isset($get['max'])) $where = addConditionToWhereQuery($where, "p.price <= " . $get['max']);

        if(isset($get['sorting'])) {
            if ($get['sorting'] === 'name_asc') $orderBy = "p.name ASC";
            if ($get['sorting'] === 'name_desc') $orderBy = "p.name DESC";
            if ($get['sorting'] === 'price_asc') $orderBy = "p.price ASC";
            if ($get['sorting'] === 'price_desc') $orderBy = "p.price DESC";
        }

        $orderBy = !empty($orderBy) ? " ORDER BY $orderBy" : '';
    }

    if ($url === '/' || $url === '/index.php') {
        $where = !empty($where) ? " WHERE $where" : '';
        $productsCount = mysqli_fetch_row(mysqli_query(getConnection(), "SELECT COUNT(*) FROM products as p" . $where))[0];
        $productsQuery = mysqli_query(getConnection(), "SELECT * FROM products AS p" . $where . $orderBy . $limit);
        $products = mysqli_fetch_all($productsQuery, MYSQLI_ASSOC);
    } else {
        $where = !empty($where) ? " WHERE path = '$url' AND $where" : " WHERE path = '$url'";
        $productsCount = mysqli_fetch_row(mysqli_query(getConnection(), "SELECT COUNT(*) FROM category_product AS c_p
                                                                        LEFT JOIN categories AS c ON c.id = c_p.category_id
                                                                        LEFT JOIN products AS p ON p.id = c_p.product_id"
                                                                        . $where))[0];

        $productsQuery = mysqli_query(getConnection(), "SELECT c_p.product_id as id, p.name, p.price, p.photo, p.sale, p.new, p.count FROM category_product AS c_p
                                                        LEFT JOIN categories AS c ON c.id = c_p.category_id
                                                        LEFT JOIN products AS p ON p.id = c_p.product_id
                                                        ". $where . $orderBy . $limit);
        $products = mysqli_fetch_all($productsQuery, MYSQLI_ASSOC);
    }

    return ['products' => $products, 'count' => $productsCount];
}
function getUserAuth($data)
{
    $email = htmlentities(mysqli_real_escape_string(getConnection(), $data['email']));
    $password = htmlentities(mysqli_real_escape_string(getConnection(), $data['password']));

    $query = mysqli_query(getConnection(), "SELECT u.id, u.password, g.id AS group_id FROM group_user AS g_u
                                            LEFT JOIN users AS u ON g_u.user_id = u.id
                                            LEFT JOIN groups AS g ON g_u.group_id = g.id 
                                            WHERE login = '$email'");
    $user = mysqli_fetch_assoc($query);

    if (count($user) === 0) {
        echo 'Пользователь не найден';
    } else {
        if (!password_verify($password, $user['password'])) {
            echo 'Неверный пароль';
        } else {
            if($user['group_id'] === '1') {
                $_SESSION['ut'] = 'admin';
                echo 'Ok';
            } elseif ($user['group_id'] === '2') {
                $_SESSION['ut'] = 'operator';
                echo 'Ok';
            }
        }
    }
}

function userLogout($data)
{
  if ($data['logout'] === 'yes') {
    $_SESSION = array();
    session_destroy();
    echo 'Ok';
  } else {
    echo 'Что-то пошло не по плану!';
  }
}

function changeStatus($data)
{
    $orderId = htmlentities(mysqli_real_escape_string(getConnection(), $data['id']));
    $status = htmlentities(mysqli_real_escape_string(getConnection(), $data['change']));

    if ($status === '1') {
        $query = mysqli_query(getConnection(), "UPDATE orders SET status = 'Обработан' WHERE id = '$orderId' ");
    } elseif ($status === '0') {
        $query = mysqli_query(getConnection(), "UPDATE orders SET status = 'Не обработан' WHERE id = '$orderId' ");
    } 

    if ($query) {
        echo 'Ok';
    } else {
        echo 'Что-то пошло не так';
    }
}

function num_word($number, $after) 
{
	$cases = array (2, 0, 1, 1, 1, 2);
	echo $after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
}

function getNavMenu()
{
    if(!empty($_SESSION['ut'])) : ?>
        <?php if($_SESSION['ut'] === 'admin') : ?>
        <li>
            <a class="main-menu__item" href="/">Главная</a>
        </li>
        <li>
            <a class="main-menu__item" href="/admin/products/">Товары</a>
        </li>
        <li>
            <a class="main-menu__item" href="/admin/orders/">Заказы</a>
        </li>
        <li>
            <a class="main-menu__item js-logout-button" href="/">Выйти</a>
        </li>
        <?php elseif($_SESSION['ut'] === 'operator') : ?>
        <li>
            <a class="main-menu__item" href="/">Главная</a>
        </li>
        <li>
            <a class="main-menu__item" href="/admin/orders/">Заказы</a>
        </li>
        <li>
            <a class="main-menu__item js-logout-button" href="/">Выйти</a>
        </li>
        <?php endif; ?>
    <?php else : ?>
        <li>
            <a class="main-menu__item" href="/">Главная</a>
        </li>
        <li>
            <a class="main-menu__item" href="/?new=on">Новинки</a>
        </li>
        <li>
            <a class="main-menu__item" href="/?sale=on">Sale</a>
        </li>
        <li>
            <a class="main-menu__item" href="/routes/delivery/">Доставка</a>
        </li>
    <? endif;
} 