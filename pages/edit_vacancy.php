<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') 
{
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) 
{
    header("Location: my_vacancies.php");
    exit();
}

$vacancy_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$check_sql = "SELECT `id` FROM `vacancies` WHERE `id` = $vacancy_id AND `user_id` = $user_id";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) 
{
    header("Location: my_vacancies.php");
    exit();
}

$vacancy_sql = "SELECT * FROM `vacancies` WHERE `id` = $vacancy_id";
$vacancy_result = mysqli_query($conn, $vacancy_sql);
$vacancy = mysqli_fetch_assoc($vacancy_result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $title = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $employment_type = trim($_POST['employment_type']);
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $description = str_replace(['\r\n', '\n', '\r'], "\n", trim($_POST['description']));
    $requirements = str_replace(['\r\n', '\n', '\r'], "\n", trim($_POST['requirements']));
    $benefits = str_replace(['\r\n', '\n', '\r'], "\n", trim($_POST['benefits'] ?? ''));
    $contacts = str_replace(['\r\n', '\n', '\r'], "\n", trim($_POST['contacts']));

    $title = mysqli_real_escape_string($conn, $title);
    $employment_type = mysqli_real_escape_string($conn, $employment_type);
    $salary = mysqli_real_escape_string($conn, $salary);
    $location = mysqli_real_escape_string($conn, $location);
    $description = mysqli_real_escape_string($conn, $description);
    $requirements = mysqli_real_escape_string($conn, $requirements);
    $benefits = mysqli_real_escape_string($conn, $benefits);
    $contacts = mysqli_real_escape_string($conn, $contacts);

    $image_path = $vacancy['image_path'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) 
    {
        $image_dir = "../uploads/vacancies/";

        if (!file_exists($image_dir)) 
        {
            mkdir($image_dir, 0777, true);
        }

        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $max_size = 2 * 1024 * 1024;

        if (in_array($file_type, $allowed_types)) 
        {
            if ($file_size <= $max_size) 
            {
                if ($image_path && file_exists($image_path)) 
                {
                    unlink($image_path);
                }

                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'vacancy_' . $user_id . '_' . time() . '.' . $file_ext;
                $target_path = $image_dir . $new_filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) 
                {
                    $image_path = $target_path;
                } 
                else 
                {
                    $_SESSION['error'] = "Ошибка при загрузке изображения";
                    header("Location: edit_vacancy.php?id=$vacancy_id");
                    exit();
                }
            } 
            else 
            {
                $_SESSION['error'] = "Размер изображения не должен превышать 2MB";
                header("Location: edit_vacancy.php?id=$vacancy_id");
                exit();
            }
        } 
        else 
        {
            $_SESSION['error'] = "Допустимы только файлы JPG или PNG";
            header("Location: edit_vacancy.php?id=$vacancy_id");
            exit();
        }
    }

    $update_sql = "UPDATE `vacancies` SET `title` = ?, `category_id` = ?, `employment_type` = ?, `salary` = ?, `location` = ?, `description` = ?, `requirements` = ?, `benefits` = ?, `contacts` = ?, `image_path` = ? WHERE `id` = ? AND `user_id` = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "sissssssssii", $title, $category_id, $employment_type, $salary, $location, $description, $requirements, $benefits, $contacts, $image_path, $vacancy_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) 
    {
        $_SESSION['success'] = "Вакансия успешно обновлена";
        header("Location: my_vacancies.php");
    } 
    else 
    {
        $_SESSION['error'] = "Ошибка при обновлении вакансии: " . mysqli_error($conn);
        header("Location: edit_vacancy.php?id=$vacancy_id");
    }

    exit();
}

$categories = mysqli_query($conn, "SELECT * FROM `vacancy_categories`");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать вакансию | СтудМаркет</title>
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
                        <a class="nav-link" href="my_vacancies.php">Мои вакансии</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="edit_vacancy.php">Редактировать вакансию</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../files/logout.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Выйти
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="edit-vacancy py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mb-0">Редактировать вакансию</h3>
                        </div>
                        <div class="card-body">
                            <?php 
                            if (isset($_SESSION['error']))
                            {
                            ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php 
                            } 
                            ?>
                            <form method="POST" action="edit_vacancy.php?id=<?= $vacancy_id ?>" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Название вакансии <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($vacancy['title']) ?>" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Категория <span class="text-danger">*</span></label>
                                        <select class="form-select" id="category" name="category_id" required>
                                            <?php 
                                            while ($cat = mysqli_fetch_assoc($categories))
                                            { 
                                            ?>
                                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $vacancy['category_id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php 
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="employment" class="form-label">Тип занятости <span class="text-danger">*</span></label>
                                        <select class="form-select" id="employment" name="employment_type" required>
                                            <option value="full" <?= $vacancy['employment_type'] == 'full' ? 'selected' : '' ?>>Полная занятость</option>
                                            <option value="part" <?= $vacancy['employment_type'] == 'part' ? 'selected' : '' ?>>Частичная занятость</option>
                                            <option value="internship" <?= $vacancy['employment_type'] == 'internship' ? 'selected' : '' ?>>Стажировка</option>
                                            <option value="remote" <?= $vacancy['employment_type'] == 'remote' ? 'selected' : '' ?>>Удалённая работа</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="salary" class="form-label">Зарплата</label>
                                        <input type="text" class="form-control" id="salary" name="salary" value="<?= htmlspecialchars($vacancy['salary']) ?>" placeholder="Например: 30000-50000">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Местоположение</label>
                                        <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($vacancy['location']) ?>" placeholder="Город или адрес">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Описание вакансии <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($vacancy['description']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="requirements" class="form-label">Требования <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="requirements" name="requirements" rows="4" required><?= htmlspecialchars($vacancy['requirements']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="benefits" class="form-label">Условия и бонусы</label>
                                    <textarea class="form-control" id="benefits" name="benefits" rows="3"><?= htmlspecialchars($vacancy['benefits']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="contacts" class="form-label">Контактная информация <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="contacts" name="contacts" rows="2" required><?= htmlspecialchars($vacancy['contacts']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Изображение</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <div class="form-text">Макс. размер: 2MB. Допустимые форматы: JPG, PNG.</div>
                                    <?php 
                                    if ($vacancy['image_path']) 
                                    {
                                    ?>
                                        <div class="mt-2">
                                            <p>Текущее изображение:</p>
                                            <img src="<?= htmlspecialchars($vacancy['image_path']) ?>" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    <?php 
                                    } 
                                    ?>
                                </div>
                                <div class="d-grid d-flex justify-content-center gap-2">
                                    <button type="submit" class="btn btn-primary w-50">Сохранить изменения</button>
                                    <a href="vacancy_details.php?id=<?= $vacancy_id ?>" class="btn btn-secondary w-50">Отмена</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
                    <li class="mb-2"><a href="my_vacancies.php" class="text-white text-decoration-none">Мои вакансии</a></li>
                    <li><a href="edit_vacancy.php" class="text-white text-decoration-none">Редактировать вакансию</a></li>
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