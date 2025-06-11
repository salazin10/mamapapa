<?php
// Начало сессии и проверка авторизации
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// Подключение к базе данных
require_once 'db_connect.php';

// Получение списка заявок пользователя
$user_id = $_SESSION['user_id'];
$orders_query = "SELECT o.*, s.name as status_name 
                 FROM orders o 
                 JOIN status s ON o.status_id = s.id 
                 WHERE o.user_id = :user_id 
                 ORDER BY o.date_order DESC, o.time_order DESC";
$stmt = $pdo->prepare($orders_query);
$stmt->execute(['user_id' => $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки | Едем, но это не точно</title>
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
        .logout-btn {
            background: none;
            border: none;
            color: #4CAF50;
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
        }
        .new-order-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
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
        .order-address {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .no-orders {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Мои заявки</h1>
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Выйти</button>
        </form>
    </div>
    
    <a href="new_order.php" class="new-order-btn">Новая заявка на тест-драйв</a>
    
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
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-orders">У вас пока нет заявок на тест-драйв</div>
        <?php endif; ?>
    </div>
</body>
</html>