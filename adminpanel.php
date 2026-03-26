<?php

    // Запускает сессию - без этого не получим данные пользователя
    // session_start() ВСЕГДА должна быть в самом начале файла
    session_start();

    // Проверяем: есть ли в сесси логин пользователя (То есть хотя бы что то)
    // Если нет - значит, пользователь не вошел в систему и мы его возвращаем на Авторизацию
    if (!isset($_SESSION['user_login'])) {
        // Перенаправляем на страницу входа
        header("Location: index.php");
        exit();
    }

    // Подключение к базе данных
    require "BD.php";

    $login = $_SESSION['user_login'];
    $user_id = $_SESSION['user_id'];
    
    //SQL Запрос
    $sql = "SELECT * FROM users WHERE login = :login";
    // prepare() - подготавливает запрос к выполнению
    $stmt = $pdo->prepare($sql);
    // Подставляем реальные знчаение вместо параметров
    $stmt->execute([':login' => $login]);
    // fetch() - достает одну строку из результата поиска
    // Если пользователь найден - в переменной $user будет массив с его данными
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    //var_dump($user);


    // ===========================================================
    // ВЫХОД ИЗ СИСТЕМЫ
    // ===========================================================
    if(isset($_POST['logout'])) {
        session_destroy(); // уничтожить сессию
        header("Location: index.php");
        exit();
    }

    // ===========================================================
    // БЛОК: СОХРАНЕНИЕ ЛИЧНЫХ ДАННЫХ
    // Срабатывает только когда нажата кнопка "Сохранить данные"
    // ===========================================================
    if(isset($_POST['save_portfolio'])) {
        // Собираем данные из формы + id из сессии
        $user_id = $_SESSION['user_id'];
        $full_name = $_POST['get_full_name'];
        $profession	= $_POST['get_profession'];
        $city = $_POST['get_city'];
        $phone = $_POST['get_phone'];
        $telegram = $_POST['get_telegram'];
        $github = $_POST['get_github'];
        $about = $_POST['get_about'];

        // prepare() - подготавливает запрос к выполнению
        $check = $pdo->prepare("SELECT id FROM portfolio WHERE user_id = :zon_uid");
        $check->execute([':zon_uid' => $user_id]);
        $exists = $check->fetch(); // fetch() вернет строку которую нашел (true) или вернет false (если ничего не нашел)

        if($exists) {
            // Запись есть - обновляем ее
            echo 'Сейчас будем обновлять данные!';
            $sql = "UPDATE portfolio SET
	                    full_name = :zon_full_name,
                        profession = :zon_profession,
                        city = :zon_city,
                        phone = :zon_phone,
                        telegram = :zon_telegram,
                        github = :zon_github,
                        about = :zon_about
                    WHERE user_id = :zon_uid";
        } else {
            // Записи нет - вставляем новую строку
            $sql = "INSERT INTO portfolio (user_id, full_name, profession, city, phone, telegram, github, about) 
                VALUES (:zon_uid, :zon_full_name, :zon_profession, :zon_city, :zon_phone, :zon_telegram, :zon_github, :zon_about);";
        }

        // prepare() - подготавливает запрос к выполнению
        $stmt = $pdo->prepare($sql);
        // Подставляем реальные значения вместо параметров (именных зон)
        $stmt->execute([
            ':zon_uid' => $user_id, 
            ':zon_full_name' => $full_name,
            ':zon_profession' => $profession,
            ':zon_city' => $city,
            ':zon_phone' => $phone,
            ':zon_telegram' => $telegram,
            ':zon_github' => $github,
            ':zon_about' => $about
        ]);

        echo '<div class="alert alert-success"> Личные данные сохранены!</div>';

    }



    // ===========================================================
    // БЛОК: ДОБАВЛЕНИЕ НОВОГО НАВЫКА
    // ===========================================================
    if(isset($_POST['add_skill'])) {

        // Сборка данных с формы
        $skill_name = $_POST['get_skill']; // название навыка
        $level = $_POST['get_level']; // уровень

        // Простая валидация: уровень должен быть от 1 до 100
        if($level < 1) {$level = 1;}
        if($level > 100) {$level = 100;}

        $sql = "INSERT INTO `skills` (`user_id`, `skill_name`, `level`) VALUES (:zon_uid, :zon_name, :zon_level);";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':zon_uid' => $user_id, 
            ':zon_name' => $skill_name,
            ':zon_level' => $level
        ]);
        echo "<div class='alert alert-success'>Навык $skill_name ($level%) добавлен в базу данных!</div>";
    }

    // ===========================================================
    // БЛОК: УДАЛЕНИЕ НАВЫКА
    // ===========================================================

    if(isset($_POST['delete_skill'])) {
        // Сборка данных с формы
        $skill_id_per = $_POST['skill_id'];
        
        $sql = "DELETE FROM `skills` WHERE `id` = :skill_id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':skill_id' => $skill_id_per
        ]);
        echo "<div class='alert alert-warning'> 🗑 Навык успешно удален из базы данных</div>";
    }




    // ===========================================================
    // БЛОК: ДОБАВЛЕНИЕ НОВОГО ПРОЕКТА
    // ===========================================================
    if(isset($_POST['add_project'])) {
        
        $title = $_POST['get_title'];
        $description = $_POST['get_description'];
        $url = $_POST['get_url'];

        $sql = "INSERT INTO `projects` (`user_id`, `title`, `description`, `url`) VALUES (:zon_uid, :zon_title, :zon_desc, :zon_url);";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':zon_uid' => $user_id, 
            ':zon_title' => $title,
            ':zon_desc' => $description,
            ':zon_url' => $url
        ]);

        echo "<div class='alert alert-success'>Проект $title добавлен в базу данных!</div>";
    }

    // ===========================================================
    // БЛОК: УДАЛЕНИЕ ПРОЕКТА
    // ===========================================================

    if(isset($_POST['delete_project'])) {
        // Сборка данных с формы
        $project_id_per = $_POST['project_id'];
        
        $sql = "DELETE FROM `projects` WHERE `id` = :zon_projectID;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':zon_projectID' => $project_id_per
        ]);
        echo "<div class='alert alert-warning'> 🗑 Проект успешно удален из базы данных</div>";
    }






 
    // ===========================================================
    // ЗАГРУЖАЕМ ДАННЫЕ ИЗ БД ДЛЯ ОТОБРАЖЕНИЯ НА СТРАНИЦЕ
    // ===========================================================

    // Личные данные
    $stmtLD = $pdo->prepare("SELECT * FROM portfolio WHERE user_id = :zon_uid"); // prepare() - подготавливает запрос к выполнению
    $stmtLD->execute([':zon_uid' => $user_id]);// Подставляем реальные знчаение вместо параметров
    $portfolio = $stmtLD->fetch(PDO::FETCH_ASSOC);// fetch() - достает одну строку из результата поиска
    if(!$portfolio) $portfolio = []; // если нет записи (первый раз пользователь заходит в ЛК) - пустой массив, чтобы не было ошибок
    
    // Навыки
    $stmtSK = $pdo->prepare("SELECT * FROM skills WHERE user_id = :zon_uid"); // prepare() - подготавливает запрос к выполнению
    $stmtSK->execute([':zon_uid' => $user_id]);// Подставляем реальные знчаение вместо параметров
    $skills = $stmtSK->fetchAll(PDO::FETCH_ASSOC);// fetchAll() - все строки сразу
    //echo '<pre>' . print_r($skills, 1) . '</pre>';

    // Список проектов
    $stmtPR = $pdo->prepare("SELECT * FROM projects WHERE user_id = :zon_uid"); // prepare() - подготавливает запрос к выполнению
    $stmtPR->execute([':zon_uid' => $user_id]);// Подставляем реальные знчаение вместо параметров
    $projects = $stmtPR->fetchAll(PDO::FETCH_ASSOC);// fetchAll() - все строки сразу
    //echo '<pre>' . print_r($projects, 1) . '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="Bootstrap\bootstrap.css">
</head>
<body class="bg-light container">
    <!-- ------------------ -->
    <div class="card d-flex justify-content-between align-items-center flex-row mt-4 mb-4 p-3">
        <h2>Добро пожаловать, <?php echo $user['name']; ?>!</h2>
        <form method="POST" action="adminpanel.php">
            <button type="submit" name="logout" class="btn btn-danger" value="1">Выйти</button>
        </form>
    </div>
    <!-- ------------------ -->
    <div class="card p-3 mb-4">
        <h2>Профиль</h2>
        <p><b>ID:</b> <?php echo $user['id']; ?></p>
        <p><b>Логин:</b> <?php echo $user['login']; ?></p>
        <p><b>Email:</b> <?php echo $user['email']; ?></p>
        <a href="portfolio.php?id=<?php echo $user['id']; ?>">Страница портфолио</a>
    </div>

    <div class="card d-flex justify-content-between align-items-center flex-row mt-4 mb-4 p-3">
        <h2>Система Портфолио (CRM)</h2>
    </div>

    <!-- =================================================== -->
    <!-- СЕКЦИЯ 1. ЛИЧНЫЕ ДАННЫЕ                             -->
    <!-- =================================================== -->
    <div class="card">
        <div class="card-header bg-warning-subtle fs-5">
            Личные данные
        </div>
        <div class="card-body">
            
            <form method="POST" action="adminpanel.php">

                <div class="row mb-4">
                    <!--Поле: ФИО  -->
                    <div class="col-6">
                        <label for="full_name" class="form-label">ФИО</label>
                        <input type="text" class="form-control" id="full_name" placeholder="Введите полное ФИО" name="get_full_name" value="<?php echo $portfolio['full_name'] ?? ''; ?>">
                    </div>
                    <!--Поле: Профессия  -->
                    <div class="col-6">
                        <label for="profession" class="form-label">Профессия</label>
                        <input type="text" class="form-control" id="profession" placeholder="Введите название профессии" name="get_profession" value="<?php echo $portfolio['profession'] ?? ''; ?>">
                    </div>
                </div>

                <div class="row mb-4">
                    <!--Поле: Город  -->
                    <div class="col-6">
                        <label for="city" class="form-label">Город</label>
                        <input type="text" class="form-control" id="city" placeholder="Введите город" name="get_city" value="<?php echo $portfolio['city'] ?? ''; ?>">
                    </div>
                    <!--Поле: Телефон  -->
                    <div class="col-6">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="phone" placeholder="Введите личный номер телефона" name="get_phone" value="<?php echo $portfolio['phone'] ?? ''; ?>">
                        <div class="form-text">В формате: 8(111)111-11-11</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!--Поле: Telegram  -->
                    <div class="col-6">
                        <label for="telegram" class="form-label">Telegram</label>
                        <input type="text" class="form-control" id="telegram" placeholder="Введите имя пользователя" name="get_telegram" value="<?php echo $portfolio['telegram'] ?? ''; ?>">
                        <div class="form-text">В формате: @username</div>
                    </div>
                    <!--Поле: GitHub  -->
                    <div class="col-6">
                        <label for="github" class="form-label">GitHub (ссылка)</label>
                        <input type="text" class="form-control" id="github" placeholder="Полная ссылка (url) на профиль" name="get_github" value="<?php echo $portfolio['github'] ?? ''; ?>">
                        <div class="form-text">В формате: https://github.com/username</div>
                    </div>
                </div>

                <!--Поле: О себе  -->
                <div class="mb-3">
                    <label for="about" class="form-label">О себе</label>
                    <textarea class="form-control" id="about" rows="10" placeholder="Напишите информацию о себе" name="get_about" ><?php echo $portfolio['about'] ?? ''; ?></textarea>
                </div>

                <button type="submit" name="save_portfolio" class="btn btn-warning">Сохранить данные</button>
            </form>



        </div>
    </div>

    <!-- =================================================== -->
    <!-- СЕКЦИЯ 2. НАВЫКИ                                    -->
    <!-- =================================================== -->
    <div class="card mt-4">
        <div class="card-header bg-success text-white fs-5">
            Навыки
        </div>
        <div class="card-body">
            <!-- Форма добавления навыка -->
            <form method="POST" action="adminpanel.php" class="row mb-4">
                 <div class="col-6">
                    <input type="text" class="form-control" name="get_skill" placeholder="Введите название навыка (PHP, MySQL, CSS, ...)" required>
                 </div>
                 <div class="col-4">
                    <input type="number" name="get_level" class="form-control" placeholder="Уровень (1-100)" min="1" max="100" required>
                 </div>
                 <div class="col-2">
                    <button type="submit" name="add_skill" class="btn btn-success text-white w-100">+ Добавить</button>
                 </div>
            </form>


            <?php if (empty($skills)): ?>
                        <!-- Навыков нет -->
                        <p class="text-muted">Навыки еще не добавлены.</p>
            <?php else: ?>
                        <?php foreach ($skills as $element_skill): ?>
                                <!-- Список навыков -->
                                <div class="d-flex align-items-center mb-2 gap-2">
                                    <span style="width:150px"><b><?= $element_skill['skill_name'] ?></b></span>

                                    <div class="progress flex-grow-1">
                                        <div class="progress-bar bg-success" style="width: <?= $element_skill['level'] ?>%"><?= $element_skill['level'] ?>%</div>
                                    </div>

                                    <form method="POST" action="adminpanel.php" class="mb-0">
                                        <input type="hidden" name="skill_id" value="<?= $element_skill['id'] ?>">
                                        <button type="submit" name="delete_skill" class="btn btn-sm btn-danger">🗑</button>  
                                    </form>
                                </div>
                        <?php endforeach; ?>
            <?php endif; ?>
            
        </div>
    </div>

    <!-- =================================================== -->
    <!-- СЕКЦИЯ 3. ПРОЕКТЫ                                   -->
    <!-- =================================================== -->
    
    <div class="card mt-4">
        <div class="card-header bg-dark text-white fs-5">
            Проекты
        </div>
        <div class="card-body">
            <!-- Форма добавления проекта -->
            <form method="POST" action="adminpanel.php" class="row mb-4">
                 <div class="col-6">
                        <input type="text" class="form-control" name="get_title" placeholder="Название проекта" required>
                 </div>
                 <div class="col-6">
                        <input type="text" class="form-control" name="get_url" placeholder="Ссылка на проект (https://...)" required>
                 </div>
                 <div class="col-12 mt-3">
                        <textarea class="form-control" rows="5" placeholder="Краткое описание проекта" name="get_description"></textarea>
                 </div>
                 <div class="col-2 mt-3">
                        <button type="submit" class="btn btn-dark" name="add_project">+ Добавить проект</button>
                 </div>
            </form>


            

            <?php if (empty($projects)): ?>
                        <!-- Проектов  нет -->
                        <p class="text-muted">Проекты еще не добавлены.</p>
            <?php else: ?>
                        <div class="row g-3">
                                <?php foreach ($projects as $el): ?>
                                        <!-- Список проектов -->
                                        <div class="col-4">
                                            <div class="card border-dark">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= $el['title'] ?></h5>
                                                    <p class="card-text text-muted"><?= $el['description'] ?></p>
                                                    <a href="<?= $el['url'] ?>" target="_blank" class="btn btn-sm btn-outline-dark">Открыть</a>
                                                </div>
                                                <div class="card-footer">
                                                    <form method="POST" action="adminpanel.php" class="mb-0">
                                                        <input type="hidden" name="project_id" value="<?= $el['id'] ?>">
                                                        <button type="submit" name="delete_project" class="btn btn-sm btn-danger">🗑 Удалить</button>  
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                        </div>
            <?php endif; ?>
            
                                    
           
           
            




            
            
        </div>
    </div>




    <br><br>
    <script src="Bootstrap\bootstrap.js"></script>
</body>
</html>