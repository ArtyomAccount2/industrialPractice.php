<?php
session_start();
require_once("../config/link.php");

$isLoggedIn = isset($_SESSION['user_id']);

if (isset($_SESSION['admin_id'])) 
{
    header("Location: ../admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM `admins` WHERE `email` = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) 
    {
        $admin = $result->fetch_assoc();
        
        if (password_verify($password, $admin['password'])) 
        {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $conn->query("INSERT INTO `admin_actions` (`admin_id`, `action`, `details`, `ip_address`) VALUES ({$admin['id']}, 'Вход в систему', 'Успешный вход', '$ip')");
            
            header("Location: ../admin.php");
            exit();
        } 
        else 
        {
            $_SESSION['error'] = "Неверный email или пароль";
            header("Location: admin_login.php");
            exit();
        }
    } 
    else 
    {
        $_SESSION['error'] = "Неверный email или пароль";
        header("Location: admin_login.php");
        exit();
    }
}

if (isset($_SESSION['error'])) 
{
    $error = $_SESSION['error'];
} 
else 
{
    $error = null;
}

unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_admin.css">
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
                        <a class="nav-link" href="../files/portfolio.php">Портфолио</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../files/cooperation.php">Сотрудничество</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../files/vacancies.php">Вакансии</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../files/all_reviews.php">Отзывы</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="login-container py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card login-card">
                        <div class="card-header login-header text-center py-4">
                            <h4><i class="bi bi-shield-lock"></i> Административная панель</h4>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <?php 
                            if (isset($error)) 
                            {
                            ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                            </div>
                            <?php 
                            } 
                            ?>
                            <form method="POST" action="admin_login.php">
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label">Пароль</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-login text-white btn-lg">
                                        <i class="bi bi-box-arrow-in-right"></i> Войти
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
    require_once("../files/footer.php");
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>