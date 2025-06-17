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

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $admin_id = $_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    if (isset($_POST['toggle_ban'])) 
    {
        $user_id = (int)$_POST['user_id'];
        $reason = $conn->real_escape_string($_POST['reason'] ?? '');
        
        $user = $conn->query("SELECT `is_banned` FROM `users` WHERE `id` = $user_id")->fetch_assoc();
        
        if ($user['is_banned']) 
        {
            $new_status = 0;
        } 
        else 
        {
            $new_status = 1;
        }
        
        $conn->query("UPDATE `users` SET `is_banned` = $new_status, `ban_reason` = '$reason' WHERE `id` = $user_id");
        
        if ($new_status) 
        {
            $action = 'Блокировка пользователя';
        } 
        else 
        {
            $action = 'Разблокировка пользователя';
        }

        $details = "ID пользователя: $user_id. Причина: " . ($reason ?: 'не указана');
        $conn->query("INSERT INTO `admin_actions` (`admin_id`, `action`, `details`, `ip_address`) VALUES ($admin_id, '$action', '$details', '$ip')");
        
        if ($new_status) 
        {
            $_SESSION['admin_message'] = 'Пользователь заблокирован';
        } 
        else 
        {
            $_SESSION['admin_message'] = 'Пользователь разблокирован';
        }

        header("Location: admin_users.php");
        exit();
    }
 
    if (isset($_POST['verify_employer'])) 
    {
        $user_id = (int)$_POST['user_id'];
        
        $conn->query("UPDATE `users` SET `is_verified` = 1 WHERE `id` = $user_id AND `user_type` = 'employer'");
        
        $action = 'Верификация работодателя';
        $details = "ID пользователя: $user_id";
        $conn->query("INSERT INTO `admin_actions` (`admin_id`, `action`, `details`, `ip_address`) VALUES ($admin_id, '$action', '$details', '$ip')");
        
        $_SESSION['admin_message'] = 'Работодатель верифицирован';
        header("Location: admin_users.php");
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

if (isset($_GET['type'])) 
{
    $type = $_GET['type'];
} 
else 
{
    $type = '';
}

if (isset($_GET['status'])) 
{
    $status = $_GET['status'];
} 
else 
{
    $status = '';
}

$where = [];

if ($search) 
{
    $where[] = "(name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}

if ($type) 
{
    $where[] = "`user_type` = '$type'";
}

if ($status == 'banned') 
{
    $where[] = "`is_banned` = 1";
} 
else if ($status == 'active') 
{
    $where[] = "`is_banned` = 0";
}

if ($status == 'unverified') 
{
    $where[] = "`user_type` = 'employer' AND `is_verified` = 0";
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$per_page = 20;

if (isset($_GET['page'])) 
{
    $page = (int)$_GET['page'];
} 
else 
{
    $page = 1;
}

$offset = ($page - 1) * $per_page;

$total_users = $conn->query("SELECT COUNT(*) FROM `users` $where_clause")->fetch_row()[0];
$total_pages = ceil($total_users / $per_page);

$users = $conn->query("SELECT * FROM `users` $where_clause ORDER BY `id` ASC LIMIT $per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями | СтудМаркет</title>
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
            <h1 class="h2">Управление пользователями</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="admin_users.php" class="btn btn-sm btn-outline-secondary">Все пользователи</a>
                    <a href="admin_users.php?status=unverified" class="btn btn-sm btn-outline-warning">Неверифицированные</a>
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
                        <input type="text" class="form-control" name="search" placeholder="Поиск по имени, email или телефону" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="type">
                            <option value="">Все типы</option>
                            <option value="student" <?= $type == 'student' ? 'selected' : '' ?>>Студенты</option>
                            <option value="employer" <?= $type == 'employer' ? 'selected' : '' ?>>Работодатели</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">Все статусы</option>
                            <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Активные</option>
                            <option value="banned" <?= $status == 'banned' ? 'selected' : '' ?>>Заблокированные</option>
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
                                <th>Пользователь</th>
                                <th>Email</th>
                                <th>Телефон</th>
                                <th>Тип</th>
                                <th>Статус</th>
                                <th>Дата регистрации</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($user = $users->fetch_assoc()) 
                            {
                            ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        if (!empty($user['avatar_path'])) 
                                        {
                                            $avatarPath = $user['avatar_path'];
                                        } 
                                        else 
                                        {
                                            $avatarPath = '../img/no-image.png';
                                        }

                                        if (!empty($user['avatar_path']) && strpos($user['avatar_path'], '../') === false) 
                                        {
                                            $avatarPath = '../' . $user['avatar_path'];
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($avatarPath) ?>" class="user-avatar me-2" alt="Аватар">
                                        <?= htmlspecialchars($user['name']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td>
                                    <span class="badge rounded-pill <?= $user['user_type'] == 'employer' ? 'badge-employer' : 'badge-student' ?>">
                                        <?= $user['user_type'] == 'employer' ? 'Работодатель' : 'Студент' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if ($user['is_banned'])
                                    { 
                                    ?>
                                        <span class="badge rounded-pill badge-banned">Заблокирован</span>
                                    <?php 
                                    }
                                    else if ($user['user_type'] == 'employer')
                                    {
                                    ?>
                                        <span class="badge rounded-pill badge-verified">Верифицирован</span>
                                    <?php 
                                    }
                                    else 
                                    {
                                    ?>
                                        <span class="badge rounded-pill bg-success">Активен</span>
                                    <?php 
                                    } 
                                    ?>
                                </td>
                                <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Действия
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="../profile.php?id=<?= $user['id'] ?>" target="_blank"><i class="bi bi-eye"></i> Просмотреть</a></li>
                                            <?php 
                                            if ($user['user_type'] == 'employer') 
                                            {
                                            ?>
                                            <li>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <button type="submit" name="verify_employer" class="dropdown-item">
                                                        <i class="bi bi-check-circle"></i> Верифицировать
                                                    </button>
                                                </form>
                                            </li>
                                            <?php 
                                            } 
                                            ?>
                                            <li>
                                                <?php 
                                                if ($user['is_banned']) 
                                                {
                                                ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <button type="submit" name="toggle_ban" class="dropdown-item">
                                                        <i class="bi bi-unlock"></i> Разблокировать
                                                    </button>
                                                </form>
                                                <?php 
                                                }
                                                else
                                                {
                                                ?>
                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#banModal<?= $user['id'] ?>">
                                                    <i class="bi bi-lock"></i> Заблокировать
                                                </button>
                                                <?php 
                                                } 
                                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="modal fade" id="banModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Блокировка пользователя</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <div class="modal-body">
                                                        <p>Вы собираетесь заблокировать пользователя <strong><?= htmlspecialchars($user['name']) ?></strong>.</p>
                                                        <div class="mb-3">
                                                            <label for="reason<?= $user['id'] ?>" class="form-label">Причина блокировки</label>
                                                            <textarea class="form-control" id="reason<?= $user['id'] ?>" name="reason" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                        <button type="submit" name="toggle_ban" class="btn btn-danger">Заблокировать</button>
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
                            <a class="page-link" href="admin_users.php?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&type=<?= $type ?>&status=<?= $status ?>">Назад</a>
                        </li>
                        <?php 
                        for ($i = 1; $i <= $total_pages; $i++)
                        {
                        ?>
                        <li class="page-item <?php if ($page == $i) { echo 'active'; } ?>">
                            <a class="page-link" href="admin_users.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= $type ?>&status=<?= $status ?>"><?= $i ?></a>
                        </li>
                        <?php 
                        } 
                        ?>
                        <li class="page-item <?php if ($page >= $total_pages) { echo 'disabled'; } ?>">
                            <a class="page-link" href="admin_users.php?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&type=<?= $type ?>&status=<?= $status ?>">Вперед</a>
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