<?php
session_start();
require_once("../config/link.php");

if (isset($_GET['token'])) 
{
    $token = $_GET['token'];
} 
else 
{
    $token = '';
}

$isLoggedIn = isset($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'GET') 
{
    $sql = "SELECT `user_id` FROM `password_resets` WHERE `token` = '$token' AND `expires_at` > NOW() AND `used` = 0";
    $result = mysqli_query($conn, $sql);
    $reset = mysqli_fetch_assoc($result);
    
    if (!$reset) 
    {
        $_SESSION['error'] = "Недействительная или просроченная ссылка для сброса пароля.";
        header("Location: forgot_password.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if ($password !== $confirm_password) 
    {
        $_SESSION['error'] = "Пароли не совпадают.";
        header("Location: reset_password.php?token=$token");
        exit();
    }

    $sql = "SELECT `user_id` FROM `password_resets` WHERE `token` = '$token' AND `expires_at` > NOW() AND `used` = 0";
    $result = mysqli_query($conn, $sql);
    $reset = mysqli_fetch_assoc($result);
    
    if ($reset) 
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_id = $reset['user_id'];
        
        $sql = "UPDATE `users` SET `password` = '$hashed_password' WHERE `id` = $user_id";
        mysqli_query($conn, $sql);

        $sql = "UPDATE `password_resets` SET `used` = 1 WHERE `token` = '$token'";
        mysqli_query($conn, $sql);
        
        $_SESSION['success'] = "Пароль успешно изменен. Теперь вы можете войти.";
        header("Location: index.php");
        exit();
    } 
    else 
    {
        $_SESSION['error'] = "Недействительная или просроченная ссылка для сброса пароля.";
        header("Location: forgot_password.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img class="logo" src="../img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">Портфолио</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cooperation.php">Сотрудничество</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vacancies.php">Вакансии</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_reviews.php">Отзывы</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="password-reset py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <i class="bi bi-key text-primary" style="font-size: 3rem;"></i>
                                <h2 class="mt-3">Установка нового пароля</h2>
                                <p class="text-muted">Введите новый пароль для вашего аккаунта</p>
                            </div>
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
                            <form action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="POST">
                                <div class="mb-4">
                                    <label for="password" class="form-label">Новый пароль <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Введите новый пароль">
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Подтвердите пароль <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Повторите пароль">
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Изменить пароль
                                    </button>
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
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>