<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['admin_id'])) 
{
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $admin_id = $_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    if (isset($_POST['moderate_portfolio'])) 
    {
        $work_id = (int)$_POST['work_id'];
        $status = $conn->real_escape_string($_POST['status']);
        
        if (isset($_POST['comment'])) 
        {
            $comment = $conn->real_escape_string($_POST['comment']);
        } 
        else 
        {
            $comment = $conn->real_escape_string('');
        }
        
        $conn->query("UPDATE `portfolio` SET `status` = '$status', `moderator_comment` = '$comment' WHERE `id` = $work_id");
        
        $action = "Модерация портфолио";
        $details = "ID работы: $work_id. Статус: $status. Комментарий: " . ($comment ?: 'нет');
        $conn->query("INSERT INTO `admin_actions` (`admin_id`, `action`, `details`, `ip_address`) VALUES ($admin_id, '$action', '$details', '$ip')");
        
        $_SESSION['admin_message'] = "Работа успешно отмодерирована";
        header("Location: admin_portfolio.php");
        exit();
    }
}

if (isset($_GET['search'])) 
{
    $search = $conn->real_escape_string($_GET['search']);
} 
else 
{
    $search = '';
}

if (isset($_GET['status'])) 
{
    $status_filter = $_GET['status'];
} 
else 
{
    $status_filter = 'pending';
}

if (isset($_GET['category'])) 
{
    $category_filter = (int)$_GET['category'];
} 
else 
{
    $category_filter = 0;
}

$where = [];

if ($search) 
{
    $where[] = "(p.title LIKE '%$search%' OR p.description LIKE '%$search%' OR p.tags LIKE '%$search%')";
}

if ($status_filter) 
{
    $where[] = "p.status = '$status_filter'";
}

if ($category_filter > 0) 
{
    $where[] = "p.category_id = $category_filter";
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$per_page = 15;

if (isset($_GET['page'])) 
{
    $page = (int)$_GET['page'];
} 
else 
{
    $page = 1;
}

$offset = ($page - 1) * $per_page;

$total_works = $conn->query("SELECT COUNT(*) FROM portfolio p $where_clause")->fetch_row()[0];
$total_pages = ceil($total_works / $per_page);

$categories = $conn->query("SELECT * FROM `portfolio_categories`");

$works = $conn->query("SELECT p.*, u.name as author_name, u.user_type, c.name as category_name FROM portfolio p JOIN users u ON p.user_id = u.id JOIN portfolio_categories c ON p.category_id = c.id $where_clause ORDER BY p.id ASC LIMIT $per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модерация портфолио | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body class="d-flex flex-column min-vh-100">
    
<div class="flex-grow-1">
    <?php 
        require_once('admin_header.php'); 
    ?>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Модерация портфолио</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="admin_portfolio.php?status=pending" class="btn btn-sm btn-outline-warning">На модерации</a>
                    <a href="admin_portfolio.php?status=approved" class="btn btn-sm btn-outline-success">Одобренные</a>
                    <a href="admin_portfolio.php?status=rejected" class="btn btn-sm btn-outline-danger">Отклоненные</a>
                </div>
            </div>
        </div>
        <?php 
        if (isset($_SESSION['admin_message'])) 
        {
        ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['admin_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php 
        unset($_SESSION['admin_message']); 
        } 
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="search" placeholder="Поиск по названию, описанию или тегам" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="category">
                            <option value="0">Все категории</option>
                            <?php 
                            while ($category = $categories->fetch_assoc()) 
                            {
                            ?>
                            <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                            <?php 
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>На модерации</option>
                            <option value="approved" <?= $status_filter == 'approved' ? 'selected' : '' ?>>Одобренные</option>
                            <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Отклоненные</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Применить</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Работа</th>
                                <th>Автор</th>
                                <th>Категория</th>
                                <th>Статус</th>
                                <th>Дата</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($work = $works->fetch_assoc()) 
                            {
                            ?>
                            <tr>
                                <td><?= $work['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($work['image_path'] ?: '../img/no-image.png') ?>" class="portfolio-img me-2" alt="<?= htmlspecialchars($work['title']) ?>">
                                        <div>
                                            <strong><?= htmlspecialchars($work['title']) ?></strong>
                                            <div class="text-muted small"><?= mb_substr(htmlspecialchars($work['description']), 0, 50) ?>...</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $work['user_type'] == 'employer' ? 'bg-warning text-dark' : 'bg-info' ?>">
                                        <?= htmlspecialchars($work['author_name']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($work['category_name']) ?></td>
                                <td>
                                    <?php 
                                    if ($work['status'] == 'pending')
                                    { 
                                    ?>
                                        <span class="badge badge-pending">На модерации</span>
                                    <?php 
                                    }
                                    else if ($work['status'] == 'approved') 
                                    {
                                    ?>
                                        <span class="badge badge-approved">Одобрено</span>
                                    <?php 
                                    }
                                    else 
                                    {
                                    ?>
                                        <span class="badge badge-rejected">Отклонено</span>
                                    <?php 
                                    } 
                                    ?>
                                </td>
                                <td><?= date('d.m.Y', strtotime($work['created_at'])) ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Действия
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="../files/portfolio.php?id=<?= $work['id'] ?>" target="_blank"><i class="bi bi-eye"></i> Просмотреть</a></li>
                                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#moderateModal<?= $work['id'] ?>"><i class="bi bi-check-circle"></i> Модерировать</button></li>
                                        </ul>
                                    </div>
                                    <div class="modal fade" id="moderateModal<?= $work['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Модерация работы</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST">
                                                    <input type="hidden" name="work_id" value="<?= $work['id'] ?>">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Название работы</label>
                                                            <input type="text" class="form-control" value="<?= htmlspecialchars($work['title']) ?>" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Автор</label>
                                                            <input type="text" class="form-control" value="<?= htmlspecialchars($work['author_name']) ?>" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Статус</label>
                                                            <select class="form-select" name="status" required>
                                                                <option value="approved" <?= $work['status'] == 'approved' ? 'selected' : '' ?>>Одобрить</option>
                                                                <option value="rejected" <?= $work['status'] == 'rejected' ? 'selected' : '' ?>>Отклонить</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="comment<?= $work['id'] ?>" class="form-label">Комментарий модератора</label>
                                                            <textarea class="form-control" id="comment<?= $work['id'] ?>" name="comment" rows="3"><?= htmlspecialchars($work['moderator_comment']) ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                        <button type="submit" name="moderate_portfolio" class="btn btn-primary">Сохранить</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php 
                if ($total_pages > 1) 
                {
                ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if ($page <= 1) { echo 'disabled'; } ?>">
                            <a class="page-link" href="admin_portfolio.php?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&status=<?= $status_filter ?>">Назад</a>
                        </li>
                        <?php 
                        for ($i = 1; $i <= $total_pages; $i++) 
                        {
                        ?>
                        <li class="page-item <?php if ($page == $i) { echo 'active'; } ?>">
                            <a class="page-link" href="admin_portfolio.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&status=<?= $status_filter ?>"><?= $i ?></a>
                        </li>
                        <?php 
                        } 
                        ?>
                        <li class="page-item <?php if ($page >= $total_pages) { echo 'disabled'; } ?>">
                            <a class="page-link" href="admin_portfolio.php?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&status=<?= $status_filter ?>">Вперед</a>
                        </li>
                    </ul>
                </nav>
                <?php 
                } 
                ?>
            </div>
        </div>
    </main>
</div>
    
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>