<?php
// ============================================================
// BD.php - файл подключения к базе данных MySQL
// Файл будем подключать в другие файлы через reauire
// ============================================================

// Настройки
$host = "localhost";
$dbname = "fbhtpxnk_m1";
$username = "fbhtpxnk";
$password = "f7NUDu";

// Пробуем подключится у базе данных через PDO
// PDO - это удобный способ работа с БД в PHP

try {
    // Создаем подключения к PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Включаем режим ошибок - PDO будет кидать исключения при ошибках
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Сообщение об успешном подключении
    // echo '<div class="alert alert-success">Подключение к базе данных успешно установлено</div>';

} catch(PDOException $e) {
    die('<div class="alert alert-danger">Ошибка подключения к БД:'. $e->getMessage() .'</div>');
}


echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';
echo 'Новый релиз';

