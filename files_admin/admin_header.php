<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block bg-dark admin-sidebar collapse">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <img src="../img/img5.png" alt="Логотип" class="logo mb-2">
                    <h5 class="text-white">Админ-панель</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../admin.php">
                            <i class="bi bi-speedometer2"></i> Панель управления
                        </a>
                    </li>
                    <?php 
                    if ($_SESSION['admin_role'] != 'moderator') 
                    {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_users.php">
                            <i class="bi bi-people"></i> Пользователи
                        </a>
                    </li>
                    <?php 
                    } 
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_portfolio.php">
                            <i class="bi bi-collection"></i> Портфолио
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_vacancies.php">
                            <i class="bi bi-briefcase"></i> Вакансии
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reviews.php">
                            <i class="bi bi-chat-left-text"></i> Отзывы
                        </a>
                    </li>
                    <?php 
                    if ($_SESSION['admin_role'] != 'moderator') 
                    {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_settings.php">
                            <i class="bi bi-gear"></i> Настройки
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logs.php">
                            <i class="bi bi-journal-text"></i> Логи действий
                        </a>
                    </li>
                    <?php
                    }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php">
                            <i class="bi bi-box-arrow-right"></i> Выход
                        </a>
                    </li>
                </ul>
            </div>
        </div>