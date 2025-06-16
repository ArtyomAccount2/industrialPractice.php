<?php
session_start();
require_once("../config/link.php");

if (!isset($_GET['id'])) 
{
    header("Location: vacancies.php");
    exit();
}

if (isset($_SERVER['HTTP_REFERER'])) 
{
    $referer = $_SERVER['HTTP_REFERER'];
    
    if (strpos($referer, 'edit_vacancy.php') !== false || strpos($referer, 'company_vacancies.php') !== false) 
    {
        $_SESSION['previous_page'] = 'vacancies.php';
    } 
    else if (!strpos($referer, 'vacancy_details.php')) 
    {
        $_SESSION['previous_page'] = $referer;
    }
} 
else if (!isset($_SESSION['previous_page'])) 
{
    $_SESSION['previous_page'] = 'vacancies.php';
}

$vacancy_id = (int)$_GET['id'];
$isLoggedIn = isset($_SESSION['user_id']);

$sql = "SELECT v.*, u.name as company_name, u.email as company_email, u.phone as company_phone, c.name as category_name FROM vacancies v JOIN users u ON v.user_id = u.id JOIN vacancy_categories c ON v.category_id = c.id WHERE v.id = $vacancy_id";

$result = mysqli_query($conn, $sql);
$vacancy = mysqli_fetch_assoc($result);

if (!$vacancy) 
{
    header("Location: vacancies.php");
    exit();
}

if ($isLoggedIn) 
{
    $user_id = $_SESSION['user_id'];
    $check_view_sql = "SELECT * FROM `vacancy_views` WHERE `vacancy_id` = $vacancy_id AND `user_id` = $user_id";
    $check_view_result = mysqli_query($conn, $check_view_sql);
    
    if (mysqli_num_rows($check_view_result) == 0) 
    {
        $view_sql = "INSERT INTO `vacancy_views` (`vacancy_id`, `user_id`) VALUES ($vacancy_id, $user_id)";
        mysqli_query($conn, $view_sql);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($vacancy['title']) ?> | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php if ($isLoggedIn) { echo '../profile.php'; } else { echo '../index.php'; } ?>">
                <img class="logo" src="../img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                        <a class="nav-link" href="cooperation.php">Сотрудничество</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="vacancies.php">Вакансии</a>
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
                        <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Авторизация
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="bi bi-person-add me-2"></i>Регистрация
                        </button>
                    <?php 
                    } 
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <section class="vacancy-details py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mb-3">
                                <img src="<?= htmlspecialchars($vacancy['image_path']) ?>" class="img-thumbnail" style="max-height: 250px;">
                            </div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary"><?= htmlspecialchars($vacancy['category_name']) ?></span>
                                <p class="text-muted small border-0">Опубликовано: <?= date('d.m.Y', strtotime($vacancy['created_at'])) ?></p>
                            </div>
                            <h2 class="mb-3"><?= htmlspecialchars($vacancy['title']) ?></h2>
                            <div class="vacancy-meta mb-4">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <p class="mb-1"><i class="bi bi-building"></i> Компания:</p>
                                        <h5><?= htmlspecialchars($vacancy['company_name']) ?></h5>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <p class="mb-1"><i class="bi bi-geo-alt"></i> Местоположение:</p>
                                        <h5><?= htmlspecialchars($vacancy['location'] ?: 'Не указано') ?></h5>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <p class="mb-1"><i class="bi bi-cash-coin"></i> Зарплата:</p>
                                        <h5><?= $vacancy['salary'] ? htmlspecialchars($vacancy['salary']) . ' ₽' : 'По договорённости' ?></h5>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <p class="mb-1"><i class="bi bi-clock"></i> Тип занятости:</p>
                                        <h5>
                                            <?php 
                                            switch($vacancy['employment_type']) 
                                            {
                                                case 'full': echo 'Полная занятость'; break;
                                                case 'part': echo 'Частичная занятость'; break;
                                                case 'internship': echo 'Стажировка'; break;
                                                case 'remote': echo 'Удалённая работа'; break;
                                                default: echo 'Не указано';
                                            }
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="mb-3">Описание вакансии</h4>
                                <p><?= nl2br(htmlspecialchars(str_replace(['\r\n', '\n', '\r'], "\n", $vacancy['description']))) ?></p>
                            </div>
                            <div class="mb-4">
                                <h4 class="mb-3">Требования</h4>
                                <p><?= nl2br(htmlspecialchars(str_replace(['\r\n', '\n', '\r'], "\n", $vacancy['requirements']))) ?></p>
                            </div>
                            <?php 
                            if (!empty($vacancy['benefits'])) 
                            {
                            ?>
                                <div class="mb-4">
                                    <h4 class="mb-3">Условия и бонусы</h4>
                                    <p><?= nl2br(htmlspecialchars(str_replace(['\r\n', '\n', '\r'], "\n", $vacancy['benefits']))) ?></p>
                                </div>
                            <?php 
                            } 
                            ?>
                                <div class="mb-4">
                                    <h4 class="mb-3">Контактная информация</h4>
                                    <p><?= nl2br(htmlspecialchars(str_replace(['\r\n', '\n', '\r'], "\n", $vacancy['contacts']))) ?></p>
                                </div>
                            <?php 
                            if ($isLoggedIn && $_SESSION['user_type'] == 'student') 
                            {
                            ?>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#applyModal">
                                        <i class="bi bi-send"></i> Откликнуться на вакансию
                                    </button>
                                </div>
                            <?php 
                            } 
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">О компании</h4>
                            <p>Название: <strong><?= htmlspecialchars($vacancy['company_name']) ?></strong></p>
                            <?php 
                            if ($isLoggedIn && $_SESSION['user_type'] == 'employer' && $_SESSION['user_id'] == $vacancy['user_id'])
                            { 
                            ?>
                                <p>Email: <?= htmlspecialchars($vacancy['company_email']) ?></p>
                                <p>Телефон: <?= htmlspecialchars($vacancy['company_phone']) ?></p>
                                <div class="d-grid d-flex flex-column align-items-center gap-2 mt-3">
                                    <a href="edit_vacancy.php?id=<?= $vacancy['id'] ?>" class="btn btn-warning w-75">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                    <button class="btn btn-danger w-75" onclick="if(confirm('Вы уверены, что хотите удалить эту вакансию?')) window.location='delete_vacancy.php?id=<?= $vacancy['id'] ?>'">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                    <a href="<?= htmlspecialchars($_SESSION['previous_page']) ?>" class="btn btn-primary w-75">
                                        <i class="bi bi-arrow-left"></i> Назад
                                    </a>
                                </div>
                            <?php 
                            } 
                            else
                            {
                            ?>
                                <p>Телефон: <?= htmlspecialchars($vacancy['company_phone']) ?></p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Другие вакансии</h4>
                            <?php
                            $other_vacancies_sql = "SELECT v.id, v.title, v.created_at FROM vacancies v WHERE v.user_id = {$vacancy['user_id']} AND v.id != {$vacancy['id']} ORDER BY v.created_at DESC LIMIT 3";
                            $other_vacancies = mysqli_query($conn, $other_vacancies_sql);
                            
                            if (mysqli_num_rows($other_vacancies) > 0) 
                            {
                            ?>
                                <ul class="list-group list-group-flush">
                                    <?php 
                                    while($other = mysqli_fetch_assoc($other_vacancies)) 
                                    {
                                    ?>
                                        <li class="list-group-item">
                                            <a href="vacancy_details.php?id=<?= $other['id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($other['title']) ?>
                                                <p class="d-block text-muted small border-0"><?= date('d.m.Y', strtotime($other['created_at'])) ?></p>
                                            </a>
                                        </li>
                                    <?php 
                                    } 
                                    ?>
                                </ul>
                                <div class="mt-2">
                                    <a href="company_vacancies.php?company_id=<?= $vacancy['user_id'] ?>" class="btn btn-sm btn-outline-primary w-100">
                                        Все вакансии компании
                                    </a>
                                </div>
                            <?php 
                            }
                            else 
                            {
                            ?>
                                <p class="text-muted">Других вакансий пока нет</p>
                            <?php 
                            } 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php 
if ($isLoggedIn && $_SESSION['user_type'] == 'student') 
{
?>
<div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Отклик на вакансию</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="apply_vacancy.php">
                <input type="hidden" name="vacancy_id" value="<?= $vacancy['id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Вакансия</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($vacancy['title']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Компания</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($vacancy['company_name']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Сопроводительное письмо <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="resume" class="form-label">Резюме</label>
                        <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Отправить отклик</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
}
?>

<?php 
    require_once("modals.php");
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>