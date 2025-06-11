if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    $fio = trim($_POST['fio']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    // Проверка уникальности логина и email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE login =? OR email =?");
    $stmt->execute([$login, $email]);
    if ($stmt->fetch()) {
        $errors[] = "Логин или email уже занят";
    }

    // Хеширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (login, password, fio, phone, email) VALUES (?,?,?,?,?)");
            $stmt->execute([$login, $hashed_password, $fio, $phone, $email]);
            $_SESSION['register_message'] = "Регистрация успешна!";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Ошибка при регистрации: ". $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        header("Location: register.php");
        exit();
    }
}