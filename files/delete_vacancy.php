<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') 
{
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) 
{
    header("Location: my_vacancies.php");
    exit();
}

$vacancy_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
mysqli_begin_transaction($conn);

try 
{
    $vacancy_sql = "SELECT `image_path` FROM `vacancies` WHERE `id` = $vacancy_id AND `user_id` = $user_id";
    $vacancy_result = mysqli_query($conn, $vacancy_sql);
    $vacancy = mysqli_fetch_assoc($vacancy_result);
    
    if (!$vacancy) 
    {
        throw new Exception("Вакансия не найдена или нет прав на удаление");
    }

    $delete_applications_sql = "DELETE FROM `vacancy_applications` WHERE `vacancy_id` = $vacancy_id";
    mysqli_query($conn, $delete_applications_sql);

    $delete_views_sql = "DELETE FROM `vacancy_views` WHERE `vacancy_id` = $vacancy_id";
    mysqli_query($conn, $delete_views_sql);

    $delete_sql = "DELETE FROM `vacancies` WHERE `id` = $vacancy_id AND `user_id` = $user_id";
    mysqli_query($conn, $delete_sql);

    if ($vacancy['image_path'] && file_exists($vacancy['image_path'])) 
    {
        unlink($vacancy['image_path']);
    }

    mysqli_commit($conn);
    $_SESSION['success'] = "Вакансия и все связанные данные успешно удалены";
} 
catch (Exception $e) 
{
    mysqli_rollback($conn);
    $_SESSION['error'] = "Ошибка при удалении: " . $e->getMessage();
}

header("Location: my_vacancies.php");
exit();
?>