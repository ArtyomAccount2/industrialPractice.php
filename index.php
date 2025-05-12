<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профессиональные резюме студентов</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Student<span class="text-primary">Resume</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Главная</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio.php">Портфолио</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Обратная связь</a></li>
                </ul>
                <div class="d-flex">
                    <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Вход</button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">Регистрация</button>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section vh-100 d-flex align-items-center">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Найдите талантливых студентов</h1>
            <p class="lead">Платформа для размещения профессиональных резюме студентов и выпускников</p>
            <a href="portfolio.php" class="btn btn-primary btn-lg mt-3">Посмотреть портфолио</a>
        </div>
    </section>

    <section class="features-section">
        <div class="container my-5">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Почему выбирают нас</h2>
                <p class="lead text-muted">Мы соединяем талантливых студентов с ведущими компаниями</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center">
                        <div class="feature-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3>Широкий выбор</h3>
                        <p>Более 1000 резюме студентов из различных вузов и специальностей. Найдите идеального кандидата для вашей компании среди нашего обширного каталога.</p>
                        <div class="stats mt-3">
                            <span class="badge bg-primary rounded-pill">500+ IT специалистов</span>
                            <span class="badge bg-success rounded-pill">300+ дизайнеров</span>
                            <span class="badge bg-info rounded-pill">200+ маркетологов</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center">
                        <div class="feature-icon">
                            <i class="bi bi-search"></i>
                        </div>
                        <h3>Удобный поиск</h3>
                        <p>Мощные фильтры по специальностям, навыкам и уровню подготовки помогут быстро найти именно тех кандидатов, которые соответствуют вашим требованиям.</p>
                        <ul class="text-start mt-3 ps-4">
                            <li>Фильтр по вузам и факультетам</li>
                            <li>Поиск по ключевым навыкам</li>
                            <li>Сортировка по рейтингу</li>
                            <li>Географический поиск</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center">
                        <div class="feature-icon">
                            <i class="bi bi-hand-thumbs-up"></i>
                        </div>
                        <h3>Прямой контакт</h3>
                        <p>Наша платформа обеспечивает прямую коммуникацию с кандидатами без посредников, что ускоряет процесс найма и делает его более эффективным.</p>
                        <div class="testimonial mt-3 p-3 bg-light rounded">
                            <p class="mb-0"><i>"Нашли идеального стажера за 2 дня!"</i></p>
                            <small class="text-muted">- Ольга, HR-менеджер TechCompany</small>
                        </div>
                    </div>
                </div>
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
                            <p class="card-text">Создайте аккаунт работодателя или студента всего за 2 минуты</p>
                            <div class="step-details mt-3">
                                <p><small>Для студентов: бесплатно</small></p>
                                <p><small>Для работодателей: бесплатный пробный период</small></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <span class="badge bg-primary rounded-circle p-3 mb-3">2</span>
                            <h5 class="card-title">Заполнение профиля</h5>
                            <p class="card-text">Студенты добавляют информацию о себе и навыках</p>
                            <div class="step-details mt-3">
                                <ul class="text-start ps-4">
                                    <li>Добавьте образование</li>
                                    <li>Укажите навыки</li>
                                    <li>Загрузите проекты</li>
                                    <li>Получите рекомендации</li>
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
                                <div class="d-flex justify-content-around">
                                    <span class="badge bg-light text-dark"><i class="bi bi-envelope me-1"></i> Сообщения</span>
                                    <span class="badge bg-light text-dark"><i class="bi bi-calendar me-1"></i> Интервью</span>
                                    <span class="badge bg-light text-dark"><i class="bi bi-briefcase me-1"></i> Оффер</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cta-box mt-5 p-4 rounded-3">
                <h3 class="mb-3">Готовы начать?</h3>
                <p class="mb-4">Присоединяйтесь к тысячам студентов и работодателей, которые уже нашли друг друга</p>
                <a href="#" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#registerModal">Зарегистрироваться</a>
                <a href="portfolio.php" class="btn btn-light">Посмотреть резюме</a>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Вход в систему</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="loginEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="loginPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Регистрация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="regName" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="regName" required>
                    </div>
                    <div class="mb-3">
                        <label for="regEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="regEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="regPassword" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="regPassword" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Вы регистрируетесь как:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="userType" id="studentType" checked>
                            <label class="form-check-label" for="studentType">
                                Студент
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="userType" id="employerType">
                            <label class="form-check-label" for="employerType">
                                Работодатель
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-4">
    <div class="container text-center">
        <div class="mb-3">
            <a href="index.php" class="text-white text-decoration-none me-3">Главная</a>
            <a href="portfolio.php" class="text-white text-decoration-none me-3">Портфолио</a>
            <a href="feedback.php" class="text-white text-decoration-none">Обратная связь</a>
        </div>
        <p class="mb-0">© 2025 StudentResume. Все права защищены.</p>
    </div>
</footer>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>