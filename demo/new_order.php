<?php
// Начало сессии и проверка авторизации
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Подключение к базе данных
require_once 'db_connect.php';

// Список доступных марок и моделей автомобилей
$cars = [
    'Toyota' => ['Camry', 'Corolla', 'RAV4'],
    'Honda' => ['Accord', 'Civic', 'CR-V'],
    'BMW' => ['3 Series', '5 Series', 'X5'],
    'Audi' => ['A4', 'A6', 'Q5']
];

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Валидация и сохранение данных
    $errors = [];
    
    $required_fields = [
        'address', 'contact_info', 'marka_auto', 'model_auto', 
        'date_order', 'time_order', 'payment_type', 
        'card_serial', 'card_number', 'date_iss'
    ];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Поле " . ucfirst(str_replace('_', ' ', $field)) . " обязательно для заполнения";
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO orders 
                (user_id, status_id, address, contact_info, marka_auto, model_auto, 
                 date_order, time_order, payment_type, card_serial, card_number, date_iss, comment) 
                VALUES 
                (:user_id, 1, :address, :contact_info, :marka_auto, :model_auto, 
                 :date_order, :time_order, :payment_type, :card_serial, :card_number, :date_iss, :comment)");
            
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'address' => $_POST['address'],
                'contact_info' => $_POST['contact_info'],
                'marka_auto' => $_POST['marka_auto'],
                'model_auto' => $_POST['model_auto'],
                'date_order' => $_POST['date_order'],
                'time_order' => $_POST['time_order'],
                'payment_type' => $_POST['payment_type'],
                'card_serial' => $_POST['card_serial'],
                'card_number' => $_POST['card_number'],
                'date_iss' => $_POST['date_iss'],
                'comment' => $_POST['comment'] ?? null
            ]);
            
            header("Location: orders.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Ошибка при сохранении заявки: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка | Едем, но это не точно</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 390px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin: 0;
        }
        .back-btn {
            background: none;
            border: none;
            color: #4CAF50;
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .car-select {
            display: flex;
            gap: 10px;
        }
        .car-select select {
            flex: 1;
        }
        .date-time {
            display: flex;
            gap: 10px;
        }
        .date-time input {
            flex: 1;
        }
        .payment-type {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .payment-type label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: normal;
        }
        .payment-type input {
            width: auto;
        }
        .license-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .license-info h3 {
            margin-top: 0;
            color: #333;
        }
        .license-fields {
            display: flex;
            gap: 10px;
        }
        .license-fields input {
            flex: 1;
        }
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Новая заявка</h1>
        <a href="orders.php" class="back-btn">Назад</a>
    </div>
    
    <div class="form-container">
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form action="new_order.php" method="post">
            <div class="form-group">
                <label for="address">Адрес:</label>
                <input type="text" id="address" name="address" required>
            </div>
            
            <div class="form-group">
                <label for="contact_info">Контактный телефон:</label>
                <input type="tel" id="contact_info" name="contact_info" pattern="\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}" placeholder="+7(XXX)-XXX-XX-XX" required>
            </div>
            
            <div class="form-group">
                <label>Автомобиль:</label>
                <div class="car-select">
                    <select id="marka_auto" name="marka_auto" required>
                        <option value="">Выберите марку</option>
                        <?php foreach ($cars as $marka => $models): ?>
                            <option value="<?= htmlspecialchars($marka) ?>"><?= htmlspecialchars($marka) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="model_auto" name="model_auto" required disabled>
                        <option value="">Выберите модель</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Дата и время тест-драйва:</label>
                <div class="date-time">
                    <input type="date" id="date_order" name="date_order" min="<?= date('Y-m-d') ?>" required>
                    <input type="time" id="time_order" name="time_order" min="09:00" max="18:00" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Тип оплаты:</label>
                <div class="payment-type">
                    <label>
                        <input type="radio" name="payment_type" value="Наличные" checked> Наличные
                    </label>
                    <label>
                        <input type="radio" name="payment_type" value="Карта"> Карта
                    </label>
                </div>
            </div>
            
            <div class="license-info">
                <h3>Данные водительского удостоверения</h3>
                <div class="license-fields">
                    <input type="text" id="card_serial" name="card_serial" placeholder="Серия" required>
                    <input type="text" id="card_number" name="card_number" placeholder="Номер" required>
                </div>
                <div class="form-group">
                    <label for="date_iss">Дата выдачи:</label>
                    <input type="date" id="date_iss" name="date_iss" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="comment">Комментарий (необязательно):</label>
                <textarea id="comment" name="comment" rows="3"></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Отправить заявку</button>
        </form>
    </div>

    <script>
        // Динамическое заполнение моделей автомобилей
        const cars = <?= json_encode($cars) ?>;
        const markaSelect = document.getElementById('marka_auto');
        const modelSelect = document.getElementById('model_auto');
        
        markaSelect.addEventListener('change', function() {
            const selectedMarka = this.value;
            modelSelect.innerHTML = '<option value="">Выберите модель</option>';
            modelSelect.disabled = !selectedMarka;
            
            if (selectedMarka) {
                cars[selectedMarka].forEach(model => {
                    const option = document.createElement('option');
                    option.value = model;
                    option.textContent = model;
                    modelSelect.appendChild(option);
                });
            }
        });
        
        // Установка минимального времени (текущее время + 1 час)
        const now = new Date();
        const minDate = new Date(now.getTime() + 60 * 60 * 1000); // +1 час
        document.getElementById('date_order').min = new Date().toISOString().split('T')[0];
        
        // Валидация даты и времени
        document.querySelector('form').addEventListener('submit', function(e) {
            const dateOrder = new Date(document.getElementById('date_order').value);
            const timeOrder = document.getElementById('time_order').value;
            
            if (dateOrder && timeOrder) {
                const [hours, minutes] = timeOrder.split(':').map(Number);
                dateOrder.setHours(hours, minutes);
                
                if (dateOrder < minDate) {
                    alert('Дата и время тест-драйва должны быть не раньше, чем через час от текущего времени');
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>