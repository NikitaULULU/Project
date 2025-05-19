<?php
// Подключение к БД
$pdo = new PDO('mysql:host=localhost;dbname=u68806', 'u68806', '1921639');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// HTTP Basic Auth
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    header('WWW-Authenticate: Basic realm="Admin Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Требуется авторизация';
    exit;
}

$stmt = $pdo->prepare("SELECT password_hash FROM admins WHERE login = ?");
$stmt->execute([$_SERVER['PHP_AUTH_USER']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || !password_verify($_SERVER['PHP_AUTH_PW'], $row['password_hash'])) {
    header('WWW-Authenticate: Basic realm="Admin Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Неверные учетные данные';
    exit;
}

// Удаление заявки
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM application WHERE id = ?");
    $stmt->execute([$id]);
    $stmt = $pdo->prepare("DELETE FROM application_languages WHERE app_id = ?");
    $stmt->execute([$id]);
    header('Location: admin.php');
    exit;
}

// Загрузка данных
$stmt = $pdo->query("SELECT * FROM application");
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Статистика по языкам
$stat = $pdo->query("
    SELECT lang_ID, COUNT(*) as count
    FROM application_languages
    GROUP BY lang_ID
")->fetchAll(PDO::FETCH_ASSOC);

$lang_names = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Админ-панель</h1>

    <h2>Список заявок</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th><th>ФИО</th><th>Телефон</th><th>Email</th><th>Дата рождения</th><th>Пол</th><th>Биография</th><th>Логин</th><th>Действия</th>
        </tr>
        <?php foreach ($apps as $app): ?>
        <tr>
            <td><?= $app['id'] ?></td>
            <td><?= htmlspecialchars($app['fio']) ?></td>
            <td><?= htmlspecialchars($app['phone']) ?></td>
            <td><?= htmlspecialchars($app['email']) ?></td>
            <td><?= htmlspecialchars($app['dob']) ?></td>
            <td><?= htmlspecialchars($app['gender']) ?></td>
            <td><?= htmlspecialchars($app['bio']) ?></td>
            <td><?= htmlspecialchars($app['login']) ?></td>
            <td>
                <a href="edit.php?id=<?= $app['id'] ?>">✏️</a>
                <a href="admin.php?delete=<?= $app['id'] ?>" onclick="return confirm('Удалить?')">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Статистика по языкам программирования</h2>
    <ul>
        <?php foreach ($stat as $row): ?>
        <li><?= $lang_names[$row['lang_ID'] - 1] ?>: <?= $row['count'] ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
