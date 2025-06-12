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
    
    foreach ($_POST['settings'] as $key => $value) 
    {
        $key = $conn->real_escape_string($key);
        $value = $conn->real_escape_string($value);
        
        $conn->query("INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES ('$key', '$value') ON DUPLICATE KEY UPDATE `setting_value` = '$value'");
    }

    $action = "Изменение системных настроек";
    $details = "Изменены настройки: " . implode(', ', array_keys($_POST['settings']));
    $conn->query("INSERT INTO `admin_actions` (`admin_id`, `action`, `details`, `ip_address`) VALUES ($admin_id, '$action', '$details', '$ip')");
    
    $_SESSION['admin_message'] = "Настройки успешно сохранены";
    header("Location: admin_settings.php");
    exit();
}

$settings = [];
$result = $conn->query("SELECT * FROM `system_settings`");

while ($row = $result->fetch_assoc()) 
{
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Системные настройки | СтудМаркет</title>
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
            <h1 class="h2">Системные настройки</h1>
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
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-4">
                        <h5 class="mb-3">Основные настройки</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="site_name" class="form-label">Название сайта</label>
                                <input type="text" class="form-control" id="site_name" name="settings[site_name]" value="<?= htmlspecialchars($settings['site_name'] ?? 'СтудМаркет') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="site_email" class="form-label">Email сайта</label>
                                <input type="email" class="form-control" id="site_email" name="settings[site_email]" value="<?= htmlspecialchars($settings['site_email'] ?? 'gaukokp@mail.ru') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="moderation_enabled" class="form-label">Модерация контента</label>
                                <select class="form-select" id="moderation_enabled" name="settings[moderation_enabled]">
                                    <option value="1" <?= ($settings['moderation_enabled'] ?? '1') == '1' ? 'selected' : '' ?>>Включена</option>
                                    <option value="0" <?= ($settings['moderation_enabled'] ?? '1') == '0' ? 'selected' : '' ?>>Выключена</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="registration_enabled" class="form-label">Регистрация новых пользователей</label>
                                <select class="form-select" id="registration_enabled" name="settings[registration_enabled]">
                                    <option value="1" <?= ($settings['registration_enabled'] ?? '1') == '1' ? 'selected' : '' ?>>Включена</option>
                                    <option value="0" <?= ($settings['registration_enabled'] ?? '1') == '0' ? 'selected' : '' ?>>Выключена</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h5 class="mb-3">Настройки портфолио</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="portfolio_max_size" class="form-label">Макс. размер работы (МБ)</label>
                                <input type="number" class="form-control" id="portfolio_max_size" name="settings[portfolio_max_size]" value="<?= htmlspecialchars($settings['portfolio_max_size'] ?? '5') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="portfolio_allowed_types" class="form-label">Разрешенные форматы</label>
                                <input type="text" class="form-control" id="portfolio_allowed_types" name="settings[portfolio_allowed_types]" value="<?= htmlspecialchars($settings['portfolio_allowed_types'] ?? 'jpg,jpeg,png,gif') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h5 class="mb-3">Настройки вакансий</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="vacancy_lifetime" class="form-label">Срок жизни вакансии (дней)</label>
                                <input type="number" class="form-control" id="vacancy_lifetime" name="settings[vacancy_lifetime]" value="<?= htmlspecialchars($settings['vacancy_lifetime'] ?? '30') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="vacancy_max_per_employer" class="form-label">Макс. вакансий на работодателя</label>
                                <input type="number" class="form-control" id="vacancy_max_per_employer" name="settings[vacancy_max_per_employer]" value="<?= htmlspecialchars($settings['vacancy_max_per_employer'] ?? '5') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> Сохранить настройки
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
    
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>