<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') 
{
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $user_id = $_SESSION['user_id'];
    $vacancy_id = (int)$_POST['vacancy_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $resume_path = null;

    $check_vacancy = mysqli_query($conn, "SELECT `id` FROM `vacancies` WHERE `id` = $vacancy_id");

    if (mysqli_num_rows($check_vacancy) == 0) 
    {
        $_SESSION['error'] = "Вакансия не найдена";
        header("Location: vacancies.php");
        exit();
    }

    $check_application = mysqli_query($conn, "SELECT `id` FROM `vacancy_applications` WHERE `user_id` = $user_id AND `vacancy_id` = $vacancy_id");

    if (mysqli_num_rows($check_application) > 0) 
    {
        $_SESSION['error'] = "Вы уже откликались на эту вакансию";
        header("Location: vacancy_details.php?id=$vacancy_id");
        exit();
    }

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) 
    {
        $resume_dir = "../uploads/resumes/";

        if (!file_exists($resume_dir)) 
        {
            mkdir($resume_dir, 0777, true);
        }

        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $file_type = $_FILES['resume']['type'];
        $file_size = $_FILES['resume']['size'];
        $max_size = 5 * 1024 * 1024;

        if (in_array($file_type, $allowed_types)) 
        {
            if ($file_size <= $max_size) 
            {
                $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
                $new_filename = 'resume_' . $user_id . '_' . time() . '.' . $file_ext;
                $target_path = $resume_dir . $new_filename;

                if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_path)) 
                {
                    $resume_path = $target_path;
                } 
                else 
                {
                    $_SESSION['error'] = "Ошибка при загрузке файла резюме";
                    header("Location: vacancy_details.php?id=$vacancy_id");
                    exit();
                }
            } 
            else 
            {
                $_SESSION['error'] = "Размер файла резюме не должен превышать 5MB";
                header("Location: vacancy_details.php?id=$vacancy_id");
                exit();
            }
        } 
        else 
        {
            $_SESSION['error'] = "Допустимы только файлы PDF, DOC или DOCX";
            header("Location: vacancy_details.php?id=$vacancy_id");
            exit();
        }
    }

    $sql = "INSERT INTO `vacancy_applications` (`vacancy_id`, `user_id`, `message`, `resume_path`) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiss", $vacancy_id, $user_id, $message, $resume_path);

    if (mysqli_stmt_execute($stmt)) 
    {
        $_SESSION['success'] = "Ваш отклик успешно отправлен!";

        $employer_sql = "SELECT u.email, v.title FROM vacancies v JOIN users u ON v.user_id = u.id WHERE v.id = $vacancy_id";
        $employer_result = mysqli_query($conn, $employer_sql);
        $employer_data = mysqli_fetch_assoc($employer_result);
        
        if ($employer_data) 
        {
            $to = $employer_data['email'];
            $subject = "Новый отклик на вакансию: " . $employer_data['title'];
            $message = "Здравствуйте!\n\nНа вашу вакансию \"".$employer_data['title']."\" поступил новый отклик.\n\nПожалуйста, войдите в систему для просмотра деталей.\n\nС уважением,\nКоманда СтудМаркет";
            $headers = "From: no-reply@studmarket.ru";
            
            mail($to, $subject, $message, $headers);
        }
        
        header("Location: vacancy_details.php?id=$vacancy_id");
    } 
    else 
    {
        $_SESSION['error'] = "Ошибка при отправке отклика: " . mysqli_error($conn);
        header("Location: vacancy_details.php?id=$vacancy_id");
    }
    
    exit();
} 
else 
{
    header("Location: vacancies.php");
    exit();
}
?>