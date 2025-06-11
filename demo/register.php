<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | Едем, но это не точно</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 390px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Регистрация</h1>
        <form id="registerForm" action="register_process.php" method="post">
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль (мин. 6 символов):</label>
                <input type="password" id="password" name="password" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="fio">ФИО:</label>
                <input type="text" id="fio" name="fio" pattern="[А-Яа-яЁё\s]+" required>
            </div>
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" pattern="\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}" placeholder="+7(XXX)-XXX-XX-XX" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <div class="login-link">
            Уже есть аккаунт? <a href="login.php">Войти</a>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            // Дополнительная валидация перед отправкой
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                alert('Пароль должен содержать минимум 6 символов');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>