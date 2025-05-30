-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 30 2025 г., 20:11
-- Версия сервера: 5.7.39
-- Версия PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `studmarket`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cooperation_requests`
--

CREATE TABLE `cooperation_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `type` enum('employer','college','other') NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','processed','rejected') NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `like_currect` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `review_id`, `is_active`, `like_currect`, `created_at`) VALUES
(4, 1, 4, 0, '24', '2025-05-21 07:24:39'),
(9, 2, 9, 0, '18', '2025-05-19 16:28:30'),
(10, 3, 10, 0, '12', '2025-05-17 11:25:37'),
(11, 7, 10, 0, '0', '2025-05-29 16:33:50');

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio`
--

CREATE TABLE `portfolio` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `external_links` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `portfolio`
--

INSERT INTO `portfolio` (`id`, `user_id`, `title`, `category_id`, `description`, `tags`, `image_path`, `external_links`, `created_at`) VALUES
(1, 4, 'Веб-сайт для автосервиса', 2, ' Разработка адаптивного сайта с системой онлайн-записи. Использованы: HTML5, CSS3, JavaScript.', '#веб-разработка, #лэндинг, #автосервис', '../uploads/portfolio/work_4_1748346459.jfif', NULL, '2025-05-12 11:47:39'),
(2, 5, 'Логотип для пекарни \"Ржаной\"', 1, 'Создание узнаваемого логотипа в народном стиле с элементами пшеничных колосьев.', '#логотип, #хлеб, #традиции, #брендинг', '../uploads/portfolio/work_7_1748346573.jfif', NULL, '2025-05-15 11:49:33'),
(3, 6, 'Фирменный стиль для кафе \"Утро\"', 1, 'Создание фирменного стиля для уютного кафе с акцентом на утреннюю атмосферу.', '#логотип, #брендинг, #кафе, #упаковка', '../uploads/portfolio/work_8_1748349086.jfif', NULL, '2025-05-27 12:31:26'),
(4, 7, 'Логотип и айдентика для кофейни \"Morning Brew\"', 1, 'Полный редизайн айдентики для сети кофеен премиум-класса. Концепция объединяет образ утреннего солнца и кофейного зерна, передавая атмосферу свежести и пробуждения.', '#логотип, #брендинг, #кофе, #упаковка', '../uploads/portfolio/work_11_1748447879.jpg', NULL, '2025-05-28 15:57:59');

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio_categories`
--

CREATE TABLE `portfolio_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `portfolio_categories`
--

INSERT INTO `portfolio_categories` (`id`, `name`) VALUES
(1, 'Дизайн'),
(2, 'IT'),
(3, 'Ювелиры'),
(4, 'Игры'),
(5, 'Маркетинг'),
(6, 'Фотография'),
(7, 'Видео'),
(8, '3D моделирование');

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio_likes`
--

CREATE TABLE `portfolio_likes` (
  `id` int(11) NOT NULL,
  `work_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `liked_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `portfolio_likes`
--

INSERT INTO `portfolio_likes` (`id`, `work_id`, `user_id`, `liked_at`) VALUES
(1, 1, 4, '2025-05-27 11:47:49'),
(2, 1, 5, '2025-05-27 11:52:07'),
(3, 3, 6, '2025-05-27 12:31:34'),
(4, 4, 7, '2025-05-28 15:58:17');

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio_views`
--

CREATE TABLE `portfolio_views` (
  `id` int(11) NOT NULL,
  `work_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `portfolio_views`
--

INSERT INTO `portfolio_views` (`id`, `work_id`, `user_id`, `viewed_at`) VALUES
(1, 1, 4, '2025-05-27 11:47:39'),
(2, 1, 5, '2025-05-27 11:48:47'),
(3, 2, 5, '2025-05-27 11:49:33'),
(4, 1, 6, '2025-05-27 12:19:53'),
(5, 2, 6, '2025-05-27 12:21:00'),
(6, 3, 6, '2025-05-27 12:21:29'),
(27, 3, 7, '2025-05-28 15:24:52'),
(28, 2, 7, '2025-05-28 15:24:52'),
(29, 1, 7, '2025-05-28 15:24:52'),
(30, 4, 7, '2025-05-28 15:57:59');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `rating`, `text`, `created_at`) VALUES
(4, 1, 5, 'Благодаря СтудМаркету я получила стажировку в крупной компании уже на 3 курсе. Очень удобная платформа, где можно показать свои работы и сразу получить отклик от работодателей.', '2025-05-21 07:14:42'),
(9, 2, 4, 'За последний год нашли через платформу 3 отличных стажера. Особенно ценно, что можно сразу увидеть реальные работы студентов, а не только сухие резюме. Экономит массу времени!', '2025-05-19 16:26:34'),
(10, 3, 5, 'Платформа помогла мне найти первых клиентов на фрилансе еще во время учебы. Теперь у меня есть портфолио и опыт, которые помогут устроиться на работу после выпуска.', '2025-05-17 10:58:33');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` enum('student','employer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'img/no-image.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `phone`, `created_at`, `avatar_path`) VALUES
(1, 'Анна К.', 'email1@gmail.com', '', 'student', NULL, NULL, 'uploads/avatars/avatar1.png'),
(2, 'TechSolutions Inc.', 'email2@gmail.com', '', 'employer', NULL, NULL, 'uploads/avatars/avatar2.png'),
(3, 'Иван П.', 'email3@gmail.com', '', 'student', NULL, NULL, 'uploads/avatars/avatar3.png'),
(4, 'Илья Р.', 'email4@gmail.com', '', 'student', NULL, NULL, NULL),
(5, 'DesignPro Studio', 'email5@gmail.com', '', 'employer', NULL, NULL, NULL),
(6, 'Максим А.', 'email6@gmail.com', '', 'student', NULL, NULL, NULL),
(7, 'Тестовый аккаунт', 'account404@gmail.com', '$2y$10$DQfQW.kpiRs3yZ.Cm.q52OvZ9i0ZtBvPpDLtxTyz19NvhPTYRNSm.', 'student', '+79115678934', '2025-05-28 15:24:43', 'img/no-image.png');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cooperation_requests`
--
ALTER TABLE `cooperation_requests`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`review_id`),
  ADD KEY `review_id` (`review_id`);

--
-- Индексы таблицы `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `portfolio_categories`
--
ALTER TABLE `portfolio_categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `portfolio_likes`
--
ALTER TABLE `portfolio_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_user` (`work_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `portfolio_views`
--
ALTER TABLE `portfolio_views`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_user` (`work_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cooperation_requests`
--
ALTER TABLE `cooperation_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `portfolio_categories`
--
ALTER TABLE `portfolio_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `portfolio_likes`
--
ALTER TABLE `portfolio_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `portfolio_views`
--
ALTER TABLE `portfolio_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`);

--
-- Ограничения внешнего ключа таблицы `portfolio`
--
ALTER TABLE `portfolio`
  ADD CONSTRAINT `portfolio_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `portfolio_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `portfolio_categories` (`id`);

--
-- Ограничения внешнего ключа таблицы `portfolio_likes`
--
ALTER TABLE `portfolio_likes`
  ADD CONSTRAINT `portfolio_likes_ibfk_1` FOREIGN KEY (`work_id`) REFERENCES `portfolio` (`id`),
  ADD CONSTRAINT `portfolio_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `portfolio_views`
--
ALTER TABLE `portfolio_views`
  ADD CONSTRAINT `portfolio_views_ibfk_1` FOREIGN KEY (`work_id`) REFERENCES `portfolio` (`id`),
  ADD CONSTRAINT `portfolio_views_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
