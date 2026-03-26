<?php
    // Подключение к базе данных
    require "BD.php";

    // Переменнная для хранения сообщения об ошибке или успехе
    $error = "";
    $msg = "";

    // Проверяем была ли отправлена форма (нажата ли кнопка "Войти")
    // $_SERVER['REQUEST_METHOD'] - содержит метод запроса: GET или POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $login = $_POST['getlogin'];
        $password = $_POST['getpassword'];

        // Готовим SQL-запрос для поиска пользователя по логину и паролю
        // :login и :password - именованные параметры (защита от SQL-инъекций)
        $sql = "SELECT * FROM users WHERE login = :login AND password = :password";

        // prepare() - подготавливает запрос к выполнению
        $stmt = $pdo->prepare($sql);

        // Подставляем реальные знчаение вместо параметров
        $stmt->execute([':login' => $login, ':password' => $password]);
        
        // fetch() - достает одну строку из результата поиска
        // Если пользователь найден - в переменной $user будет массив с его данными
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        //array(4) { ["id"]=> int(1) ["login"]=> string(5) "admin" ["password"]=> string(8) "admin123" ["email"]=> string(13) "admin@mail.ru" }
        if ($user) {
            // Ветка когда пользователь в переменной $user - найден (а значит авторизован)
            $msg = "Вы успешно авторизованы!";
            
            // Запускает сессию - механизм для хранения данных между страницами
            session_start();

            // Сохраняем данные пользователя в сессию
            $_SESSION['user_login'] = $user['login']; // Столбец из массива 'login'
            $_SESSION['user_id'] = $user['id']; // Столбец из массива 'id'

            // Делаем паузу в 2 секунды, чтобы пользователь мог увидеть зеленое сообщение об успехе Авторизации
            // sleep(2);

            // Перенаправляем пользователя на страницу админ-панели
            header("Location: adminpanel.php");

            // Останавливаем выполнение скрипта после редирект (перенаправление)
            exit();
        } else {
            // Ветка когда пользователя в переменной $user НЕТ (в базе не нашелся)
            $error = "Не верный логин или пароль!";
        }
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="Bootstrap\bootstrap.css">
</head>
<body class="bg-light container d-flex justify-content-center align-items-center flex-column" style="min-height:100vh;">

    <div class="card p-4 shadow" style="width: 400px">
        <!-- Заголовок формы -->
        <h3 class="text-center mb-4">Вход в систему</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($msg): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>

        <!-- Форма авторизация -->
        <form method="POST" action="index.php">
            <!-- Поля для логина -->
            <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input type="text" class="form-control" id="login" placeholder="Введите логин" name="getlogin">
            </div>
            <!-- Поля для пароля -->
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" placeholder="Введите пароль" name="getpassword">
            </div>
            <!-- Кнопка отправки формы -->
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
        <br>
       
    </div>

    <script src="Bootstrap\bootstrap.js"></script>
</body>
</html>