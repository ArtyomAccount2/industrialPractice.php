<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') 
{
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $employment_type = mysqli_real_escape_string($conn, $_POST['employment_type']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $requirements = mysqli_real_escape_string($conn, $_POST['requirements']);
    $benefits = mysqli_real_escape_string($conn, $_POST['benefits']);
    $contacts = mysqli_real_escape_string($conn, $_POST['contacts']);
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) 
    {
        $image_dir = "../uploads/vacancies/";

        if (!file_exists($image_dir)) 
        {
            mkdir($image_dir, 0777, true);
        }

        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $max_size = 2 * 1024 * 1024;

        if (in_array($file_type, $allowed_types)) 
        {
            if ($file_size <= $max_size) 
            {
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'vacancy_' . $user_id . '_' . time() . '.' . $file_ext;
                $target_path = $image_dir . $new_filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) 
                {
                    $image_path = $target_path;
                } 
                else 
                {
                    $_SESSION['error'] = "Ошибка при загрузке изображения";
                    header("Location: ../pages/my_vacancies.php");
                    exit();
                }
            } 
            else 
            {
                $_SESSION['error'] = "Размер изображения не должен превышать 2MB";
                header("Location: ../pages/my_vacancies.php");
                exit();
            }
        } 
        else 
        {
            $_SESSION['error'] = "Допустимы только файлы JPG или PNG";
            header("Location: ../pages/my_vacancies.php");
            exit();
        }
    }

    $sql = "INSERT INTO `vacancies` (`user_id`, `title`, `category_id`, `employment_type`, `salary`, `location`, `description`, `requirements`, `benefits`, `contacts`, `image_path`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isissssssss", $user_id, $title, $category_id, $employment_type, $salary, $location, $description, $requirements, $benefits, $contacts, $image_path);
    
    if (mysqli_stmt_execute($stmt)) 
    {
        $_SESSION['success'] = "Вакансия успешно опубликована";
        header("Location: ../pages/my_vacancies.php");
    } 
    else 
    {
        $_SESSION['error'] = "Ошибка при публикации вакансии: " . mysqli_error($conn);
        header("Location: ../pages/my_vacancies.php");
    }
    
    exit();
} 
else 
{
    header("Location: ../pages/my_vacancies.php");
    exit();
}
?>