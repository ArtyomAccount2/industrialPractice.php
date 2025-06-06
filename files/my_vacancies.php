<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') 
{
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$vacancies = mysqli_query($conn, "SELECT v.*, c.name as category_name, (SELECT COUNT(*) FROM vacancy_applications WHERE vacancy_id = v.id) as applications_count, (SELECT COUNT(*) FROM vacancy_views WHERE vacancy_id = v.id) as views_count FROM vacancies v JOIN vacancy_categories c ON v.category_id = c.id WHERE v.user_id = $user_id ORDER BY v.created_at DESC");
$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total_vacancies, SUM((SELECT COUNT(*) FROM vacancy_applications WHERE vacancy_id = v.id)) as total_applications, SUM((SELECT COUNT(*) FROM vacancy_views WHERE vacancy_id = v.id)) as total_views FROM vacancies v WHERE v.user_id = $user_id"));
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои вакансии | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../profile.php">
                <img class="logo" src="../img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../profile.php">Назад в кабинет</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_vacancies.php">Мои вакансии</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vacancies.php">Добавить вакансию</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="logout.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Выйти
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="my-vacancies py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Мои вакансии</h2>
                <p class="lead text-muted">Управляйте своими вакансиями и просматривайте отклики</p>
                <div class="divider mx-auto"></div>
            </div>
            <?php 
            if (isset($_SESSION['success']))
            { 
            ?>
                <div class="alert alert-success alert-dismissible fade show text-center">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php 
            } 
            ?>
            <?php 
            if (isset($_SESSION['error']))
            {
            ?>
                <div class="alert alert-danger alert-dismissible fade show text-center">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php 
            } 
            ?>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h3 class="text-primary"><?= $stats['total_vacancies'] ? $stats['total_vacancies'] : 0?></h3>
                                    <p class="text-muted">Всего вакансий</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-primary"><?= $stats['total_views'] ? $stats['total_views'] : 0?></h3>
                                    <p class="text-muted">Просмотров</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-primary"><?= $stats['total_applications'] ? $stats['total_applications'] : 0?></h3>
                                    <p class="text-muted">Откликов</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php 
                if (mysqli_num_rows($vacancies) > 0)
                {
                ?>
                    <?php 
                    while ($vacancy = mysqli_fetch_assoc($vacancies)) 
                    {
                    ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary"><?= htmlspecialchars($vacancy['category_name']) ?></span>
                                        <small class="text-muted"><?= date('d.m.Y', strtotime($vacancy['created_at'])) ?></small>
                                    </div>
                                    <h4 class="card-title"><?= htmlspecialchars($vacancy['title']) ?></h4>
                                    <div class="vacancy-meta mb-3">
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
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-info">
                                            <i class="bi bi-eye"></i> <?= $vacancy['views_count'] ?>
                                        </span>
                                        <span class="badge bg-success">
                                            <i class="bi bi-people"></i> <?= $vacancy['applications_count'] ?>
                                        </span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="vacancy_details.php?id=<?= $vacancy['id'] ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i> Просмотреть
                                        </a>
                                        <button class="btn btn-outline-danger" 
                                                onclick="if(confirm('Вы уверены, что хотите удалить эту вакансию? Все отклики также будут удалены.')) window.location='delete_vacancy.php?id=<?= $vacancy['id'] ?>'">
                                            <i class="bi bi-trash"></i> Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                    } 
                    ?>
                <?php 
                }
                else 
                {
                ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-briefcase display-4 text-muted mb-4"></i>
                        <h4>У вас пока нет вакансий</h4>
                        <p class="text-muted mb-4">Создайте свою первую вакансию, чтобы привлекать студентов</p>
                        <a href="vacancies.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle me-1"></i> Добавить вакансию
                        </a>
                    </div>
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
                    <li class="mb-2"><a href="../profile.php" class="text-white text-decoration-none">Назад в кабинет</a></li>
                    <li class="mb-2"><a href="my_vacancies.php" class="text-white text-decoration-none">Мои вакансии</a></li>
                    <li><a href="vacancies.php" class="text-white text-decoration-none">Добавить работу</a></li>
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