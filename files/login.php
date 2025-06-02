<?php
session_start();
require_once("../config/link.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

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
    
    $sql = "SELECT `id`, `name`, `password`, `user_type`, `phone` FROM `users` WHERE `email` = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) 
    {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) 
        {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_phone'] = $user['phone'];
            
            header("Location: ../profile.php");
            exit();
        } 
        else 
        {
            $_SESSION['error'] = "Неверный пароль";
            header("Location: $referer#loginModal");
            exit();
        }
    } 
    else 
    {
        $_SESSION['error'] = "Пользователь с таким email не найден";
        header("Location: $referer#loginModal");
        exit();
    }
}
?>