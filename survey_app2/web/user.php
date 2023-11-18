<?php
// Подключение к базе данных
$mysqli = new mysqli("db", "root", "examplepassword", "mydb");

if ($mysqli->connect_error) {
    die("Ошибка подключения к базе данных: " . $mysqli->connect_error);
}

// Проверка, что пользователь аутентифицирован
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit();
}

// Запрос к базе данных для получения списка доступных опросов
$query = "SELECT * FROM surveys";
$result = $mysqli->query($query);

// Создание массива для хранения списка опросов
$surveys = array();

while ($row = $result->fetch_assoc()) {
    $surveys[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пользователь</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
        }
        .user-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            margin: 0 auto;
            margin-top: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #007bff;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        li a {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            display: inline-block;
            transition: background-color 0.2s;
        }

        li a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="user-container">
        <h2>Доступные опросы</h2>
        <a href="logout.php">Выйти</a>
        <ul>
            <?php
            foreach ($surveys as $survey) {
                echo "<li><a href='survey.php?id=" . $survey["id"] . "'>" . $survey["title"] . "</a></li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>
