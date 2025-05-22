<?php
error_reporting(E_ALL);

$conn = mysqli_connect("127.0.0.1", "root", "", "studmarket");

if ($conn->connect_error)
{
    echo "Ошибка подключения: " . $conn->connect_error;
}
?>