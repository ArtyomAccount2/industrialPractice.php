<?php
session_start();
require_once("../config/link.php");

if (isset($_GET['action'])) 
{
    $action = $_GET['action'];
} 
else 
{
    $action = 'login';
}

$bot_token = 'МОЙ_BOT_TOKEN';

if (isset($_POST['hash']) && isset($_POST['user'])) 
{
    $data = $_POST;
    $secret_key = hash('sha256', $bot_token, true);
    $hash = $data['hash'];
    unset($data['hash']);

    if (isset($_POST['referer'])) 
    {
        $referer = $_POST['referer'];
    } 
    else if (isset($_SERVER['HTTP_REFERER'])) 
    {
        $referer = $_SERVER['HTTP_REFERER'];
    } 
    else 
    {
        $referer = '../index.php';
    }
    
    $data_check_arr = [];

    foreach ($data as $key => $value) 
    {
        $data_check_arr[] = "$key=$value";
    }

    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    $generated_hash = hash_hmac('sha256', $data_check_string, $secret_key);
    
    if ($generated_hash === $hash) 
    {
        $user_data = json_decode($data['user'], true);
        $tg_id = $user_data['id'];
        $first_name = $user_data['first_name'];

        if (isset($user_data['last_name'])) 
        {
            $last_name = $user_data['last_name'];
        } 
        else 
        {
            $last_name = '';
        }
        
        if (isset($user_data['username'])) 
        {
            $username = $user_data['username'];
        } 
        else 
        {
            $username = '';
        }

        $full_name = trim("$first_name $last_name");
        
        $check_sql = "SELECT * FROM `users` WHERE `telegram_id` = '$tg_id'";
        $result = $conn->query($check_sql);
        
        if ($result->num_rows > 0) 
        {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type'];
            
            header("Location: ../profile.php");
            exit();
        } 
        else if ($action == 'register') 
        {
            $insert_sql = "INSERT INTO `users` (`name`, `telegram_id`, `telegram_username`, `user_type`, `created_at`) VALUES ('$full_name', '$tg_id', '$username', 'student', NOW())";
            
            if ($conn->query($insert_sql)) 
            {
                $new_user_id = $conn->insert_id;
                
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_type'] = 'student';
                
                header("Location: ../profile.php");
                exit();
            }
        } 
        else 
        {
            $_SESSION['error_register'] = "Пользователь не найден. Пожалуйста, зарегистрируйтесь.";
            header("Location: $referer#registerModal");
            exit();
        }
    }
}

if ($action == 'register')
{
    $_SESSION['error_register'] = "Ошибка авторизации через Telegram";
    header("Location: ../index.php#registerModal");
    exit();
}
else
{
    $_SESSION['error'] = "Ошибка авторизации через Telegram";
    header("Location: ../index.php#loginModal");
    exit();
}
?>