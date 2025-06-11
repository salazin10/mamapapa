<?php
require_once 'db_connect.php';

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_fio'] = $user['fio'];
$_SESSION['user_role'] = $user['role_id'];


$_SESSION['login_message'] = "Добро пожаловать, " . htmlspecialchars($user['fio']) . "!";
header("Location: orders.php");
exit();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE login =?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_fio'] = $user['fio'];
        header("Location: orders.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Неверный логин или пароль";
        header("Location: login.php");
        exit();
    }
}

// Если аутентификация не удалась
header("Location: login.php?error=1");
exit();
