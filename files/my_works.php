<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id'])) 
{
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) 
{
    $work_id = (int)$_GET['delete'];
    mysqli_begin_transaction($conn);
    
    try 
    {
        $check_sql = "SELECT image_path FROM portfolio WHERE id = $work_id AND user_id = $user_id";
        $check_result = mysqli_query($conn, $check_sql);
        $work = mysqli_fetch_assoc($check_result);
        
        if (!$work) 
        {
            throw new Exception("Работа не найдена или нет прав на удаление");
        }
        
        $delete_likes_sql = "DELETE FROM portfolio_likes WHERE work_id = $work_id";
        mysqli_query($conn, $delete_likes_sql);

        $delete_views_sql = "DELETE FROM portfolio_views WHERE work_id = $work_id";
        mysqli_query($conn, $delete_views_sql);
        
        $delete_sql = "DELETE FROM portfolio WHERE id = $work_id AND user_id = $user_id";
        mysqli_query($conn, $delete_sql);
        
        if ($work['image_path'] && file_exists($work['image_path'])) 
        {
            unlink($work['image_path']);
        }

        mysqli_commit($conn);
        $_SESSION['success'] = "Работа и все связанные данные успешно удалены";
    } 
    catch (Exception $e) 
    {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Ошибка при удалении: " . $e->getMessage();
    }
    
    header("Location: my_works.php");
    exit();
}

$works = mysqli_query($conn, "SELECT p.*, c.name as category_name, (SELECT COUNT(*) FROM portfolio_likes WHERE work_id = p.id) as likes_count, (SELECT COUNT(*) FROM portfolio_views WHERE work_id = p.id) as views_count FROM portfolio p JOIN portfolio_categories c ON p.category_id = c.id WHERE p.user_id = $user_id ORDER BY p.created_at DESC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои работы - СтудМаркет</title>
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
                        <a class="nav-link active" href="my_works.php">Мои работы</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">Добавить работу</a>
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

    <section class="py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Мои работы</h2>
                <p class="lead text-muted">Управляйте своими работами в портфолио</p>
                <div class="divider mx-auto"></div>
            </div>
            <?php 
            if (isset($_SESSION['success']))
            { 
            ?>
                <div class="alert alert-success alert-dismissible fade show text-center">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
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
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php 
            } 
            ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php 
                if (mysqli_num_rows($works) > 0)
                {
                ?>
                    <?php 
                    while($work = mysqli_fetch_assoc($works))
                    {
                    ?>
                        <div class="col">
                            <div class="card h-100 work-card">
                                <button class="btn btn-danger btn-sm btn-delete" 
                                        onclick="if(confirm('Вы уверены, что хотите полностью удалить эту работу?')) window.location='my_works.php?delete=<?= $work['id'] ?>'">
                                    <i class="bi bi-trash"></i> Удалить
                                </button>
                                <img src="<?= htmlspecialchars($work['image_path']) ?>" class="card-img-top work-img" alt="<?= htmlspecialchars($work['title']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($work['title']) ?></h5>
                                    <span class="badge bg-primary mb-2"><?= htmlspecialchars($work['category_name']) ?></span>
                                    <p class="card-text"><?= mb_substr(htmlspecialchars($work['description']), 0, 100) ?>...</p>
                                </div>
                                <div class="card-footer border-0 bg-white d-flex justify-content-between">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i> <?= date('d.m.Y', strtotime($work['created_at'])) ?>
                                    </small>
                                    <span class="stats-badge">
                                        <i class="bi bi-eye me-1"></i><?= $work['views_count'] ?> 
                                        <i class="bi bi-heart ms-2 me-1"></i><?= $work['likes_count'] ?>
                                    </span>
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
                    <div class="col-12">
                        <div class="card empty-portfolio">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-folder-x display-4 text-muted mb-4"></i>
                                <h4>Ваше портфолио пусто</h4>
                                <p class="text-muted mb-4">Добавьте свою первую работу, чтобы она появилась здесь</p>
                                <a href="portfolio.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-plus-circle me-1"></i> Добавить работу
                                </a>
                            </div>
                        </div>
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
                    <li class="mb-2"><a href="my_works.php" class="text-white text-decoration-none">Мои работы</a></li>
                    <li><a href="portfolio.php" class="text-white text-decoration-none">Добавить работу</a></li>
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