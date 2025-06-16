-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 16 2025 г., 18:32
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
-- Структура таблицы `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('superadmin','admin','moderator') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moderator',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `name`, `role`, `created_at`, `last_login`) VALUES
(3, 'account505@gmail.com', '$2y$10$QTAFDeo3g.hroPrnhSXzu.QauB5GBH/G6RaHIoUYmt/BkI1LiLeTW', 'Админ', 'superadmin', '2025-06-16 15:23:50', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `admin_actions`
--

CREATE TABLE `admin_actions` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `admin_actions`
--

INSERT INTO `admin_actions` (`id`, `admin_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(2, 3, 'Вход в систему', 'Успешный вход', '127.0.0.1', '2025-06-16 15:32:08'),
(3, 3, 'Выход из системы', 'Успешный выход', '127.0.0.1', '2025-06-16 15:32:28');

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
(10, 3, 10, 0, '12', '2025-05-17 11:25:37');

-- --------------------------------------------------------

--
-- Структура таблицы `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `moderator_comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `portfolio`
--

INSERT INTO `portfolio` (`id`, `user_id`, `title`, `category_id`, `description`, `tags`, `image_path`, `external_links`, `created_at`, `status`, `moderator_comment`) VALUES
(1, 4, 'Веб-сайт для автосервиса', 2, ' Разработка адаптивного сайта с системой онлайн-записи. Использованы: HTML5, CSS3, JavaScript.', '#веб-разработка, #лэндинг, #автосервис', '../uploads/portfolio/work_4_1748346459.jfif', NULL, '2025-05-12 11:47:39', 'pending', NULL),
(2, 5, 'Логотип для пекарни \"Ржаной\"', 1, 'Создание узнаваемого логотипа в народном стиле с элементами пшеничных колосьев.', '#логотип, #хлеб, #традиции, #брендинг', '../uploads/portfolio/work_7_1748346573.jfif', NULL, '2025-05-15 11:49:33', 'pending', NULL),
(3, 6, 'Фирменный стиль для кафе \"Утро\"', 1, 'Создание фирменного стиля для уютного кафе с акцентом на утреннюю атмосферу.', '#логотип, #брендинг, #кафе, #упаковка', '../uploads/portfolio/work_8_1748349086.jfif', NULL, '2025-05-27 12:31:26', 'pending', NULL),
(4, 7, 'Логотип и айдентика для кофейни \"Morning Brew\"', 1, 'Полный редизайн айдентики для сети кофеен премиум-класса. Концепция объединяет образ утреннего солнца и кофейного зерна, передавая атмосферу свежести и пробуждения.', '#логотип, #брендинг, #кофе, #упаковка', '../uploads/portfolio/work_11_1748447879.jpg', NULL, '2025-05-28 15:57:59', 'pending', NULL);

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
(30, 4, 7, '2025-05-28 15:57:59'),
(31, 4, 8, '2025-06-06 15:28:23'),
(32, 3, 8, '2025-06-06 15:28:23'),
(33, 2, 8, '2025-06-06 15:28:23'),
(34, 1, 8, '2025-06-06 15:28:23'),
(35, 4, 13, '2025-06-16 10:49:13'),
(36, 3, 13, '2025-06-16 10:49:13'),
(37, 2, 13, '2025-06-16 10:49:13'),
(38, 1, 13, '2025-06-16 10:49:13'),
(39, 4, 15, '2025-06-16 13:56:57'),
(40, 3, 15, '2025-06-16 13:56:57'),
(41, 2, 15, '2025-06-16 13:56:57'),
(42, 1, 15, '2025-06-16 13:56:57');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `moderator_comment` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `rating`, `text`, `created_at`, `status`, `moderator_comment`) VALUES
(4, 1, 5, 'Благодаря СтудМаркету я получила стажировку в крупной компании уже на 3 курсе. Очень удобная платформа, где можно показать свои работы и сразу получить отклик от работодателей.', '2025-05-21 07:14:42', 'pending', NULL),
(9, 2, 4, 'За последний год нашли через платформу 3 отличных стажера. Особенно ценно, что можно сразу увидеть реальные работы студентов, а не только сухие резюме. Экономит массу времени!', '2025-05-19 16:26:34', 'pending', NULL),
(10, 3, 5, 'Платформа помогла мне найти первых клиентов на фрилансе еще во время учебы. Теперь у меня есть портфолио и опыт, которые помогут устроиться на работу после выпуска.', '2025-05-17 10:58:33', 'pending', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'img/no-image.png',
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  `ban_reason` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `phone`, `created_at`, `avatar_path`, `is_banned`, `ban_reason`) VALUES
(1, 'Анна К.', 'email1@gmail.com', '', 'student', NULL, NULL, 'uploads/avatars/avatar1.png', 0, NULL),
(2, 'TechSolutions Inc.', 'email2@gmail.com', '', 'employer', NULL, NULL, 'uploads/avatars/avatar2.png', 0, NULL),
(3, 'Иван П.', 'email3@gmail.com', '', 'student', NULL, NULL, 'uploads/avatars/avatar3.png', 0, NULL),
(4, 'Илья Р.', 'email4@gmail.com', '', 'student', NULL, NULL, 'img/no-image.png', 0, NULL),
(5, 'Александр Я.', 'email5@gmail.com', '', 'student', NULL, NULL, 'img/no-image.png', 0, NULL),
(6, 'Максим А.', 'email6@gmail.com', '', 'student', NULL, NULL, 'img/no-image.png', 0, NULL),
(7, 'Тестовый аккаунт', 'account404@gmail.com', '$2y$10$P0y/B9YqB57bA7GRkoS/W.vY8Cjww3HYy9esUgPvuqJ3vy/.RXw2i', 'student', '+79115678934', '2025-05-28 15:24:43', 'img/no-image.png', 0, NULL),
(8, 'Тест-Работодатель', 'account202@gmail.com', '$2y$10$StXzsLp6zorahiMjTv92Ke6Exge/yOvkexTz7J4kuSfLtI5n9xxTi', 'employer', '+79118954238', '2025-06-06 14:45:33', 'img/no-image.png', 0, NULL),
(13, 'DataLabs', 'email7@gmail.com', '', 'employer', '+79118654736', NULL, 'img/no-image.png', 0, NULL),
(15, 'WebInnovations', 'email8@gmail.com', '', 'employer', '+79115957623', NULL, 'img/no-image.png', 0, NULL),
(16, 'DesignHub', 'email9@gmail.com', '', 'employer', '+79114368769', NULL, 'img/no-image.png', 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `vacancies`
--

CREATE TABLE `vacancies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `employment_type` enum('full','part','internship','remote') NOT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `requirements` varchar(255) DEFAULT NULL,
  `benefits` varchar(255) DEFAULT NULL,
  `contacts` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `moderator_comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `vacancies`
--

INSERT INTO `vacancies` (`id`, `user_id`, `title`, `category_id`, `employment_type`, `salary`, `location`, `description`, `requirements`, `benefits`, `contacts`, `image_path`, `created_at`, `status`, `moderator_comment`) VALUES
(1, 13, 'Стажировка в IT-компании', 3, 'internship', '30000-40000', 'Калининград / Удалённо', 'Мы ищем студентов-разработчиков для участия в проекте по созданию API для fintech-стартапа. Работа в команде с ментором, гибкий график, возможность трудоустройства.', '· Базовые знания Python/Java/Golang;\\r\\n· Понимание REST API и баз данных (SQL/NoSQL);\\r\\n· Готовность работать 20–30 часов в неделю.', '', 'Отправлять резюме на: hr@techcompany.ru с темой «Стажировка Backend».\\r\\nТелефон: +7 (495) 123-45-67 (Анна).', '../uploads/vacancies/vacancy_13_1750071399.png', '2025-06-16 10:56:39', 'pending', NULL),
(5, 15, 'Маркетолог в Digital-агентство', 1, 'full', '60000-80000', 'Калиниград', 'Разработка и реализация маркетинговых стратегий для клиентов (SMM, контекстная реклама, email-рассылки). Анализ эффективности кампаний.', '· Опыт в digital-маркетинге от 1 года;\\n· Знание Google Ads, Meta Business Suite, Яндекс.Метрики;\\n· Умение работать с CRM (AmoCRM, Bitrix24).', '', 'Отправлять резюме на: hr@digital-agency.ru с темой «Маркетолог».\\nТелефон: +7 (495) 111-22-33 (Ольга).', '../uploads/vacancies/vacancy_15_1750072569.png', '2025-06-16 11:16:09', 'pending', NULL),
(6, 16, 'Менеджер проектов (стажёр)', 5, 'internship', '40000-50000', 'Калиниград', 'Координация работы команд, контроль сроков, ведение документации в Jira/Notion. Обучение у Senior PM.', '· Обучаетесь на менеджмента/IT;\\r\\n· Организованность и multitasking;\\r\\n· Знание основ Agile/Waterfall.', '· ДМС + компенсация спортзала;\\r\\n· Гибкий график;\\r\\n· Бонусы за успешные проекты.', 'Отправлять резюме на: pm-intern@company.com + мотивационное письмо.', '../uploads/vacancies/vacancy_16_1750084654.png', '2025-06-16 14:37:34', 'pending', NULL),
(7, 16, 'Разработчик Python (Junior)', 3, 'full', '80000-120000', 'Калиниград', 'Разработка backend-части веб-приложений (Django/Flask). Участие в код-ревью, работа в Scrum-команде.', '· Опыт с Python от 6 месяцев;\\r\\n· Знание SQL (PostgreSQL), Git;\\r\\n· Понимание REST API.', '', 'dev@tech.ru (ссылку на GitHub/GitLab).', '../uploads/vacancies/vacancy_16_1750084871.png', '2025-06-16 14:41:11', 'pending', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `vacancy_applications`
--

CREATE TABLE `vacancy_applications` (
  `id` int(11) NOT NULL,
  `vacancy_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','reviewed','rejected','accepted') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vacancy_categories`
--

CREATE TABLE `vacancy_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `vacancy_categories`
--

INSERT INTO `vacancy_categories` (`id`, `name`) VALUES
(1, 'Маркетинг'),
(2, 'Дизайн'),
(3, 'IT'),
(4, 'Финансы'),
(5, 'Управление');

-- --------------------------------------------------------

--
-- Структура таблицы `vacancy_views`
--

CREATE TABLE `vacancy_views` (
  `id` int(11) NOT NULL,
  `vacancy_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `vacancy_views`
--

INSERT INTO `vacancy_views` (`id`, `vacancy_id`, `user_id`, `viewed_at`) VALUES
(1, 1, 13, '2025-06-16 10:57:05'),
(5, 1, 15, '2025-06-16 11:14:39'),
(6, 5, 15, '2025-06-16 11:16:18'),
(7, 5, 16, '2025-06-16 14:32:25'),
(8, 6, 16, '2025-06-16 14:37:37'),
(9, 7, 16, '2025-06-16 14:41:15');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `admin_actions`
--
ALTER TABLE `admin_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

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
-- Индексы таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- Индексы таблицы `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `vacancies`
--
ALTER TABLE `vacancies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `vacancy_applications`
--
ALTER TABLE `vacancy_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vacancy_id` (`vacancy_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `vacancy_categories`
--
ALTER TABLE `vacancy_categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `vacancy_views`
--
ALTER TABLE `vacancy_views`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vacancy_user` (`vacancy_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `admin_actions`
--
ALTER TABLE `admin_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `cooperation_requests`
--
ALTER TABLE `cooperation_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `vacancies`
--
ALTER TABLE `vacancies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `vacancy_applications`
--
ALTER TABLE `vacancy_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vacancy_categories`
--
ALTER TABLE `vacancy_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `vacancy_views`
--
ALTER TABLE `vacancy_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `admin_actions`
--
ALTER TABLE `admin_actions`
  ADD CONSTRAINT `admin_actions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`);

--
-- Ограничения внешнего ключа таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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

--
-- Ограничения внешнего ключа таблицы `vacancies`
--
ALTER TABLE `vacancies`
  ADD CONSTRAINT `vacancies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `vacancies_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `vacancy_categories` (`id`);

--
-- Ограничения внешнего ключа таблицы `vacancy_applications`
--
ALTER TABLE `vacancy_applications`
  ADD CONSTRAINT `vacancy_applications_ibfk_1` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies` (`id`),
  ADD CONSTRAINT `vacancy_applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `vacancy_views`
--
ALTER TABLE `vacancy_views`
  ADD CONSTRAINT `vacancy_views_ibfk_1` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies` (`id`),
  ADD CONSTRAINT `vacancy_views_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
