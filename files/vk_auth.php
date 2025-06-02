<?php
session_start();
require_once("../config/link.php");

$client_id = 'МОЙ_APP_ID_VK';
$client_secret = 'МОЙ_APP_SECRET_VK';
$redirect_uri = 'https://мой-сайт.ru/files/vk_auth.php';

if (isset($_GET['action'])) 
{
    $action = $_GET['action'];
} 
else 
{
    $action = 'login';
}

if (isset($_GET['code'])) 
{
    $code = $_GET['code'];
    $token_url = "https://oauth.vk.com/access_token?client_id=$client_id&client_secret=$client_secret&redirect_uri=$redirect_uri&code=$code";
    $token_data = json_decode(file_get_contents($token_url), true);

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
    
    if (isset($token_data['access_token'])) 
    {
        $access_token = $token_data['access_token'];
        $user_id = $token_data['user_id'];
        
        if (isset($token_data['email'])) 
        {
            $email = $token_data['email'];
        } 
        else 
        {
            $email = null;
        }
        
        $user_info_url = "https://api.vk.com/method/users.get?user_ids=$user_id&access_token=$access_token&v=5.131&fields=first_name,last_name,photo_200";
        $user_info = json_decode(file_get_contents($user_info_url), true);
        
        if (isset($user_info['response'][0])) 
        {
            $user = $user_info['response'][0];
            $full_name = $user['first_name'] . ' ' . $user['last_name'];
            
            $check_sql = "SELECT * FROM `users` WHERE `vk_id` = '$user_id'";
            $result = $conn->query($check_sql);
            
            if ($result->num_rows > 0)
            {
                $user_data = $result->fetch_assoc();
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['user_name'] = $user_data['name'];
                $_SESSION['user_email'] = $user_data['email'];
                $_SESSION['user_type'] = $user_data['user_type'];
                
                header("Location: ../profile.php");
                exit();
            } 
            else if ($action == 'register') 
            {
                $insert_sql = "INSERT INTO `users` (`name`, `email`, `vk_id`, `user_type`, `created_at`) VALUES ('$full_name', '$email', '$user_id', 'student', NOW())";
                
                if ($conn->query($insert_sql)) 
                {
                    $new_user_id = $conn->insert_id;
                    
                    $_SESSION['user_id'] = $new_user_id;
                    $_SESSION['user_name'] = $full_name;
                    $_SESSION['user_email'] = $email;
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
}

if ($action == 'register')
{
    $_SESSION['error_register'] = "Ошибка авторизации через ВКонтакте";
    header("Location: ../index.php#registerModal");
    exit();
}
else
{
    $_SESSION['error'] = "Ошибка авторизации через ВКонтакте";
    header("Location: ../index.php#loginModal");
    exit();
}
?>