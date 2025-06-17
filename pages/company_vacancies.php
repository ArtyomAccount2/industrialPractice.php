<?php
session_start();
require_once("../config/link.php");

if (!isset($_GET['company_id'])) 
{
    header("Location: vacancies.php");
    exit();
}

$company_id = (int)$_GET['company_id'];
$isLoggedIn = isset($_SESSION['user_id']);

if (isset($_SERVER['HTTP_REFERER'])) 
{
    if (strpos($_SERVER['HTTP_REFERER'], 'vacancy_details.php') !== false) 
    {
        $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
    } 
    else if (!isset($_SESSION['previous_page'])) 
    {
        $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
    }
} 
else if (!isset($_SESSION['previous_page'])) 
{

    $_SESSION['previous_page'] = 'vacancies.php';
}

$company_sql = "SELECT * FROM `users` WHERE `id` = $company_id";
$company_result = mysqli_query($conn, $company_sql);
$company = mysqli_fetch_assoc($company_result);

if (!$company) 
{
    header("Location: vacancies.php");
    exit();
}

$vacancies_sql = "SELECT v.*, c.name as category_name FROM vacancies v JOIN vacancy_categories c ON v.category_id = c.id WHERE v.user_id = $company_id ORDER BY v.created_at DESC";
$vacancies_result = mysqli_query($conn, $vacancies_sql);

$page_title = "Вакансии компании " . htmlspecialchars($company['name']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="company_vacancies.php">
                <img class="logo" src="../img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $_SESSION['previous_page'] ?>">Назад</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="company_vacancies.php?company_id=<?= $vacancy['user_id'] ?>">Все вакансии компании</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../files/logout.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Выйти
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="company-vacancies py-5">
        <div class="container">
            <div class="company-header text-center mb-5">
                <h2 class="display-5 fw-bold">Вакансии компании: <?= htmlspecialchars($company['name']) ?></h2>
                <div class="divider mx-auto"></div>
                <div class="company-info mt-4">
                    <?php 
                    if (!empty($company['description']))
                    {
                    ?>
                        <p class="lead"><?= htmlspecialchars($company['description']) ?></p>
                    <?php 
                    } 
                    ?>
                    <div class="company-contacts mt-3">
                        <?php 
                        if (!empty($company['email']))
                        {
                        ?>
                            <p><i class="bi bi-envelope"></i> Email: <?= htmlspecialchars($company['email']) ?></p>
                        <?php 
                        } 
                        ?>
                        <?php 
                        if (!empty($company['phone']))
                        {
                        ?>
                            <p><i class="bi bi-telephone"></i> Телефон: <?= htmlspecialchars($company['phone']) ?></p>
                        <?php 
                        } 
                        ?>
                    </div>
                </div>
            </div> 
            <div class="row g-4">
                <?php 
                if (mysqli_num_rows($vacancies_result) > 0)
                { 
                ?>
                    <?php 
                    while ($vacancy = mysqli_fetch_assoc($vacancies_result))
                    {
                    ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="<?= htmlspecialchars($vacancy['image_path'] ?: '../img/no-image.png') ?>" 
                                    class="card-img-top portfolio-img" 
                                    alt="<?= htmlspecialchars($vacancy['title']) ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary"><?= htmlspecialchars($vacancy['category_name']) ?></span>
                                        <p class="text-muted small border-0"><?= date('d.m.Y', strtotime($vacancy['created_at'])) ?></p>
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($vacancy['title']) ?></h5>
                                    <div class="vacancy-meta mb-3">
                                        <p class="mb-1">
                                            <i class="bi bi-geo-alt"></i> 
                                            <?= htmlspecialchars($vacancy['location'] ?: 'Не указано') ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-cash-coin"></i> 
                                            <?= $vacancy['salary'] ? htmlspecialchars($vacancy['salary']) . ' ₽' : 'По договорённости' ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-clock"></i> 
                                            <?php 
                                            switch($vacancy['employment_type']) 
                                            {
                                                case 'full': echo 'Полная занятость'; break;
                                                case 'part': echo 'Частичная занятость'; break;
                                                case 'internship': echo 'Стажировка'; break;
                                                case 'remote': echo 'Удалённая работа'; break;
                                                default: echo 'Не указано';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <p class="card-text">
                                        <?= mb_substr(htmlspecialchars($vacancy['description']), 0, 100) ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <a href="vacancy_details.php?id=<?= $vacancy['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            Подробнее
                                        </a>
                                        <?php 
                                        if ($isLoggedIn && $_SESSION['user_type'] == 'student')
                                        {
                                        ?>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" 
                                                    data-bs-target="#applyModal<?= $vacancy['id'] ?>">
                                                Откликнуться
                                            </button>
                                        <?php 
                                        } 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                    } 
                    ?>
                <?php 
                } 
                ?>
            </div>
        </div>
    </section>
</div>

<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="mb-3">СтудМаркет</h5>
                <p>Специализированная платформа для студентов Колледжа предпринимательства, где можно демонстрировать работы, находить вакансии и взаимодействовать с работодателями.</p>
                <div class="social-icons">
                    <a href="https://vk.com/studmarket39" class="text-white me-2"><i class="bi bi-people-fill"></i></a>
                    <a href="https://t.me/StudMarket_bot" class="text-white"><i class="bi bi-telegram"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4 mb-md-0">
                <h5 class="navigation mb-3">Навигация</h5>
                <ul class="list-navigation list-unstyled">
                    <li class="mb-2"><a href="<?= $_SESSION['previous_page'] ?>" class="text-white text-decoration-none">Назад</a></li>
                    <li class="mb-2"><a href="company_vacancies.php?company_id=<?= $vacancy['user_id'] ?>" class="text-white text-decoration-none">Все вакансии компании</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <h5 class="mb-3">Контакты</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> г. Калининград, ул.Брамса, д.9</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i> gaukokp@mail.ru</li>
                    <li><i class="bi bi-telephone me-2"></i> +7 (4012) 95-77-75</li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5 class="mb-3">Подписаться</h5>
                <p>Будьте в курсе новых возможностей</p>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Ваш email">
                    <button class="btn btn-primary" type="button">OK</button>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">© 2025 СтудМаркет. Все права защищены.</p>
        </div>
    </div>
</footer>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>