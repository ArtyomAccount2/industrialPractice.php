<?php
session_start();
require_once("../config/link.php");

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сотрудничество | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img class="logo" src="../img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php 
                    if ($isLoggedIn) 
                    {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../profile.php">Главная</a>
                    </li>
                    <?php 
                    } 
                    else 
                    {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Главная</a>
                    </li>
                    <?php 
                    } 
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">Портфолио</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="cooperation.php">Сотрудничество</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vacancies.php">Вакансии</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_reviews.php">Отзывы</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php 
                    if ($isLoggedIn)
                    {
                    ?>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right"></i> Выйти
                        </a>
                    <?php 
                    }
                    else
                    {
                    ?>
                        <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Авторизация</button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registerModal">Регистрация</button>
                    <?php 
                    } 
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <section class="cooperation-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Сотрудничество с платформой</h2>
                <p class="lead text-muted">Возможности для студентов, работодателей и учебных заведений</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <h3 class="mb-3">Для студентов</h3>
                            <p>СтудМаркет предоставляет уникальные возможности для профессионального роста и развития:</p>
                            <ul class="text-start">
                                <li>Публикация своих работ и проектов</li>
                                <li>Доступ к эксклюзивным вакансиям</li>
                                <li>Возможность получить обратную связь от работодателей</li>
                                <li>Участие в реальных проектах и стажировках</li>
                                <li>Построение профессионального портфолио</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-building"></i>
                            </div>
                            <h3 class="mb-3">Для работодателей</h3>
                            <p>Наша платформа помогает компаниям находить талантливых сотрудников среди студентов:</p>
                            <ul class="text-start">
                                <li>Прямой доступ к молодым специалистам</li>
                                <li>Возможность оценить реальные работы кандидатов</li>
                                <li>Размещение вакансий и стажировок</li>
                                <li>Проведение конкурсов и кейс-чемпионатов</li>
                                <li>Сотрудничество с учебными заведениями</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="feature-icon mb-3">
                                        <i class="bi bi-award"></i>
                                    </div>
                                    <h3 class="mb-3">Для колледжа</h3>
                                    <p>СтудМаркет предлагает образовательным учреждениям современные инструменты для взаимодействия с бизнес-средой:</p>
                                    <ul class="text-start">
                                        <li>Мониторинг успехов выпускников</li>
                                        <li>Анализ востребованных навыков на рынке труда</li>
                                        <li>Организация совместных проектов с компаниями</li>
                                        <li>Повышение репутации учебного заведения</li>
                                        <li>Улучшение учебных программ на основе отзывов работодателей</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <img src="../img/image.jfif" alt="Сотрудничество с учебными заведениями" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <h3 class="mb-4">Форма для сотрудничества</h3>
                            <p class="mb-4">Заполните форму, и мы свяжемся с вами для обсуждения возможностей сотрудничества</p>
                            <form class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Ваше имя" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" placeholder="Телефон">
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select">
                                        <option selected disabled>Тип сотрудничества</option>
                                        <option>Я работодатель</option>
                                        <option>Я представитель учебного заведения</option>
                                        <option>Другое</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control" rows="4" placeholder="Сообщение"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Отправить заявку</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
    require_once("modals.php");
    require_once("footer.php");
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>