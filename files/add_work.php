<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id'])) 
{
    $_SESSION['error'] = "Для добавления работы необходимо авторизоваться";
    header("Location: portfolio.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $tags = mysqli_real_escape_string($conn, $_POST['tags']);
    $user_id = $_SESSION['user_id'];
    
    $external_links = [];

    if (isset($_POST['external_links'])) 
    {
        foreach ($_POST['external_links'] as $platform => $url) 
        {
            if (!empty(trim($url))) 
            {
                $external_links[$platform] = filter_var($url, FILTER_SANITIZE_URL);
            }
        }
    }

    if (!empty($external_links)) 
    {
        $external_links_json = json_encode($external_links);
    } 
    else 
    {
        $external_links_json = null;
    }

    $upload_dir = "../uploads/portfolio/";

    if (!file_exists($upload_dir)) 
    {
        mkdir($upload_dir, 0777, true);
    }
    
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) 
    {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024;
        
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        
        if (in_array($file_type, $allowed_types)) 
        {
            if ($file_size <= $max_size) 
            {
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'work_' . $user_id . '_' . time() . '.' . $file_ext;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) 
                {
                    $image_path = $target_path;
                } 
                else 
                {
                    $_SESSION['error'] = "Ошибка загрузки файла";
                }
            } 
            else 
            {
                $_SESSION['error'] = "Размер файла не должен превышать 5MB";
            }
        } 
        else 
        {
            $_SESSION['error'] = "Допустимы только файлы JPEG, PNG или GIF";
        }
    } 
    else 
    {
        $_SESSION['error'] = "Необходимо загрузить изображение работы";
    }
    
    if (!isset($_SESSION['error'])) 
    {
        $sql = "INSERT INTO `portfolio` (`user_id`, `title`, `category_id`, `description`, `tags`, `image_path`, `external_links`) VALUES ('$user_id', '$title', '$category_id', '$description', '$tags', '$image_path', " . ($external_links_json ? "'$external_links_json'" : "NULL") . ")";
        
        if (mysqli_query($conn, $sql)) 
        {
            $_SESSION['success'] = "Работа успешно добавлена в портфолио";
        } 
        else 
        {
            $_SESSION['error'] = "Ошибка при добавлении работы: " . mysqli_error($conn);
        }
    }
    
    header("Location: portfolio.php");
    exit();
}
?>