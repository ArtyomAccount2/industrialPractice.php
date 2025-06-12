<?php
session_start();
require_once("config/link.php");

if (!isset($_SESSION['admin_id'])) 
{
    header("Location: files_admin/admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_role = $_SESSION['admin_role'];

$users_count = $conn->query("SELECT COUNT(*) FROM `users`")->fetch_row()[0];
$new_users = $conn->query("SELECT COUNT(*) FROM `users` WHERE `created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_row()[0];
$portfolio_count = $conn->query("SELECT COUNT(*) FROM `portfolio`")->fetch_row()[0];
$pending_portfolio = $conn->query("SELECT COUNT(*) FROM `portfolio` WHERE status = 'pending'")->fetch_row()[0];
$vacancies_count = $conn->query("SELECT COUNT(*) FROM `vacancies`")->fetch_row()[0];
$pending_vacancies = $conn->query("SELECT COUNT(*) FROM `vacancies` WHERE status = 'pending'")->fetch_row()[0];
$reviews_count = $conn->query("SELECT COUNT(*) FROM `reviews`")->fetch_row()[0];
$pending_reviews = $conn->query("SELECT COUNT(*) FROM `reviews` WHERE status = 'pending'")->fetch_row()[0];

$actions = $conn->query("SELECT a.*, ad.name FROM admin_actions a JOIN admins ad ON a.admin_id = ad.id ORDER BY a.created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="files_admin/style_admin.css">
</head>
<body class="d-flex flex-column min-vh-100">
    
<div class="flex-grow-1">
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
                            <a class="nav-link active" href="admin.php">
                                <i class="bi bi-speedometer2"></i> Панель управления
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="files_admin/admin_users.php">
                                <i class="bi bi-people"></i> Пользователи
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="files_admin/admin_portfolio.php">
                                <i class="bi bi-collection"></i> Портфолио
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="files_admin/admin_vacancies.php">
                                <i class="bi bi-briefcase"></i> Вакансии
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="files_admin/admin_reviews.php">
                                <i class="bi bi-chat-left-text"></i> Отзывы
                            </a>
                        </li>
                        <?php 
                        if ($admin_role == 'superadmin' || $admin_role == 'admin')
                        {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="files_admin/admin_settings.php">
                                <i class="bi bi-gear"></i> Настройки
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="files_admin/admin_logs.php">
                                <i class="bi bi-journal-text"></i> Логи действий
                            </a>
                        </li>
                        <?php 
                        } 
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="files_admin/admin_logout.php">
                                <i class="bi bi-box-arrow-right"></i> Выход
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Панель управления</h1>
                    <span class="admin-name"><?= htmlspecialchars($_SESSION['admin_name']) ?></span>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-primary text-white stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Пользователи</h6>
                                        <h2 class="mb-0"><?= $users_count ?></h2>
                                        <small>+<?= $new_users ?> за неделю</small>
                                    </div>
                                    <i class="bi bi-people stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-success text-white stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Портфолио</h6>
                                        <h2 class="mb-0"><?= $portfolio_count ?></h2>
                                        <small><?= $pending_portfolio ?> на модерации</small>
                                    </div>
                                    <i class="bi bi-collection stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-warning text-dark stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Вакансии</h6>
                                        <h2 class="mb-0"><?= $vacancies_count ?></h2>
                                        <small><?= $pending_vacancies ?> на модерации</small>
                                    </div>
                                    <i class="bi bi-briefcase stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-info text-white stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Отзывы</h6>
                                        <h2 class="mb-0"><?= $reviews_count ?></h2>
                                        <small><?= $pending_reviews ?> на модерации</small>
                                    </div>
                                    <i class="bi bi-chat-left-text stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Последние действия</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Администратор</th>
                                        <th>Действие</th>
                                        <th>Детали</th>
                                        <th>Дата</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($action = $actions->fetch_assoc()) 
                                    {
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($action['name']) ?></td>
                                        <td><?= htmlspecialchars($action['action']) ?></td>
                                        <td><?= htmlspecialchars($action['details']) ?></td>
                                        <td><?= date('d.m.Y H:i', strtotime($action['created_at'])) ?></td>
                                    </tr>
                                    <?php 
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>