<?php
// Подключение к базе данных
$mysqli = new mysqli("db", "root", "examplepassword", "mydb");

if ($mysqli->connect_error) {
    die("Ошибка подключения к базе данных: " . $mysqli->connect_error);
}

// Проверка, что пользователь аутентифицирован как администратор
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Обработка запроса на изменение роли пользователя
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["user_id"])) {
    $userId = $_POST["user_id"];
    $newRole = $_POST["new_role"];

    // Обновление роли пользователя в базе данных
    $updateQuery = "UPDATE users SET role = '$newRole' WHERE id = $userId";
    if ($mysqli->query($updateQuery)) {
        // Успешно обновлено, обновим список пользователей
        header("Location: admin.php");
        exit();
    } else {
        $error = "Ошибка при обновлении роли пользователя.";
    }
}

// Запрос всех пользователей из базы данных
$query = "SELECT * FROM users";
$result = $mysqli->query($query);

// Создание массива для хранения данных пользователей
$users = array();

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администратор</title>
    <style>
        /* Стили для контейнера администратора */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .admin-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Стили для таблицы */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        /* Стили для заголовков таблицы */
        th {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            text-align: left;
        }

        /* Стили для ячеек таблицы */
        td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        /* Стили для выпадающего списка */
        select {
            padding: 5px;
            font-size: 16px;
        }

        /* Стили для кнопки "Изменить роль" */
        input[type="submit"] {
            padding: 5px 10px;
            font-size: 14px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        /* Стили для ссылки "Выйти" */
        a {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        /* Стили для ссылки "Выйти" при наведении */
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Администратор</h2>
    <a href="logout.php">Выйти</a>
    <h3>Управление ролями пользователей:</h3>
    <table>
        <tr>
            <th>Пользователь</th>
            <th>Роль</th>
            <th>Изменить роль</th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?php echo $user["username"]; ?></td>
                <td><?php echo $user["role"]; ?></td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
                        <select name="new_role">
                            <option value="user">user</option>
                            <option value="admin">admin</option>
                        </select>
                        <input type="submit" value="Изменить роль">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
</body>
</html>
