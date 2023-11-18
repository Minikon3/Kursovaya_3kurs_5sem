<?php
// Подключение к базе данных
$mysqli = new mysqli("db", "root", "examplepassword", "mydb");

if ($mysqli->connect_error) {
    die("Ошибка подключения к базе данных: " . $mysqli->connect_error);
}

// Проверка, что пользователь аутентифицирован
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Обработка формы логина
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Запрос к базе данных для проверки существования пользователя
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $mysqli->query($query);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION["username"] = $row["username"];
        $_SESSION["role"] = $row["role"];
        $_SESSION["id"] = $row["id"]; // Установка идентификатора пользователя в сессии

        // Вход успешен
        if ($row["role"] === "admin") {
            // Переадресация на админ-панель
            header("Location: admin.php");
            exit();
        } else {
            // Переадресация на страницу обычного пользователя
            header("Location: user.php");
            exit();
        }
    } else {
        $error = "Неправильное имя пользователя или пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Логин</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            margin: 0 auto;
            margin-top: 100px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        form {
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Вход</h2>
        <form action="login.php" method="post">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Войти">
        </form>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    </div>
</body>
</html>
