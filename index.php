<?php
session_start();
require_once("config/link.php");

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img class="logo" src="img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="files/portfolio.php">Портфолио</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="files/cooperation.php">Сотрудничество</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="files/vacancies.php">Вакансии</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="files/all_reviews.php">Отзывы</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Авторизация</button>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registerModal">Регистрация</button>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section vh-100 d-flex align-items-center">
        <div class="hero-slide carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner h-100">
                <div class="carousel-item active h-100">
                    <img src="img/hero1.jfif" class="d-block w-100 h-100 object-fit-cover" alt="Slide 1">
                    <div class="carousel-caption d-none d-md-block"></div>
                </div>
                <div class="carousel-item h-100">
                    <img src="img/hero2.jfif" class="d-block w-100 h-100 object-fit-cover" alt="Slide 2">
                    <div class="carousel-caption d-none d-md-block"></div>
                </div>
                <div class="carousel-item h-100">
                    <img src="img/hero3.jfif" class="d-block w-100 h-100 object-fit-cover" alt="Slide 3">
                    <div class="carousel-caption d-none d-md-block"></div>
                </div>
            </div>
        </div>
        <div class="container text-center position-relative z-index-1">
            <h1 class="display-4 fw-bold mb-3 shadow-text">СтудМаркет - мост между талантами и возможностями</h1>
            <p class="lead mb-4 shadow-text">Специализированная платформа для студентов Колледжа предпринимательства, где можно демонстрировать работы, находить вакансии и взаимодействовать с работодателями</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a data-bs-toggle="modal" data-bs-target="#registerModal" class="btn btn-primary btn-lg px-4 gap-3">Начать сейчас</a>
                <a href="#" class="btn btn-light btn-lg px-4">Узнать больше</a>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container my-4">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Возможности платформы</h2>
                <p class="lead text-muted">Уникальные инструменты для студентов и работодателей</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h3>Для студентов</h3>
                        <p>Создайте профессиональное портфолио, демонстрируйте свои проекты и находите подходящие вакансии от проверенных работодателей.</p>
                        <div class="text-start mt-3 ps-4">
                            <li>Публикация работ и проектов</li>
                            <li>Доступ к эксклюзивным вакансиям</li>
                            <li>Обратная связь от работодателей</li>
                            <li>Рекомендации по развитию</li>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3>Для работодателей</h3>
                        <p>Находите талантливых студентов, просматривайте портфолио и публикуйте вакансии напрямую для целевой аудитории колледжа.</p>
                        <ul class="text-start mt-3 ps-4">
                            <li>Поиск по специализациям</li>
                            <li>Просмотр студенческих работ</li>
                            <li>Публикация вакансий</li>
                            <li>Прямой контакт с кандидатами</li>
                        </ul>
                    </div>
                </div>
               <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <h3>Для колледжа</h3>
                        <p>Платформа помогает отслеживать успехи выпускников, улучшать учебные программы и укреплять связи с бизнес-сообществом, а также повышать качество обучения.</p>
                        <ul class="text-start mt-3 ps-4">
                            <li>Мониторинг трудоустройства</li>
                            <li>Анализ востребованных навыков</li>
                            <li>Совместные проекты</li>
                            <li>Повышение репутации</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="portfolio-preview py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Примеры студенческих работ</h2>
                <p class="lead text-muted">Лучшие проекты наших студентов</p>
                <div class="divider mx-auto"></div>
            </div>           
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="img/portfolio1.jfif" class="card-img-top" alt="Студенческая работа">
                        <div class="card-body step-card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge badge-category">Маркетинг</span>
                            </div>
                            <h5 class="card-title">Маркетинговая стратегия</h5>
                            <p class="card-text">Разработка комплексной маркетинговой стратегии для стартапа в сфере IT.</p>
                            <div class="step-card d-flex justify-content-between align-items-center">
                                <small class="text-muted">22.05.2025</small>
                                <p class="text-muted small mb-0">Иван П.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="img/portfolio2.jfif" class="card-img-top" alt="Студенческая работа">
                        <div class="card-body step-card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge badge-category">Дизайн</span>
                            </div>
                            <h5 class="card-title">Дизайн мобильного приложения</h5>
                            <p class="card-text">Полный цикл разработки UI/UX для приложения здорового питания.</p>
                            <div class="step-card d-flex justify-content-between align-items-center">
                                <small class="text-muted">17.05.2025</small>
                                <p class="text-muted small mb-0">Анна С.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="img/portfolio3.jfif" class="card-img-top" alt="Студенческая работа">
                        <div class="card-body step-card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge badge-category">IT</span>
                            </div>
                            <h5 class="card-title">Бизнес-план кафе</h5>
                            <p class="card-text">Детальный бизнес-план и финансовые расчеты для кофейни премиум-класса.</p>
                            <div class="step-card d-flex justify-content-between align-items-center">
                                <small class="text-muted">12.05.2025</small>
                                <p class="text-muted small mb-0">Михаил И.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="text-center mt-4">
                <a href="files/portfolio.php" class="btn btn-primary btn-lg">Смотреть все работы</a>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="container text-center">
            <div class="section-header mb-5">
                <h2 class="display-5 fw-bold">Как это работает?</h2>
                <p class="lead text-muted">Всего 3 простых шага к успешному трудоустройству</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <span class="badge bg-primary rounded-circle p-3 mb-3">1</span>
                            <h5 class="card-title">Регистрация</h5>
                            <p class="card-text">Создайте аккаунт работодателя или студента всего за несколько минут</p>
                            <div class="step-details mt-3">
                                <p class="small">Для студентов: бесплатно</p>
                                <p class="small">Для работодателей: проверка и подтверждение</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <span class="badge bg-primary rounded-circle p-3 mb-3">2</span>
                            <h5 class="card-title">Заполнение профиля</h5>
                            <p class="card-text">Добавьте информацию о себе или своей компании</p>
                            <div class="step-details mt-3">
                                <ul class="text-start ps-4">
                                    <li>Образование и навыки</li>
                                    <li>Проекты и работы</li>
                                    <li>Интересы и цели</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <span class="badge bg-primary rounded-circle p-3 mb-3">3</span>
                            <h5 class="card-title">Поиск и контакт</h5>
                            <p class="card-text">Работодатели находят подходящих кандидатов и связываются с ними</p>
                            <div class="step-details mt-3">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="step-icons badge bg-light text-dark border"><i class="bi bi-search me-1"></i>Поиск и просмотр</span>
                                    <span class="step-icons badge bg-light text-dark border mt-2"><i class="bi bi-chat me-1"></i>Общение</span>
                                    <span class="step-icons badge bg-light text-dark border mt-2"><i class="bi bi-briefcase me-1"></i>Сотрудничество</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cta-box mt-5 p-4 rounded-3">
                <h3 class="mb-3">Готовы присоединиться?</h3>
                <p class="mb-4">Станьте частью сообщества СтудМаркет и откройте новые возможности</p>
                <a href="#" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#registerModal">Зарегистрироваться</a>
                <a href="files/cooperation.php" class="btn btn-outline-primary">Сотрудничество</a>
            </div>
        </div>
    </section>

    <section class="latest-vacancies py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Свежие вакансии</h2>
                <p class="lead text-muted">Актуальные предложения для студентов</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="img/vacancy1.jfif" class="card-img-top" alt="Стажер-маркетолог" style="height: 200px; object-fit: cover;">
                        <div class="card-body step-card-body">
                            <h5 class="card-title">Стажер-маркетолог</h5>
                            <p class="text-muted small">Digital Agency</p>
                            <div class="vacancy-meta my-3">
                                <p class="mb-1"><i class="bi bi-geo-alt me-1"></i>Калининград</p>
                                <p class="mb-1"><i class="bi bi-cash-coin me-1"></i>от 25 000 ₽</p>
                                <p class="mb-0"><i class="bi bi-clock me-1"></i>Стажировка</p>
                            </div>
                            <div class="d-flex flex-column justify-content-between align-items-center">
                                <p class="card-text">Участие в разработке digital-стратегий, работа с соцсетями.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="img/vacancy2.jfif" class="card-img-top" alt="Помощник дизайнера" style="height: 200px; object-fit: cover;">
                        <div class="card-body step-card-body">
                            <h5 class="card-title">Помощник дизайнера</h5>
                            <p class="text-muted small">Creative Studio</p>
                            <div class="vacancy-meta my-3">
                                <p class="mb-1"><i class="bi bi-geo-alt me-1"></i>Удалённо</p>
                                <p class="mb-1"><i class="bi bi-cash-coin me-1"></i>30 000 ₽</p>
                                <p class="mb-0"><i class="bi bi-clock me-1"></i>Частичная занятость</p>
                            </div>
                            <div class="d-flex flex-column justify-content-between align-items-center">
                                <p class="card-text">Подготовка макетов, ретушь фото, помощь в создании презентаций.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="img/vacancy3.jfif" class="card-img-top" alt="Ассистент бухгалтера" style="height: 200px; object-fit: cover;">
                        <div class="card-body step-card-body">
                            <h5 class="card-title">Ассистент бухгалтера</h5>
                            <p class="text-muted small">Финансовая группа</p>
                            <div class="vacancy-meta my-3">
                                <p class="mb-1"><i class="bi bi-geo-alt me-1"></i>Калининград</p>
                                <p class="mb-1"><i class="bi bi-cash-coin me-1"></i>от 30 000 ₽</p>
                                <p class="mb-0"><i class="bi bi-clock me-1"></i>Полная занятость</p>
                            </div>
                            <div class="d-flex flex-column justify-content-between align-items-center">
                                <p class="card-text">Ведение первичной документации, помощь в подготовке отчетности.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="files/vacancies.php" class="btn btn-primary btn-lg">Все вакансии</a>
            </div>
        </div>
    </section>

    <section class="reviews-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Отзывы и оценки</h2>
                <p class="lead text-muted">Что говорят студенты и работодатели о нашей платформе</p>
                <div class="divider mx-auto"></div>
            </div>         
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="review-card p-4 bg-white rounded-3 h-100">
                        <div class="review-header d-flex justify-content-between mb-3">
                            <div class="reviewer-info d-flex align-items-center">
                                <img src="uploads/avatars/avatar1.png" alt="Аватар" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-1">Анна К.</h6>
                                    <p class="text-muted small mb-0">Студент</p>
                                </div>
                            </div>
                            <div class="rating">
                                <div class="stars">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <div class="review-body">
                            <p class="mb-3">Благодаря СтудМаркету я получила стажировку в крупной компании уже на 3 курсе. Очень удобная платформа, где можно показать свои работы и сразу получить отклик от работодателей.</p>
                        </div>
                        <div class="review-footer d-flex justify-content-between align-items-center">
                            <p class="text-muted small mb-0">21.05.2025</p>
                            <button class="btn btn-sm btn-outline-primary like-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-hand-thumbs-up"></i><span class="like-count"> 24</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="review-card p-4 bg-white rounded-3 h-100">
                        <div class="review-header d-flex justify-content-between mb-3">
                            <div class="reviewer-info d-flex align-items-center">
                                <img src="uploads/avatars/avatar2.png" alt="Аватар" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-1">TechSolutions Inc.</h6>
                                    <p class="text-muted small mb-0">Работодатель</p>
                                </div>
                            </div>
                            <div class="rating">
                                <div class="stars">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <div class="review-body">
                            <p class="mb-3">За последний год нашли через платформу 3 отличных стажера. Особенно ценно, что можно сразу увидеть реальные работы студентов, а не только сухие резюме. Экономит массу времени!</p>
                        </div>
                        <div class="review-footer d-flex justify-content-between align-items-center">
                            <p class="text-muted small mb-0">19.05.2025</p>
                            <button class="btn btn-sm btn-outline-primary like-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-hand-thumbs-up"></i><span class="like-count"> 18</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="review-card p-4 bg-white rounded-3 h-100">
                        <div class="review-header d-flex justify-content-between mb-3">
                            <div class="reviewer-info d-flex align-items-center">
                                <img src="uploads/avatars/avatar3.png" alt="Аватар" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-1">Иван П.</h6>
                                    <p class="text-muted small mb-0">Студент</p>
                                </div>
                            </div>
                            <div class="rating">
                                <div class="stars">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <div class="review-body">
                            <p class="mb-3">Платформа помогла мне найти первых клиентов на фрилансе еще во время учебы. Теперь у меня есть портфолио и опыт, которые помогут устроиться на работу после выпуска.</p>
                        </div>
                        <div class="review-footer d-flex justify-content-between align-items-center">
                            <p class="text-muted small mb-0">17.05.2025</p>
                            <button class="btn btn-sm btn-outline-primary like-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-hand-thumbs-up"></i><span class="like-count"> 12</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rating-stats mt-2 p-4 bg-white rounded-3">
                <div class="row d-flex align-items-center text-center">
                    <div class="col-md-3">
                        <div class="display-4 fw-bold text-primary">4.8</div>
                        <div class="stars mb-2">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-half text-warning"></i>
                        </div>
                        <p class="small text-muted mb-0">Средняя оценка</p>
                    </div>
                    <div class="col-md-3">
                        <div class="display-4 fw-bold text-primary">1,250+</div>
                        <p class="small text-muted mb-0">Всего отзывов</p>
                    </div>
                    <div class="col-md-3">
                        <div class="display-4 fw-bold text-primary">92%</div>
                        <p class="small text-muted mb-0">Положительных оценок</p>
                    </div>
                    <div class="col-md-3">
                        <div class="display-4 fw-bold text-primary">85%</div>
                        <p class="small text-muted mb-0">Повторных работодателей</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="files/all_reviews.php" class="btn btn-primary btn-lg"> Смотреть все отзывы</button></a>
            </div>
        </div>
    </section>
</div>

<?php
    require_once("files/modals.php");
    require_once("files/footer.php");
?>

<script>
document.addEventListener('DOMContentLoaded', function() 
{
    if (window.location.hash) 
    {
        let modalId = window.location.hash;
        let modal = new bootstrap.Modal(document.querySelector(modalId));
        modal.show();

        history.replaceState(null, null, ' ');
    }
});
</script>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>