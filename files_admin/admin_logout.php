<?php
session_start();
require_once("../config/link.php");

if (isset($_SESSION['admin_id'])) 
{
    $admin_id = $_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $conn->query("INSERT INTO `admin_actions` (`admin_id`, `action`, `details`, `ip_address`) VALUES ($admin_id, 'Выход из системы', 'Успешный выход', '$ip')");

    session_unset();
    session_destroy();
}

header("Location: admin_login.php");
exit();
?>