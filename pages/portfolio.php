<?php
session_start();
require_once("../config/link.php");

$isLoggedIn = isset($_SESSION['user_id']);

$categories = [];
$sql_categories = "SELECT * FROM `portfolio_categories`";
$result_categories = mysqli_query($conn, $sql_categories);

while ($row = mysqli_fetch_assoc($result_categories)) 
{
    $categories[$row['id']] = $row['name'];
}

if (isset($_GET['page'])) 
{
    $current_page = (int)$_GET['page'];
} 
else 
{
    $current_page = 1;
}

if (isset($_GET['category'])) 
{
    $category_filter = (int)$_GET['category'];
} 
else 
{
    $category_filter = 0;
}

if (isset($_GET['search'])) 
{
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
} 
else 
{
    $search_query = '';
}

if ($current_page < 1) 
{
    $current_page = 1;
}

$limit = 9;
$offset = ($current_page - 1) * $limit;

$sql_where = "WHERE 1=1";

if ($category_filter > 0) 
{
    $sql_where .= " AND p.category_id = $category_filter";
}

if (!empty($search_query)) 
{
    $sql_where .= " AND (p.title LIKE '%$search_query%' OR p.description LIKE '%$search_query%' OR p.tags LIKE '%$search_query%')";
}

$sql = "SELECT p.*, u.name as author_name, u.user_type, (SELECT COUNT(*) FROM portfolio_views WHERE work_id = p.id) as views_count, (SELECT COUNT(*) FROM portfolio_likes WHERE work_id = p.id) as likes_count FROM portfolio p JOIN users u ON p.user_id = u.id $sql_where ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
$portfolio_items = mysqli_query($conn, $sql);

$sql_total = "SELECT COUNT(*) as count FROM portfolio p $sql_where";
$total_result = mysqli_query($conn, $sql_total);
$total_items = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_items / $limit);

if ($current_page > $total_pages && $total_pages > 0) 
{
    $current_page = $total_pages;
}

if ($isLoggedIn && isset($_GET['action']) && $_GET['action'] == 'like') 
{
    $work_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    $check_sql = "SELECT * FROM `portfolio_likes` WHERE `work_id` = $work_id AND `user_id` = $user_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) == 0) 
    {
        $like_sql = "INSERT INTO `portfolio_likes` (`work_id`, `user_id`) VALUES ($work_id, $user_id)";
        mysqli_query($conn, $like_sql);
        
        $view_check_sql = "SELECT * FROM `portfolio_views` WHERE `work_id` = $work_id AND `user_id` = $user_id";
        $view_check_result = mysqli_query($conn, $view_check_sql);
        
        if (mysqli_num_rows($view_check_result) == 0) 
        {
            $view_sql = "INSERT INTO `portfolio_views` (`work_id`, `user_id`) VALUES ($work_id, $user_id)";
            mysqli_query($conn, $view_sql);
        }
    }
    
    header("Location: portfolio.php?page=$current_page" . ($category_filter ? "&category=$category_filter" : "") . (!empty($search_query) ? "&search=$search_query" : ""));
    exit();
}

if ($isLoggedIn && !isset($_GET['action'])) 
{
    $user_id = $_SESSION['user_id'];
    
    while ($work = mysqli_fetch_assoc($portfolio_items)) 
    {
        $work_id = $work['id'];
        
        $view_check_sql = "SELECT * FROM `portfolio_views` WHERE `work_id` = $work_id AND `user_id` = $user_id";
        $view_check_result = mysqli_query($conn, $view_check_sql);
        
        if (mysqli_num_rows($view_check_result) == 0) 
        {
            $view_sql = "INSERT INTO `portfolio_views` (`work_id`, `user_id`) VALUES ($work_id, $user_id)";
            mysqli_query($conn, $view_sql);
        }
    }
    
    mysqli_data_seek($portfolio_items, 0);
}

$sql_where = "WHERE p.status = 'approved'";

if ($isLoggedIn && ($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'moderator')) 
{
    $sql_where = "WHERE 1=1";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Портфолио | СтудМаркет</title>
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
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
                        <a class="nav-link active" aria-current="page" href="portfolio.php">Портфолио</a>
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
                <div class="d-flex">
                    <?php 
                    if ($isLoggedIn)
                    {
                    ?>
                        <a href="../files/logout.php" class="btn btn-outline-danger">
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

    <section class="portfolio-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Студенческие работы</h2>
                <p class="lead text-muted">Лучшие проекты наших студентов</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" action="portfolio.php" class="row g-2">
                        <div class="col-md-5">
                            <select class="form-select" name="category">
                                <option value="0">Все категории</option>
                                <?php 
                                foreach ($categories as $id => $name)
                                {
                                ?>
                                    <option value="<?php echo $id; ?>" <?php if ($category_filter == $id) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($name); ?>
                                    </option>
                                <?php 
                                } 
                                ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search" placeholder="Поиск по работам..." value="<?= htmlspecialchars($search_query) ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Применить</button>
                        </div>
                    </form>
                    <div class="col-md-12 mt-3 text-center h-50">
                    <?php 
                    if ($isLoggedIn && $_SESSION['user_type'] == 'student')
                    {
                    ?>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addWorkModal">
                            <i class="bi bi-plus-circle"></i> Добавить работу
                        </button>
                    <?php 
                    }
                    ?>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <?php 
                if (mysqli_num_rows($portfolio_items) > 0)
                {
                ?>
                    <?php 
                    while ($work = mysqli_fetch_assoc($portfolio_items))
                    { 
                    ?>
                        <div class="col-md-4">
                            <div class="card portfolio-item h-100">
                                <img src="<?= htmlspecialchars($work['image_path'] ?: '../img/no-image.png') ?>" class="card-img-top portfolio-img" alt="<?= htmlspecialchars($work['title']) ?>">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge badge-category"><?= htmlspecialchars($categories[$work['category_id']] ?? 'Без категории') ?></span>
                                        <small class="text-muted"><?= date('d.m.Y', strtotime($work['created_at'])) ?></small>
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($work['title'], ENT_QUOTES, 'UTF-8', false) ?></h5>
                                    <p class="card-text"><?= mb_substr(htmlspecialchars($work['description'], ENT_QUOTES, 'UTF-8', false), 0, 100) ?>...</p>
                                    <?php 
                                    if (!empty($work['tags']))
                                    { 
                                    ?>
                                        <div class="mb-3">
                                            <?php 
                                            $tags = explode(',', $work['tags']);

                                            foreach ($tags as $tag)
                                            {
                                                if (!empty(trim($tag)))
                                                {
                                            ?>
                                                <span class="badge bg-light text-dark me-1"><?= htmlspecialchars(trim($tag)) ?></span>
                                            <?php 
                                                }
                                            } 
                                            ?>
                                        </div>
                                    <?php 
                                    } 

                                    if (!empty($work['external_links']))
                                    {
                                    ?>
                                        <div class="mb-3">
                                            <?php 
                                            $links = json_decode($work['external_links'], true);

                                            foreach ($links as $platform => $url)
                                            {
                                                if (!empty($url))
                                                {
                                            ?>
                                                <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-1">
                                                    <i class="bi bi-<?= strtolower($platform) ?>"></i> <?= htmlspecialchars($platform) ?>
                                                </a>
                                            <?php 
                                                }
                                            }
                                            ?>
                                        </div>
                                    <?php 
                                    } 
                                    ?>
                                    <div class="step-card d-flex justify-content-between align-items-center">
                                        <span class="badge bg-<?php if ($work['user_type'] == 'student') { echo 'info'; } ?>">
                                            <?php 
                                            if ($work['user_type'] == 'student') 
                                            { 
                                                echo 'Студент'; 
                                            } 
                                            ?>
                                        </span>
                                        <p class="text-muted small border-0"><?= htmlspecialchars($work['author_name']) ?></p>
                                    </div>
                                    <div class="stats d-flex justify-content-center align-items-center w-100 mt-2">
                                        <span class="text-muted me-2" title="Просмотры">
                                            <i class="bi bi-eye stats-icon"></i> <?= $work['views_count'] ?>
                                        </span>
                                        <span class="like-btn <?php if ($isLoggedIn) echo 'clickable'; ?>" title="Лайки"
                                        <?php 
                                        if ($isLoggedIn)
                                        {
                                        ?>
                                            onclick="window.location.href='portfolio.php?action=like&id=<?= $work['id'] ?>&page=<?= $current_page ?><?= $category_filter ? '&category='.$category_filter : '' ?><?= !empty($search_query) ? '&search='.$search_query : '' ?>'"
                                        <?php 
                                        }
                                        else
                                        { 
                                        ?>
                                            data-bs-toggle="modal" data-bs-target="#loginModal"
                                        <?php 
                                        } 
                                        ?>>
                                            <i class="bi bi-heart<?= $isLoggedIn && isset($work['user_liked']) && $work['user_liked'] ? '-fill' : '' ?> stats-icon"></i> <?= $work['likes_count'] ?>
                                        </span>
                                    </div>
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
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search" style="font-size: 3rem;"></i>
                        <h4>Работ не найдено</h4>
                        <p>Попробуйте изменить параметры поиска или добавьте свою работу</p>
                    </div>
                <?php 
                } 
                ?>
            </div>
            <?php if ($total_pages > 1)
            {
            ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if ($current_page == 1) { echo 'disabled'; } ?>">
                            <a class="page-link" href="portfolio.php?page=<?= $current_page - 1 ?><?= $category_filter ? '&category='.$category_filter : '' ?><?= !empty($search_query) ? '&search='.$search_query : '' ?>">Назад</a>
                        </li>
                        <?php 
                        for ($i = 1; $i <= $total_pages; $i++)
                        { 
                        ?>
                            <li class="page-item <?php if ($current_page == $i) { echo 'active'; } ?>">
                                <a class="page-link" href="portfolio.php?page=<?= $i ?><?= $category_filter ? '&category='.$category_filter : '' ?><?= !empty($search_query) ? '&search='.$search_query : '' ?>"><?= $i ?></a>
                            </li>
                        <?php 
                        } 
                        ?>
                        <li class="page-item <?php if ($current_page >= $total_pages) { echo 'disabled'; } ?>">
                            <a class="page-link" href="portfolio.php?page=<?= $current_page + 1 ?><?= $category_filter ? '&category='.$category_filter : '' ?><?= !empty($search_query) ? '&search='.$search_query : '' ?>">Вперед</a>
                        </li>
                    </ul>
                </nav>
            <?php 
            } 
            ?>
        </div>
    </section>
</div>

<?php 
if ($isLoggedIn)
{
?>
<div class="modal fade" id="addWorkModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWorkModalLabel">Мастер добавления работы</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../files/add_work.php" enctype="multipart/form-data" id="portfolioForm">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <ul class="nav nav-pills mb-4" id="portfolioWizard" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="step1-tab" type="button" role="tab">1. Основное</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" id="step2-tab" type="button" role="tab">2. Описание</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" id="step3-tab" type="button" role="tab">3. Медиа</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" id="step4-tab" type="button" role="tab">4. Дополнительно</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="portfolioWizardContent">
                        <div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                            <div class="mb-3">
                                <label for="workTitle" class="form-label">Название работы <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="workTitle" name="title" placeholder="Например: Логотип для кафе 'Уют'" data-bs-toggle="tooltip" data-bs-placement="top" title="Придумайте краткое и понятное название, отражающее суть работы" required>
                                <div class="form-text">От 5 до 100 символов</div>
                                <div class="invalid-feedback">Пожалуйста, укажите название работы</div>
                            </div>
                            <div class="mb-3">
                                <label for="workCategory" class="form-label">Категория <span class="text-danger">*</span></label>
                                <select class="form-select" id="workCategory" name="category_id" data-bs-toggle="tooltip" data-bs-placement="top" title="Выберите наиболее подходящую категорию для вашей работы" required>
                                    <option value="" selected disabled>-- Выберите категорию --</option>
                                    <?php 
                                    foreach ($categories as $id => $name)
                                    {
                                    ?>
                                        <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                                    <?php 
                                    } 
                                    ?>
                                </select>
                                <div class="invalid-feedback">Пожалуйста, выберите категорию</div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary next-step" data-next="step2">Далее <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                            <div class="mb-3">
                                <label for="workDescription" class="form-label">Описание работы <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="workDescription" name="description" rows="5" placeholder="Опишите вашу работу: цели, задачи, процесс создания, использованные технологии..." data-bs-toggle="tooltip" data-bs-placement="top" title="Подробно опишите вашу работу, чтобы другие пользователи могли понять её ценность" required></textarea>
                                <div class="form-text">
                                    <span id="descriptionCounter">0</span>/1000 символов. Минимум 50 символов.
                                </div>
                                <div class="invalid-feedback">Описание должно содержать не менее 50 символов</div>
                            </div>
                            <div class="mb-3">
                                <label for="workTags" class="form-label">Теги (через запятую) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="workTags" name="tags" placeholder="#дизайн, #логотип, #брендинг" data-bs-toggle="tooltip" data-bs-placement="top" title="Укажите ключевые слова через запятую, которые помогут найти вашу работу" required>
                                <div class="form-text">До 5 тегов, каждый не длиннее 20 символов</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary prev-step" data-prev="step1"><i class="bi bi-arrow-left"></i> Назад</button>
                                <button type="button" class="btn btn-primary next-step" data-next="step3">Далее <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab">
                            <div class="mb-3">
                                <label for="workImage" class="form-label">Главное изображение <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="workImage" name="image" accept="image/*" data-bs-toggle="tooltip" data-bs-placement="top" title="Загрузите основное изображение, которое будет отображаться в галерее" required>
                                <div class="form-text">Форматы: JPG, PNG. Макс. размер: 5MB. Рекомендуемое разрешение: 1200x800px</div>
                                <div class="invalid-feedback">Пожалуйста, загрузите изображение</div>
                                <div class="mt-3 text-center" id="imagePreviewContainer" style="display:none;">
                                    <img id="imagePreview" src="#" alt="Предпросмотр" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary prev-step" data-prev="step2"><i class="bi bi-arrow-left"></i> Назад</button>
                                <button type="button" class="btn btn-primary next-step" data-next="step4">Далее <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                            <div class="mb-3">
                                <label class="form-label">Внешние ссылки</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-behance"></i></span>
                                            <input type="url" class="form-control" name="external_links[Behance]" placeholder="https://www.behance.net/вашпроект">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-github"></i></span>
                                            <input type="url" class="form-control" name="external_links[GitHub]" placeholder="https://github.com/вашпроект">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-dribbble"></i></span>
                                            <input type="url" class="form-control" name="external_links[Dribbble]" placeholder="https://dribbble.com/вашпроект">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                            <input type="url" class="form-control" name="external_links[Website]" placeholder="https://вашсайт.com/проект">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">Укажите ссылки на другие платформы, где представлена ваша работа</div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeTerms" name="agree_terms" required>
                                <label class="form-check-label" for="agreeTerms">Я подтверждаю, что это моя оригинальная работа и она не нарушает авторские права третьих лиц</label>
                                <div class="invalid-feedback">Необходимо подтверждение</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary prev-step" data-prev="step3"><i class="bi bi-arrow-left"></i> Назад</button>
                                <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Добавить работу</button>
                            </div>
                        </div>
                    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

    tooltipTriggerList.map(function (tooltipTriggerEl) 
    {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    let nextButtons = document.querySelectorAll('.next-step');
    let prevButtons = document.querySelectorAll('.prev-step');
    
    let currentStep = 1;
    let totalSteps = 4;
    
    function goToStep(step) 
    {
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        document.getElementById(`step${step}`).classList.add('show', 'active');
        
        document.getElementById(`step${step}-tab`).classList.add('active');
        
        currentStep = step;
    }
    
    nextButtons.forEach(button => {
        button.addEventListener('click', function() 
        {
            if (validateStep(`step${currentStep}`)) 
            {
                let nextStep = currentStep + 1;

                if (nextStep <= totalSteps) 
                {
                    document.getElementById(`step${nextStep}-tab`).classList.remove('disabled');
                    goToStep(nextStep);
                }
            }
        });
    });
    
    prevButtons.forEach(button => {
        button.addEventListener('click', function() 
        {
            let prevStep = currentStep - 1;

            if (prevStep >= 1) 
            {
                goToStep(prevStep);
            }
        });
    });
    
    function validateStep(stepId) 
    {
        let isValid = true;
        let stepElement = document.getElementById(stepId);
        let requiredFields = stepElement.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) 
            {
                field.classList.add('is-invalid');
                isValid = false;
            } 
            else 
            {
                field.classList.remove('is-invalid');
            }
        });

        if (stepId === 'step1') 
        {
            let title = document.getElementById('workTitle');

            if (title.value.length < 5 || title.value.length > 100) 
            {
                title.classList.add('is-invalid');
                isValid = false;
            }
        } 
        else if (stepId === 'step2') 
        {
            let description = document.getElementById('workDescription');

            if (description.value.length < 50) 
            {
                description.classList.add('is-invalid');
                isValid = false;
            }
        } 
        else if (stepId === 'step3') 
        {
            let imageInput = document.getElementById('workImage');

            if (imageInput.files.length === 0) 
            {
                imageInput.classList.add('is-invalid');
                isValid = false;
            }
        }
        
        return isValid;
    }

    let descriptionField = document.getElementById('workDescription');
    let counter = document.getElementById('descriptionCounter');
    
    if (descriptionField && counter) 
    {
        descriptionField.addEventListener('input', function() 
        {
            counter.textContent = this.value.length;

            if (this.value.length < 50) 
            {
                this.classList.add('is-invalid');
            } 
            else 
            {
                this.classList.remove('is-invalid');
            }
        });
    }

    let imageInput = document.getElementById('workImage');
    let previewContainer = document.getElementById('imagePreviewContainer');
    let preview = document.getElementById('imagePreview');
    
    if (imageInput && previewContainer && preview) 
    {
        imageInput.addEventListener('change', function() 
        {
            if (this.files && this.files[0]) 
            {
                let reader = new FileReader();
                
                reader.onload = function(e) 
                {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    this.classList.remove('is-invalid');
                }.bind(this);
                
                reader.readAsDataURL(this.files[0]);
            } 
            else 
            {
                previewContainer.style.display = 'none';
            }
        });
    }

    let form = document.getElementById('portfolioForm');

    if (form) 
    {
        form.addEventListener('submit', function(e) 
        {
            let allValid = true;
            let steps = ['step1', 'step2', 'step3', 'step4'];
            let firstInvalidStep = null;
            
            steps.forEach(step => {
                if (!validateStep(step)) 
                {
                    allValid = false;

                    if (!firstInvalidStep) 
                    {
                        firstInvalidStep = step;
                    }
                }
            });
            
            if (!allValid) 
            {
                e.preventDefault();
                document.getElementById(firstInvalidStep + '-tab').click();

                let invalidField = document.querySelector('#' + firstInvalidStep + ' .is-invalid');

                if (invalidField) 
                {
                    invalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    if (invalidField.tagName === 'INPUT' || invalidField.tagName === 'TEXTAREA' || invalidField.tagName === 'SELECT') 
                    {
                        invalidField.focus();
                    }
                }
                
                alert('Пожалуйста, заполните все обязательные поля корректно.');
            }
        });
    }
    
    let workTitle = document.getElementById('workTitle');

    if (workTitle) 
    {
        workTitle.addEventListener('input', function() 
        {
            if (this.value.length >= 5 && this.value.length <= 100) 
            {
                this.classList.remove('is-invalid');
            } 
            else 
            {
                this.classList.add('is-invalid');
            }
        });
    }
    
    let workCategory = document.getElementById('workCategory');

    if (workCategory) 
    {
        workCategory.addEventListener('change', function() 
        {
            if (this.value) 
            {
                this.classList.remove('is-invalid');
            } 
            else 
            {
                this.classList.add('is-invalid');
            }
        });
    }
    
    let workTags = document.getElementById('workTags');

    if (workTags) 
    {
        workTags.addEventListener('input', function() 
        {

            if (this.value.trim()) 
            {
                this.classList.remove('is-invalid');
            } 
            else 
            {
                this.classList.add('is-invalid');
            }
        });
    }
    
    let agreeTerms = document.getElementById('agreeTerms');

    if (agreeTerms) 
    {
        agreeTerms.addEventListener('change', function() 
        {
            if (this.checked) 
            {
                this.classList.remove('is-invalid');
            } 
            else 
            {
                this.classList.add('is-invalid');
            }
        });
    }
});
</script>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>