<?php
session_start();
require_once("../config/link.php");

$isLoggedIn = isset($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_cooperation'])) 
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $sql = "INSERT INTO cooperation_requests (name, email, phone, type, message, created_at) VALUES ('$name', '$email', '$phone', '$type', '$message', NOW())";
    
    if (mysqli_query($conn, $sql)) 
    {
        $_SESSION['success'] = "Ваша заявка на сотрудничество успешно отправлена!";
    } 
    else 
    {
        $_SESSION['error'] = "Ошибка при отправке заявки: " . mysqli_error($conn);
    }
    
    header("Location: cooperation.php");
    exit();
}
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

    <section class="cooperation-hero vh-100 bg-primary text-white d-flex align-items-center">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">Сотрудничество с <span class="text-warning">СтудМаркет</span></h1>
                    <p class="lead mb-3 text-white-50">Объединяем талантливых студентов, прогрессивные компании и учебные заведения для взаимовыгодного партнерства</p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="#cooperation-form" class="btn btn-warning btn-lg px-4 py-2 rounded-pill fw-bold">
                            <i class="bi bi-send-check me-2"></i>Оставить заявку
                        </a>
                        <a href="#benefits" class="btn btn-outline-light btn-lg px-4 py-2 rounded-pill">
                            <i class="bi bi-arrow-down-circle me-2"></i>Узнать больше
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="../img/image.jfif" alt="Сотрудничество" class="img-fluid rounded-3">
                </div>
            </div>
        </div>
    </section>

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
                        <div class="card-body p-4 d-flex align-items-center flex-column">
                            <div class="icon-box bg-primary mb-2 bg-opacity-10 text-primary rounded-circle me-3">
                                <i class="bi bi-person-badge fs-2"></i>
                            </div>
                            <h3 class="mb-3">Для студентов</h3>
                            <p>СтудМаркет предоставляет уникальные возможности для профессионального роста и развития:</p>
                            <ul class="list-check text-start">
                                <li>Публикация своих работ и проектов</li>
                                <li>Доступ к эксклюзивным вакансиям</li>
                                <li>Возможность получить обратную связь от работодателей</li>
                                <li>Участие в реальных проектах и стажировках</li>
                                <li>Построение профессионального портфолио</li>
                            </ul>
                            <?php 
                            if (!$isLoggedIn)
                            { 
                            ?>
                            <div class="mt-2">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registerModal">Присоединиться</button>
                            </div>
                            <?php 
                            }
                            else
                            {
                            ?>
                            <div class="mt-2">
                                <a href="portfolio.php" class="btn btn-outline-primary">Добавить работу</a>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center flex-column">
                            <div class="icon-box bg-primary mb-2 bg-opacity-10 text-primary rounded-circle me-3">
                                <i class="bi bi-building fs-2"></i>
                            </div>
                            <h3 class="mb-3">Для работодателей</h3>
                            <p>Наша платформа помогает компаниям находить талантливых сотрудников среди студентов:</p>
                            <ul class="list-check text-start">
                                <li>Прямой доступ к молодым специалистам</li>
                                <li>Возможность оценить реальные работы кандидатов</li>
                                <li>Размещение вакансий и стажировок</li>
                                <li>Проведение конкурсов и кейс-чемпионатов</li>
                                <li>Сотрудничество с учебными заведениями</li>
                            </ul>
                            <div class="mt-2">
                                <a href="vacancies.php" class="btn btn-outline-primary">Разместить вакансию</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6 d-flex align-items-center flex-column">
                                    <div class="icon-box bg-primary mb-2 bg-opacity-10 text-primary rounded-circle me-3">
                                        <i class="bi bi-award fs-2"></i>
                                    </div>
                                    <h3 class="mb-3">Для колледжа</h3>
                                    <p>СтудМаркет предлагает образовательным учреждениям современные инструменты для взаимодействия с бизнес-средой:</p>
                                    <ul class="list-check text-start">
                                        <li>Мониторинг успехов выпускников</li>
                                        <li>Анализ востребованных навыков на рынке труда</li>
                                        <li>Организация совместных проектов с компаниями</li>
                                        <li>Повышение репутации учебного заведения</li>
                                        <li>Улучшение учебных программ на основе отзывов работодателей</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <img src="../img/img0.png" alt="Сотрудничество с учебными заведениями" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="benefits-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-4 fw-bold mb-3">Преимущества сотрудничества</h2>
                <p class="lead text-muted">Почему компании и учебные заведения выбирают нашу платформу</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center p-4 h-100">
                        <div class="benefit-icon mx-auto mb-4">
                            <i class="bi bi-people-fill fs-1 text-primary"></i>
                        </div>
                        <h3 class="h4 mb-3">Доступ к талантам</h3>
                        <p class="text-muted">Прямой контакт с лучшими студентами и выпускниками колледжа</p>
                        <div class="benefit-number">01</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center p-4 h-100">
                        <div class="benefit-icon mx-auto mb-4">
                            <i class="bi bi-briefcase-fill fs-1 text-primary"></i>
                        </div>
                        <h3 class="h4 mb-3">Экономия времени</h3>
                        <p class="text-muted">Быстрый подбор кандидатов с готовыми портфолио</p>
                        <div class="benefit-number">02</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center p-4 h-100">
                        <div class="benefit-icon mx-auto mb-4">
                            <i class="bi bi-graph-up-arrow fs-1 text-primary"></i>
                        </div>
                        <h3 class="h4 mb-3">Развитие бренда</h3>
                        <p class="text-muted">Укрепление репутации среди молодых специалистов</p>
                        <div class="benefit-number">03</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center p-4 h-100">
                        <div class="benefit-icon mx-auto mb-4">
                            <i class="bi bi-lightbulb-fill fs-1 text-primary"></i>
                        </div>
                        <h3 class="h4 mb-3">Совместные проекты</h3>
                        <p class="text-muted">Возможность реализовывать реальные кейсы со студентами</p>
                        <div class="benefit-number">04</div>
                    </div>
                </div>
            </div>
            <div class="row mt-2 pt-4 g-4">
                <div class="col-12">
                    <div class="stats-card bg-primary text-white p-4 rounded-4 shadow">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="display-4 fw-bold mb-2">150+</div>
                                <p class="mb-0 opacity-75">Компаний-партнеров</p>
                            </div>
                            <div class="col-md-3">
                                <div class="display-4 fw-bold mb-2">80%</div>
                                <p class="mb-0 opacity-75">Трудоустройство выпускников</p>
                            </div>
                            <div class="col-md-3">
                                <div class="display-4 fw-bold mb-2">50+</div>
                                <p class="mb-0 opacity-75">Совместных проектов</p>
                            </div>
                            <div class="col-md-3">
                                <div class="display-4 fw-bold mb-2">4.8</div>
                                <p class="mb-0 opacity-75">Средняя оценка партнеров</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="cooperation-form" class="cooperation-form py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-5">
                                <h2 class="fw-bold">Форма для сотрудничества</h2>
                                <p class="text-muted">Заполните форму, и мы свяжемся с вами для обсуждения возможностей</p>
                            </div>
                            <?php 
                            if (isset($_SESSION['success']))
                            { 
                            ?>
                                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                            <?php 
                            } 
                            ?>
                            <?php 
                            if (isset($_SESSION['error']))
                            {
                            ?>
                                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php 
                            } 
                            ?>
                            <form method="POST" action="cooperation.php" class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Ваше имя <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Телефон</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Тип сотрудничества <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="" selected disabled>Выберите вариант</option>
                                        <option value="employer">Я работодатель</option>
                                        <option value="college">Я представитель учебного заведения</option>
                                        <option value="other">Другое</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Сообщение <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agree" required>
                                        <label class="form-check-label" for="agree">
                                            Я согласен на обработку персональных данных
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" name="submit_cooperation" class="btn btn-primary btn-lg px-4">Отправить заявку</button>
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