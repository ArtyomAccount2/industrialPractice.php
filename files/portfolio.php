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
                    if ($isLoggedIn)
                    {
                    ?>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addWorkModal">
                            <i class="bi bi-plus-circle"></i> Добавить работу
                        </button>
                    <?php 
                    }
                    else 
                    {
                    ?>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#loginModal">
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
                                    <h5 class="card-title"><?= htmlspecialchars($work['title']) ?></h5>
                                    <p class="card-text"><?= mb_substr(htmlspecialchars($work['description']), 0, 100) ?>...</p>
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
                                        <span class="badge bg-<?php if ($work['user_type'] == 'student') { echo 'info'; } else { echo 'warning'; } ?>">
                                            <?php if ($work['user_type'] == 'student') { echo 'Студент'; } else { echo 'Работодатель'; } ?>
                                        </span>
                                        <small class="text-muted"><?= htmlspecialchars($work['author_name']) ?></small>
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
                <h5 class="modal-title" id="addWorkModalLabel">Добавить работу в портфолио</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="add_work.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="workTitle" class="form-label">Название работы</label>
                        <input type="text" class="form-control" id="workTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="workCategory" class="form-label">Категория</label>
                        <select class="form-select" id="workCategory" name="category_id" required>
                            <?php 
                            foreach ($categories as $id => $name)
                            { 
                            ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                            <?php 
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="workDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="workDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="workTags" class="form-label">Теги (через запятую)</label>
                        <input type="text" class="form-control" id="workTags" name="tags" placeholder="Например: дизайн, логотип, брендинг">
                    </div>
                    <div class="mb-3">
                        <label for="workImage" class="form-label">Изображение работы</label>
                        <input type="file" class="form-control" id="workImage" name="image" accept="image/*" required>
                        <div class="form-text">Рекомендуемый размер: 1200x800px. Макс. размер: 5MB.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Внешние ссылки</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="bi bi-behance"></i></span>
                                    <input type="url" class="form-control" name="external_links[Behance]" placeholder="Ссылка на Behance">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="bi bi-github"></i></span>
                                    <input type="url" class="form-control" name="external_links[GitHub]" placeholder="Ссылка на GitHub">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="bi bi-dribbble"></i></span>
                                    <input type="url" class="form-control" name="external_links[Dribbble]" placeholder="Ссылка на Dribbble">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                    <input type="url" class="form-control" name="external_links[Website]" placeholder="Другая ссылка">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить работу</button>
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