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

// Запрос к базе данных для получения пользователей и результатов опросов
$query = "SELECT u.username, s.title AS survey_title, q.question_text, o.option_text
          FROM users AS u
          JOIN survey_responses AS r ON u.id = r.user_id
          JOIN surveys AS s ON r.survey_id = s.id
          JOIN survey_questions AS q ON r.question_id = q.id
          JOIN survey_options AS o ON r.option_id = o.id
          ORDER BY u.username, s.title, q.question_text";

$result = $mysqli->query($query);

// Создание массива для хранения данных
$usersData = array();

while ($row = $result->fetch_assoc()) {
    $username = $row["username"];
    $surveyTitle = $row["survey_title"];
    $questionText = $row["question_text"];
    $optionText = $row["option_text"];

    if (!isset($usersData[$username])) {
        $usersData[$username] = array();
    }

    if (!isset($usersData[$username][$surveyTitle])) {
        $usersData[$username][$surveyTitle] = array();
    }

    if (!isset($usersData[$username][$surveyTitle][$questionText])) {
        $usersData[$username][$surveyTitle][$questionText] = array();
    }

    $usersData[$username][$surveyTitle][$questionText][] = $optionText;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администратор</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
        }
        /* Стиль для контейнера администратора */
        .admin-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7f7f7;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        /* Стиль для заголовка */
        .admin-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Стиль для ссылки "Выйти" */
        .admin-container a {
            display: block;
            margin-bottom: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .admin-container a:hover {
            text-decoration: underline;
        }

        /* Стиль для таблицы */
        .admin-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        /* Стиль для заголовков таблицы */
        .admin-container th {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            text-align: left;
        }

        /* Стиль для ячеек таблицы */
        .admin-container td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        /* Стиль для альтернативных строк таблицы */
        .admin-container tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Администратор</h2>
        <a href="logout.php">Выйти</a>
        <a href="admin_control.php">Перейти к управлению пользователями</a>
        <h3>Данные пользователей и результаты опросов:</h3>
        <table>
            <tr>
                <th>Пользователь</th>
                <th>Опрос</th>
                <th>Вопрос</th>
                <th>Ответы</th>
            </tr>
            <?php
            foreach ($usersData as $username => $surveys) {
                foreach ($surveys as $surveyTitle => $questions) {
                    foreach ($questions as $questionText => $options) {
                        echo "<tr>";
                        echo "<td>$username</td>";
                        echo "<td>$surveyTitle</td>";
                        echo "<td>$questionText</td>";
                        echo "<td>" . implode(", ", $options) . "</td>";
                        echo "</tr>";
                    }
                }
            }
            ?>
        </table>
    </div>
</body>
</html>
