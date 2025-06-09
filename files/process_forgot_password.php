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
    $user = mysqli_fetch_assoc($stmt);
    
    if ($user) 
    {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
        
        $sql = "INSERT INTO `password_resets` (`user_id`, `token`, `expires_at`) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $user['id'], $token, $expires);
        mysqli_stmt_execute($stmt);
        
        $resetLink = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";
        
        require_once("../vendor/autoload.php");
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try 
        {
            $mail->isSMTP();
            $mail->Host = 'smtp.yourprovider.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@studmarket.ru';
            $mail->Password = 'yourpassword';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            
            $mail->setFrom('noreply@studmarket.ru', 'СтудМаркет');
            $mail->addAddress($email, $user['name']);
            
            $mail->isHTML(true);
            $mail->Subject = 'Восстановление пароля на СтудМаркет';
            $mail->Body = "
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
            
            $mail->send();
            $_SESSION['success'] = "Инструкции по восстановлению пароля отправлены на ваш email.";
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