<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id'])) 
{
    die("Неавторизованный доступ");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{
    die("Неверный метод запроса");
}

$work_id = (int)$_POST['work_id'];
$user_id = $_SESSION['user_id'];

$check_sql = "SELECT * FROM `portfolio` WHERE `id` = ? AND `user_id` = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $work_id, $user_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) === 0) 
{
    die("Работа не найдена или нет прав на редактирование");
}

$title = trim(stripslashes($_POST['title']));
$category_id = (int)$_POST['category_id'];
$description = trim(stripslashes($_POST['description']));

if (isset($_POST['tags'])) 
{
    $tags = trim(stripslashes($_POST['tags']));
} 
else 
{
    $tags = '';
}

$external_links = [];

foreach ($_POST['external_links'] as $platform => $url) 
{
    if (!empty($url)) 
    {
        $external_links[$platform] = $url;
    }
}

$external_links_json = !empty($external_links) ? json_encode($external_links) : NULL;
$image_path = null;

if (!empty($_FILES['image']['name'])) 
{
    $upload_dir = '../uploads/portfolio/';

    if (!is_dir($upload_dir)) 
    {
        mkdir($upload_dir, 0755, true);
    }
        
    $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = 'work_' . $work_id . '_' . time() . '.' . $file_ext;
    $target_file = $upload_dir . $file_name;
        
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) 
    {
        $image_path = $target_file;
        $old_image = mysqli_fetch_assoc($check_result)['image_path'];

        if ($old_image && file_exists($old_image)) 
        {
            unlink($old_image);
        }
    }
}

$sql = "UPDATE `portfolio` SET `title` = ?, `category_id` = ?, `description` = ?, `tags` = ?, `external_links` = ?" . ($image_path ? ", `image_path` = ?" : "") . " WHERE `id` = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($image_path) 
{
    mysqli_stmt_bind_param($stmt, "sissssi", $title, $category_id, $description, $tags, $external_links_json, $image_path, $work_id);
} 
else 
{
    mysqli_stmt_bind_param($stmt, "sisssi", $title, $category_id, $description, $tags, $external_links_json, $work_id);
}

if (mysqli_stmt_execute($stmt)) 
{
    $_SESSION['success'] = "Работа успешно обновлена";
} 
else 
{
    $_SESSION['error'] = "Ошибка при обновлении работы: " . mysqli_error($conn);
}

header("Location: my_works.php");
exit();
?>