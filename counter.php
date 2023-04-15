<?php
$servername = '127.0.0.1'; // адрес сервера БД
$username = 'root'; // имя пользователя БД
$password = ""; // пароль пользователя БД
$dbname = 'dbip'; // имя БД

// создаём подключение к БД
$conn = new mysqli($servername, $username, $password, $dbname);
// проверяем соединение
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// получаем данные из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);

$ip = $data['ip'];
$city = $data['city'];
$device = $data['device'];

// вставляем данные в таблицу
$sql = "INSERT INTO counter (ip, city, device, created_at) VALUES ('$ip', '$city', '$device', NOW())";
$conn->query($sql);

$conn->close();
?>