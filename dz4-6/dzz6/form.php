<?php
header('Content-Type: text/html; charset=UTF-8');

// Подключение к базе данных
$db = new PDO('mysql:host=localhost;dbname=u68806', 'u68806', '1921639');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Проверка метода POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fio = $_POST['fio'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $dob_input = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    // Преобразование даты, если она в формате ДД.ММ.ГГГГ
    if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $dob_input)) {
        $parts = explode('.', $dob_input);
        $dob = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
    } else {
        $dob = $dob_input;
    }

    // Хеширование пароля
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Вставка в базу данных
    $stmt = $db->prepare("INSERT INTO application (fio, phone, email, dob, gender, bio, login, password_hash)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fio, $phone, $email, $dob, $gender, $bio, $login, $password_hash]);

    echo "<p style='color: green;'>Данные успешно сохранены!</p>";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма заявки</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<form action="form.php" method="POST">
    <h1>Форма заявки</h1>
    <label>ФИО:<br><input name="fio" required></label>
    <label>Телефон:<br><input name="phone" type="tel" required></label>
    <label>Email:<br><input name="email" type="email" required></label>
    <label>Дата рождения:<br><input name="dob" type="date" required></label>
    <label>Пол:<br>
        <input type="radio" name="gender" value="male" required> Мужской
        <input type="radio" name="gender" value="female"> Женский
    </label>
    <label>Биография:<br><textarea name="bio" required></textarea></label>
    <label>Логин:<br><input name="login" required></label>
    <label>Пароль:<br><input name="password" type="password" required></label>
    <input type="submit" value="Отправить">
</form>
</body>
</html>
