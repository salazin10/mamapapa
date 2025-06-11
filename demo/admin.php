<?php
// Начало сессии и проверка авторизации
session_start();

// Проверка логина и пароля администратора
if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] != 'avto2024' || 
    $_SERVER['PHP_AUTH_PW'] != 'poehali') {
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Требуется авторизация';
    exit();
}

// Подключение к базе данных
require_once 'db_connect.php';

// Обработка изменения статуса заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status_id'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status_id = :status_id WHERE id = :order_id");
    $stmt->execute([
        'status_id' => $_POST['status_id'],
        'order_id' => $_POST['order_id']
    ]);
    $_SESSION['update_message'] = "Статус заявки обновлен!";
    header("Location: admin.php");
    exit();
}

// Получение всех заявок с информацией о пользователях
$query = "SELECT o.*, u.fio, u.phone, u.email, s.name as status_name 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          JOIN status s ON o.status_id = s.id 
          ORDER BY o.date_order, o.time_order";
$orders = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Получение списка статусов
$statuses = $pdo->query("SELECT * FROM status")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора | Едем, но это не точно</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 390px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .logout-btn {
            display: block;
            text-align: right;
            margin-bottom: 15px;
            color: #4CAF50;
            text-decoration: underline;
        }
        .order-list {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .order-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .order-model {
            font-weight: bold;
            color: #333;
        }
        .order-date {
            color: #666;
            font-size: 14px;
        }
        .order-status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-new {
            background-color: #ffeb3b;
            color: #333;
        }
        .status-approved {
            background-color: #4CAF50;
            color: white;
        }
        .status-completed {
            background-color: #2196F3;
            color: white;
        }
        .status-rejected {
            background-color: #f44336;
            color: white;
        }
        .order-user {
            margin-top: 10px;
            font-size: 14px;
        }
        .order-user strong {
            color: #333;
        }
        .order-details {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        .order-license {
            margin-top: 5px;
            font-size: 13px;
            color: #555;
        }
        .status-form {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .status-form select {
            flex: 1;
            padding: 5px;
            border-radius: 4px;
        }
        .status-form button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .no-orders {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout-btn">Выйти</a>
    <h1>Панель администратора</h1>
    
    <div class="order-list">
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-item">
                    <div class="order-header">
                        <span class="order-model"><?= htmlspecialchars($order['marka_auto']) ?> <?= htmlspecialchars($order['model_auto']) ?></span>
                        <span class="order-date"><?= date('d.m.Y', strtotime($order['date_order'])) ?> <?= substr($order['time_order'], 0, 5) ?></span>
                    </div>
                    
                    <div class="order-header">
                        <span class="order-address"><?= htmlspecialchars($order['address']) ?></span>
                        <span class="order-status status-<?= 
                            $order['status_name'] == 'новая' ? 'new' : 
                            ($order['status_name'] == 'одобрено' ? 'approved' : 
                            ($order['status_name'] == 'выполнено' ? 'completed' : 'rejected')) ?>">
                            <?= htmlspecialchars($order['status_name']) ?>
                        </span>
                    </div>
                    
                    <div class="order-user">
                        <strong>Клиент:</strong> <?= htmlspecialchars($order['fio']) ?><br>
                        <strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?>
                    </div>
                    
                    <div class="order-details">
                        <strong>Контакт:</strong> <?= htmlspecialchars($order['contact_info']) ?><br>
                        <strong>Оплата:</strong> <?= htmlspecialchars($order['payment_type']) ?>
                    </div>
                    
                    <div class="order-license">
                        <strong>Водительское удостоверение:</strong> 
                        <?= htmlspecialchars($order['card_serial']) ?> <?= htmlspecialchars($order['card_number']) ?>, 
                        выдано <?= date('d.m.Y', strtotime($order['date_iss'])) ?>
                    </div>
                    
                    <?php if ($order['comment']): ?>
                        <div class="order-details">
                            <strong>Комментарий:</strong> <?= htmlspecialchars($order['comment']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form class="status-form" method="post">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status_id" required>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status['id'] ?>" <?= $status['id'] == $order['status_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($status['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Обновить</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-orders">Нет заявок на тест-драйв</div>
        <?php endif; ?>
    </div>
</body>
</html>