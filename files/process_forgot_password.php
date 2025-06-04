<?php
session_start();
require_once("../config/link.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $email = trim($_POST['email']);
    
    $sql = "SELECT `id` FROM `users` WHERE `email` = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) 
    {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
        
        $sql = "INSERT INTO `password_resets` (`user_id`, `token`, `expires_at`) VALUES ('{$user['id']}', '$token', '$expires')";
        mysqli_query($conn, $sql);
        
        if (isset($_SERVER['HTTPS'])) 
        {
            $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";
        } 
        else 
        {
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";
        }
        
        $_SESSION['success'] = "Инструкции по восстановлению пароля отправлены на ваш email.";

        try
        {
            $to = $email;
            $subject = 'Восстановление пароля на СтудМаркет';
            $message = "
                <html>
                <head>
                    <title>Восстановление пароля</title>
                </head>
                <body>
                    <h2>Восстановление пароля</h2>
                    <p>Здравствуйте, {$user['name']}!</p>
                    <p>Мы получили запрос на сброс пароля для вашего аккаунта на платформе СтудМаркет.</p>
                    <p>Для установки нового пароля перейдите по ссылке:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.</p>
                    <p>Ссылка действительна в течение 1 часа.</p>
                    <hr>
                    <p>С уважением,<br>Команда СтудМаркет</p>
                </body>
                </html>
            ";

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "From: СтудМаркет <noreply@studmarket.ru>\r\n";

            if (mail($to, $subject, $message, $headers)) 
            {
                $_SESSION['success'] = "Инструкции по восстановлению пароля отправлены на ваш email.";
            } 
            else 
            {
                $_SESSION['error'] = "Не удалось отправить письмо. Попробуйте позже.";
            }
        }
        catch (Exception $e) 
        {
            $_SESSION['error'] = "Не удалось отправить письмо. Ошибка: {$mail->ErrorInfo}";
        }
    } 
    else 
    {
        $_SESSION['error'] = "Пользователь с таким email не найден.";
    }
    
    header("Location: forgot_password.php");
    exit();
}