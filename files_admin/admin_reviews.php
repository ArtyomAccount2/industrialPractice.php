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
    
    if (isset($_POST['moderate_review'])) 
    {
        $review_id = (int)$_POST['review_id'];
        $status = $conn->real_escape_string($_POST['status']);
        
        if (isset($_POST['comment'])) 
        {
            $comment = $conn->real_escape_string($_POST['comment']);
        } 
        else 
        {
            $comment = $conn->real_escape_string('');
        }
        
        $conn->query("UPDATE `reviews` SET status = '$status', `moderator_comment` = '$comment' WHERE `id` = $review_id");
        
        $action = "Модерация отзыва";
        $details = "ID отзыва: $review_id. Статус: $status. Комментарий: " . ($comment ?: 'нет');
        $conn->query("INSERT INTO `admin_actions` (`admin_id`, `action`, `details`, `ip_address`) VALUES ($admin_id, '$action', '$details', '$ip')");
        
        $_SESSION['admin_message'] = "Отзыв успешно отмодерирован";
        header("Location: admin_reviews.php");
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

$where = [];

if ($search) 
{
    $where[] = "(r.text LIKE '%$search%' OR u.name LIKE '%$search%')";
}

if ($status_filter) 
{
    $where[] = "r.status = '$status_filter'";
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

$total_reviews = $conn->query("SELECT COUNT(*) FROM reviews r $where_clause")->fetch_row()[0];
$total_pages = ceil($total_reviews / $per_page);

$reviews = $conn->query("SELECT r.*, u.name as author_name, u.user_type as author_type FROM reviews r JOIN users u ON r.user_id = u.id $where_clause ORDER BY r.id ASC LIMIT $per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модерация отзывов | СтудМаркет</title>
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
            <h1 class="h2">Модерация отзывов</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="admin_reviews.php?status=pending" class="btn btn-sm btn-outline-warning">На модерации</a>
                    <a href="admin_reviews.php?status=approved" class="btn btn-sm btn-outline-success">Одобренные</a>
                    <a href="admin_reviews.php?status=rejected" class="btn btn-sm btn-outline-danger">Отклоненные</a>
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
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="search" placeholder="Поиск по тексту отзыва или именам" value="<?= htmlspecialchars($search) ?>">
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
                                <th>Отзыв</th>
                                <th>Автор</th>
                                <th>Рейтинг</th>
                                <th>Статус</th>
                                <th>Дата</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($review = $reviews->fetch_assoc()) 
                            {
                            ?>
                            <tr>
                                <td><?= $review['id'] ?></td>
                                <td class="review-text"><?= htmlspecialchars($review['text']) ?></td>
                                <td>
                                    <span class="badge <?= $review['author_type'] == 'employer' ? 'bg-warning text-dark' : 'badge-author' ?>">
                                        <?= htmlspecialchars($review['author_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) 
                                    {
                                    ?>
                                        <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?> text-warning"></i>
                                    <?php 
                                    } 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($review['status'] == 'pending')
                                    {
                                    ?>
                                        <span class="badge badge-pending">На модерации</span>
                                    <?php 
                                    }
                                    else if ($review['status'] == 'approved')
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
                                <td><?= date('d.m.Y', strtotime($review['created_at'])) ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Действия
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $review['id'] ?>"><i class="bi bi-eye"></i> Просмотреть</button></li>
                                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#moderateModal<?= $review['id'] ?>"><i class="bi bi-check-circle"></i> Модерировать</button></li>
                                        </ul>
                                    </div>
                                    <div class="modal fade" id="reviewModal<?= $review['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Просмотр отзыва #<?= $review['id'] ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Автор</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($review['author_name']) ?>" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Рейтинг</label>
                                                        <div>
                                                            <?php 
                                                            for ($i = 1; $i <= 5; $i++) 
                                                            {
                                                            ?>
                                                                <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?> text-warning"></i>
                                                            <?php 
                                                            } 
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Текст отзыва</label>
                                                        <textarea class="form-control" rows="5" readonly><?= htmlspecialchars($review['text']) ?></textarea>
                                                    </div>
                                                    <?php 
                                                    if ($review['moderator_comment']) 
                                                    {
                                                    ?>
                                                    <div class="mb-3">
                                                        <label class="form-label">Комментарий модератора</label>
                                                        <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($review['moderator_comment']) ?></textarea>
                                                    </div>
                                                    <?php 
                                                    } 
                                                    ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="moderateModal<?= $review['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Модерация отзыва</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST">
                                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Автор</label>
                                                            <input type="text" class="form-control" value="<?= htmlspecialchars($review['author_name']) ?>" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Текст отзыва</label>
                                                            <textarea class="form-control" rows="5" readonly><?= htmlspecialchars($review['text']) ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Статус</label>
                                                            <select class="form-select" name="status" required>
                                                                <option value="approved" <?= $review['status'] == 'approved' ? 'selected' : '' ?>>Одобрить</option>
                                                                <option value="rejected" <?= $review['status'] == 'rejected' ? 'selected' : '' ?>>Отклонить</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="comment<?= $review['id'] ?>" class="form-label">Комментарий модератора</label>
                                                            <textarea class="form-control" id="comment<?= $review['id'] ?>" name="comment" rows="3"><?= htmlspecialchars($review['moderator_comment']) ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                        <button type="submit" name="moderate_review" class="btn btn-primary">Сохранить</button>
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
                            <a class="page-link" href="admin_reviews.php?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&status=<?= $status_filter ?>">Назад</a>
                        </li>
                        <?php 
                        for ($i = 1; $i <= $total_pages; $i++) 
                        {
                        ?>
                        <li class="page-item <?php if ($page == $i) { echo 'active'; } ?>">
                            <a class="page-link" href="admin_reviews.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&status=<?= $status_filter ?>"><?= $i ?></a>
                        </li>
                        <?php 
                        } 
                        ?>
                        <li class="page-item <?php if ($page >= $total_pages) { echo 'disabled'; } ?>">
                            <a class="page-link" href="admin_reviews.php?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&status=<?= $status_filter ?>">Вперед</a>
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