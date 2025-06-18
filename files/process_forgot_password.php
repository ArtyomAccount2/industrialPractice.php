<?php
session_start();
require_once("../config/link.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $email = trim($_POST['email']);

    $sql = "SELECT `id`, `name` FROM `users` WHERE `email` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) 
    {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
        
        $sql = "DELETE FROM `password_resets` WHERE `user_id` = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user['id']);
        mysqli_stmt_execute($stmt);
        
        $sql = "INSERT INTO `password_resets` (`user_id`, `token`, `expires_at`) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $user['id'], $token, $expires);
        mysqli_stmt_execute($stmt);
        
        $resetLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/pages/reset_password.php?token=$token";
        
        require_once '../lib/src/Exception.php';
        require_once '../lib/src/PHPMailer.php';
        require_once '../lib/src/SMTP.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try 
        {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your.email@gmail.com';
            $mail->Password = 'your_app_password';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            
            $mail->setFrom('your.email@gmail.com', 'СтудМаркет');
            $mail->addAddress($email, $user['name']);
            
            $mail->isHTML(true);
            $mail->Subject = 'Восстановление пароля на СтудМаркет';
            $mail->Body = "<html>
                <head>
                    <title>Восстановление пароля</title>
                    <style>
                        body 
                        { 
                            font-family: Arial, sans-serif; line-height: 1.6; color: #333; 
                        }
                        .container 
                        { 
                            max-width: 600px; margin: 0 auto; padding: 20px; 
                        }
                        .button 
                        { 
                            display: inline-block; padding: 10px 20px; 
                            background-color: #007bff; color: white; 
                            text-decoration: none; border-radius: 5px; 
                            margin: 15px 0;
                        }
                        .footer 
                        { 
                            margin-top: 20px; font-size: 0.9em; color: #777; 
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2>Восстановление пароля</h2>
                        <p>Здравствуйте, {$user['name']}!</p>
                        <p>Мы получили запрос на сброс пароля для вашего аккаунта на платформе СтудМаркет.</p>
                        <p>Для установки нового пароля нажмите на кнопку ниже:</p>
                        <p><a href='$resetLink' class='button'>Сбросить пароль</a></p>
                        <p>Или скопируйте и вставьте в браузер следующую ссылку:</p>
                        <p><code>$resetLink</code></p>
                        <p>Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.</p>
                        <p>Ссылка действительна в течение 1 часа.</p>
                        <div class='footer'>
                            <hr>
                            <p>С уважением,<br>Команда СтудМаркет</p>
                        </div>
                    </div>
                </body>
                </html>";
            
            $mail->AltBody = "Восстановление пароля\n\nЗдравствуйте, {$user['name']}!\n\nДля сброса пароля перейдите по ссылке: $resetLink\n\nСсылка действительна 1 час.";
            
            $mail->send();
            $_SESSION['success'] = "Инструкции по восстановлению пароля отправлены на ваш email.";
        } 
        catch (Exception $e) 
        {
            error_log("Ошибка отправки письма: " . $mail->ErrorInfo);
            $_SESSION['error'] = "Не удалось отправить письмо. Пожалуйста, попробуйте позже.";
        }
    } 
    else 
    {
        $_SESSION['error'] = "Пользователь с таким email не найден.";
    }
    
    header("Location: ../pages/forgot_password.php");
    exit();
}