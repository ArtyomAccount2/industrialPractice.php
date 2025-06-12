<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['admin_id'])) 
{
    header("Location: admin_login.php");
    exit();
}

if ($_SESSION['admin_role'] == 'moderator') 
{
    header("Location: ../admin.php");
    exit();
}

if (isset($_GET['search'])) 
{
    $search = $conn->real_escape_string($_GET['search']);
} 
else 
{
    $search = '';
}

if (isset($_GET['admin'])) 
{
    $admin_filter = (int)$_GET['admin'];
} 
else 
{
    $admin_filter = 0;
}

if (isset($_GET['date_from'])) 
{
    $date_from = $conn->real_escape_string($_GET['date_from']);
} 
else 
{
    $date_from = '';
}

if (isset($_GET['date_to'])) 
{
    $date_to = $conn->real_escape_string($_GET['date_to']);
} 
else 
{
    $date_to = '';
}

$where = [];

if ($search) 
{
    $where[] = "(a.action LIKE '%$search%' OR a.details LIKE '%$search%')";
}

if ($admin_filter > 0) 
{
    $where[] = "a.admin_id = $admin_filter";
}

if ($date_from) 
{
    $where[] = "a.created_at >= '$date_from 00:00:00'";
}

if ($date_to) 
{
    $where[] = "a.created_at <= '$date_to 23:59:59'";
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$per_page = 50;

if (isset($_GET['page'])) 
{
    $page = (int)$_GET['page'];
} 
else 
{
    $page = 1;
}

$offset = ($page - 1) * $per_page;

$total_logs = $conn->query("SELECT COUNT(*) FROM admin_actions a $where_clause")->fetch_row()[0];
$total_pages = ceil($total_logs / $per_page);

$admins = $conn->query("SELECT `id`, `name` FROM `admins` ORDER BY `name`");

$logs = $conn->query("SELECT a.*, ad.name as admin_name FROM admin_actions a JOIN admins ad ON a.admin_id = ad.id $where_clause ORDER BY a.id ASC LIMIT $per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Логи действий | СтудМаркет</title>
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
            <h1 class="h2">Логи действий администраторов</h1>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="Поиск по действию или деталям" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="admin">
                            <option value="0">Все администраторы</option>
                            <?php 
                            while ($admin = $admins->fetch_assoc()) 
                            {
                            ?>
                            <option value="<?= $admin['id'] ?>" <?= $admin_filter == $admin['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($admin['name']) ?>
                            </option>
                            <?php 
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_from" value="<?= htmlspecialchars($date_from) ?>" placeholder="От">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_to" value="<?= htmlspecialchars($date_to) ?>" placeholder="До">
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
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Дата</th>
                                <th>Администратор</th>
                                <th>Действие</th>
                                <th>Детали</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($log = $logs->fetch_assoc()) 
                            {
                            ?>
                            <tr>
                                <td><?= $log['id'] ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                                <td><?= htmlspecialchars($log['admin_name']) ?></td>
                                <td><?= htmlspecialchars($log['action']) ?></td>
                                <td><?= htmlspecialchars($log['details']) ?></td>
                                <td><?= htmlspecialchars($log['ip_address']) ?></td>
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
                            <a class="page-link" href="admin_logs.php?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&admin=<?= $admin_filter ?>&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>">Назад</a>
                        </li>
                        <?php 
                        for ($i = 1; $i <= $total_pages; $i++)
                        {
                        ?>
                        <li class="page-item <?php if ($page == $i) { echo 'active'; } ?>">
                            <a class="page-link" href="admin_logs.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&admin=<?= $admin_filter ?>&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>"><?= $i ?></a>
                        </li>
                        <?php 
                        } 
                        ?>
                        <li class="page-item <?php if ($page >= $total_pages) { echo 'disabled'; } ?>">
                            <a class="page-link" href="admin_logs.php?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&admin=<?= $admin_filter ?>&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>">Вперед</a>
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