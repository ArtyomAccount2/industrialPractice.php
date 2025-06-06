<?php
session_start();
require_once("config/link.php");

$isLoggedIn = isset($_SESSION['user_id']);

if (!isset($_SESSION['user_id'])) 
{
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_type = $_SESSION['user_type'];
$user_phone = $_SESSION['user_phone'];

$sql = "SELECT * FROM `users` WHERE `id` = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$avatar_dir = "uploads/avatars/";

if (!file_exists($avatar_dir)) 
{
    mkdir($avatar_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) 
{
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    $update_sql = "UPDATE `users` SET name = '$name', `email` = '$email', `phone` = '$phone' WHERE `id` = $user_id";

    if ($conn->query($update_sql)) 
    {
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_phone'] = $phone;

        $_SESSION['success'] = "Профиль успешно обновлен";
        header("Location: profile.php");
        exit();
    } 
    else 
    {
        $_SESSION['error'] = "Ошибка обновления профиля: " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) 
{
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $sql = "SELECT `password` FROM `users` WHERE `id` = $user_id";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
    
    if (!password_verify($current_password, $user['password'])) 
    {
        $_SESSION['error'] = "Текущий пароль неверен";
    } 
    else if ($new_password != $confirm_password) 
    {
        $_SESSION['error'] = "Новые пароли не совпадают";
    } 
    else 
    {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE `users` SET `password` = '$new_password_hash' WHERE `id` = $user_id";

        if ($conn->query($update_sql)) 
        {
            $_SESSION['success'] = "Пароль успешно изменен";
            header("Location: profile.php");
            exit();
        } 
        else 
        {
            $_SESSION['error'] = "Ошибка изменения пароля: " . $conn->error;
        }
    }

    header("Location: profile.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_avatar']) && isset($_FILES['avatar'])) 
{
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024;
    
    if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) 
    {
        $file_type = $_FILES['avatar']['type'];
        $file_size = $_FILES['avatar']['size'];
        
        if (in_array($file_type, $allowed_types)) 
        {
            if ($file_size <= $max_size) 
            {
                $file_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $file_ext;
                $target_path = $avatar_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) 
                {
                    if (isset($user['avatar_path'])) 
                    {
                        $old_avatar = $user['avatar_path'];
                    } 
                    else 
                    {
                        $old_avatar = '';
                    }

                    if ($old_avatar && $old_avatar !== 'img/no-image.png' && file_exists($old_avatar)) 
                    {
                        unlink($old_avatar);
                    }
                    
                    $update_sql = "UPDATE `users` SET `avatar_path` = '$target_path' WHERE `id` = $user_id";
                    
                    if ($conn->query($update_sql)) 
                    {
                        $_SESSION['success'] = "Аватар успешно обновлен";
                        $sql = "SELECT * FROM `users` WHERE `id` = $user_id";
                        $result = $conn->query($sql);
                        $user = $result->fetch_assoc();
                    } 
                    else 
                    {
                        $_SESSION['error'] = "Ошибка обновления аватара в базе данных: " . $conn->error;
                    }
                } 
                else 
                {
                    $_SESSION['error'] = "Ошибка загрузки файла";
                }
            } 
            else 
            {
                $_SESSION['error'] = "Размер файла не должен превышать 2MB";
            }
        } 
        else 
        {
            $_SESSION['error'] = "Допустимы только файлы JPEG, PNG или GIF";
        }
    } 
    else 
    {
        $_SESSION['error'] = "Ошибка загрузки файла: " . $_FILES['avatar']['error'];
    }
    
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="profile.php">
                <img class="logo" src="img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="profile.php">Главная</a>
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
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="<?php echo isset($user['avatar_path']) && !empty($user['avatar_path']) ? htmlspecialchars($user['avatar_path']) : 'img/no-image.png'; ?>" alt="Аватар" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h3><?php echo htmlspecialchars($user_name); ?></h3>
                        <p class="text-muted">
                            <?php 
                            if ($user_type == 'student') 
                            { 
                                echo 'Студент'; 
                            } 
                            else 
                            { 
                                echo 'Работодатель'; 
                            } 
                            ?>
                        </p>
                        <form method="POST" action="profile.php" enctype="multipart/form-data" class="mt-3">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Изменить аватар</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/jpeg,image/png,image/gif" required>
                                <div class="form-text">Макс. размер: 2MB. Допустимые форматы: JPG, PNG, GIF.</div>
                            </div>
                            <button type="submit" name="change_avatar" class="btn btn-primary btn-sm">Загрузить</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>Навигация</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="profile.php"><i class="bi bi-person me-2"></i>Профиль</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="files/my_works.php"><i class="bi bi-collection me-2"></i>Мои работы</a>
                            </li>
                            <?php 
                            if ($_SESSION['user_type'] == 'employer') 
                            {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="files/my_vacancies.php"><i class="bi bi-collection me-2"></i>Мои вакансии</a>
                            </li>
                            <?php 
                            }
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="files/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Основная информация</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])) 
                        {
                        ?>
                            <div class="alert alert-success alert-dismissible fade show text-center">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php 
                        }
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
                        <form method="POST" action="profile.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Имя</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user_phone); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тип аккаунта</label>
                                <input type="text" class="form-control" value="<?php if ($user_type == 'student') { echo 'Студент'; } else { echo 'Работодатель'; } ?>" disabled>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">Сохранить изменения</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Смена пароля</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="profile.php">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Текущий пароль</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Новый пароль</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Подтвердите новый пароль</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary">Изменить пароль</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    require_once("files/footer.php");
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>