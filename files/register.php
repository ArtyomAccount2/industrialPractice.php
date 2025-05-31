<?php
session_start();
require_once("../config/link.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($_POST['phone']);
    $user_type = $_POST['user_type'] == 'employer' ? 'employer' : 'student';

    $referer = isset($_POST['referer']) ? $_POST['referer'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php');
    
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) 
    {
        $_SESSION['error_register'] = "Этот email уже зарегистрирован";
        header("Location: $referer#registerModal");
        exit();
    }
    
    $sql = "INSERT INTO users (name, email, password, user_type, phone) VALUES ('$name', '$email', '$password', '$user_type', '$phone')";
    
    if ($conn->query($sql) === TRUE) 
    {
        $user_id = $conn->insert_id;
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_type'] = $user_type;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_phone'] = $phone;
        
        header("Location: ../profile.php");
        exit();
    } 
    else 
    {
        $_SESSION['error_register'] = "Ошибка регистрации: " . $conn->error;
        header("Location: $referer#registerModal.php");
        exit();
    }
}
?>