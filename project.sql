-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 24 2020 г., 13:24
-- Версия сервера: 10.3.22-MariaDB
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `project`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `path` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `path`) VALUES
(1, 'Женщины', '/category/woman/'),
(2, 'Мужчины', '/category/men/'),
(3, 'Дети', '/category/children/'),
(4, 'Аксессуары', '/category/accessories/');

-- --------------------------------------------------------

--
-- Структура таблицы `category_product`
--

CREATE TABLE `category_product` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `category_product`
--

INSERT INTO `category_product` (`product_id`, `category_id`) VALUES
(124, 1),
(125, 1),
(126, 4),
(127, 1),
(127, 2),
(128, 4),
(129, 1),
(130, 4),
(131, 1),
(132, 2),
(133, 4),
(134, 4),
(135, 2),
(135, 4),
(136, 3),
(137, 3),
(138, 1),
(138, 3),
(139, 2),
(139, 3),
(140, 2),
(141, 2),
(141, 4),
(142, 1),
(142, 2),
(142, 3),
(142, 4),
(143, 1),
(143, 4),
(144, 1),
(144, 4),
(145, 2),
(146, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `name`) VALUES
(1, 'Администратор'),
(2, 'Оператор'),
(3, 'Пользователь');

-- --------------------------------------------------------

--
-- Структура таблицы `group_user`
--

CREATE TABLE `group_user` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `group_user`
--

INSERT INTO `group_user` (`user_id`, `group_id`) VALUES
(17, 1),
(18, 2),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `delivery_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apart` int(11) DEFAULT NULL,
  `payment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Не обработан'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `product_id`, `user_id`, `delivery_type`, `city`, `street`, `home`, `apart`, `payment`, `price`, `comment`, `created_at`, `status`) VALUES
(64, 125, 23, 'Доставка', 'Город', 'Улица', '12', 12, 'Наличные', 5500, '', '2020-12-24 09:34:53', 'Не обработан'),
(65, 124, 24, 'Самовывоз', NULL, NULL, NULL, NULL, 'Наличные', 4000, 'Уже в пути!!!', '2020-12-24 09:35:52', 'Не обработан');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale` tinyint(1) DEFAULT NULL,
  `new` tinyint(1) DEFAULT NULL,
  `count` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `photo`, `sale`, `new`, `count`) VALUES
(124, 'А товар', '4000.00', 'product-1.jpg', 1, 0, 1),
(125, 'Д товар', '5500.00', 'product-2.jpg', 1, 1, 1),
(126, 'И товар', '2000.00', 'product-3.jpg', 1, 0, 1),
(127, 'Л товар', '1500.00', 'product-4.jpg', 0, 1, 1),
(128, 'Сумка', '1500.00', 'product-5.jpg', 0, 0, 1),
(129, 'Платье', '11699.00', 'product-6.jpg', 0, 1, 1),
(130, 'Сумка', '8000.00', 'product-7.jpg', 0, 0, 1),
(131, 'Ботинок', '15000.00', 'product-9.jpg', 0, 0, 1),
(132, 'Мужской ботинок', '4500.00', '5b4e839f1a96d5f02e4d67dc6b81b7f6.jpg', 0, 1, 1),
(133, 'Перчатки', '3000.00', '9011880-1.jpg', 1, 1, 1),
(134, 'Очки Palaroid', '3900.00', '5281094-1.jpg', 0, 1, 1),
(135, 'Ray-Ban', '8999.00', '4577696-1.jpg', 1, 1, 1),
(136, 'Комбинезон детский', '4999.00', '13158488-1.jpg', 0, 1, 1),
(137, 'Ботинки детские', '3500.00', '13231800-1.jpg', 0, 0, 1),
(138, 'Куртка детская', '6000.00', '12525319-1.jpg', 0, 0, 1),
(139, 'Куртка детская', '5000.00', '12525329-1.jpg', 0, 0, 1),
(140, 'Пуленепробиваемые шорты', '20000.00', '80541a3b3cfb86e7f1d80142d8f2354a.jpg', 0, 0, 1),
(141, 'Зонт самурая', '5000.00', '3048618-1.jpg', 0, 1, 1),
(142, 'LGBT зонт', '3000.00', '15202804-1.jpg', 0, 1, 1),
(143, 'Дары смерти', '10000.00', '11857160-1.jpg', 1, 1, 1),
(144, 'Серьги', '1900.00', '11139194-1.jpg', 0, 1, 1),
(145, 'Джинсы', '4000.00', '10471591-1.jpg', 1, 1, 1),
(146, 'Худи', '7000.00', '15952114-4.jpg', 0, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thirdname` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`, `surname`, `thirdname`, `phone`) VALUES
(17, 'admin@mail.ru', '$2y$10$mTR1Qn4WLRzVtJztlXbsnO1svXVt1lVaNBC.A1w58GtUSjjtsag4.', 'Admin', 'Admin', 'Admin', '+7-999-999-99-99'),
(18, 'operator@mail.ru', '$2y$10$JLpVNPloiDMnP2cUXoxdFezWkyY6w6Poe6IGuzPzIu68P781GEl0y', 'Operator', 'Operator', 'Operator', '+7-111-111-11-11'),
(21, '123@mail.ru', NULL, 'Henry Ford', 'Nik', 'asd', '+7(123)-123-12-31'),
(22, 'laze24@mail.ru', NULL, 'Henry Ford', 'Nik', 'asd', '+7(123)-123-12-31'),
(23, '123123@mail.ru', NULL, 'Имя', 'Фамилия', 'Отчество', '+7(123)-123-12-33'),
(24, 'ivanon@mail.ru', NULL, 'Иван', 'Иванов', 'Иванович', '+7(123)-123-12-31'),
(25, '2312312@mail.ru', NULL, 'Henry Ford', 'Nik', 'asd', '+7(123)-123-12-31');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `category_product`
--
ALTER TABLE `category_product`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `category_product_category_id` (`category_id`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `group_user`
--
ALTER TABLE `group_user`
  ADD PRIMARY KEY (`user_id`,`group_id`),
  ADD KEY `group_user_group_id` (`group_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `category_product`
--
ALTER TABLE `category_product`
  ADD CONSTRAINT `category_product_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `category_product_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ограничения внешнего ключа таблицы `group_user`
--
ALTER TABLE `group_user`
  ADD CONSTRAINT `group_user_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `group_user_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
