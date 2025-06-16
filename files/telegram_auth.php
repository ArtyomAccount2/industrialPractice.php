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