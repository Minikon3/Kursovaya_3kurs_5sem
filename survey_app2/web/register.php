<?php
// Подключение к базе данных
$mysqli = new mysqli("db", "root", "examplepassword", "mydb");

if ($mysqli->connect_error) {
    die("Ошибка подключения к базе данных: " . $mysqli->connect_error);
}

// Проверка, если форма регистрации отправлена
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Получение данных из формы
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Проверка, что пользователь с таким именем не существует
    $checkQuery = "SELECT * FROM users WHERE username = '$username'";
    $checkResult = $mysqli->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        $error = "Пользователь с таким именем уже существует.";
    } else {
        // Вставка нового пользователя в базу данных
        $insertQuery = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'user')";
        if ($mysqli->query($insertQuery)) {
            // Успешно зарегистрирован, выполните аутентификацию и переадресацию
            session_start();
            $_SESSION["username"] = $username;
            $_SESSION["role"] = "user";
            $_SESSION["id"] = $row["id"];
            header("Location: user.php");
            exit();
        } else {
            $error = "Ошибка регистрации. Пожалуйста, попробуйте позже.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        /* Ваши стили для формы регистрации */
        .registration-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7f7f7;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h2>Регистрация</h2>
        <form action="" method="post">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Зарегистрироваться">
        </form>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    </div>
</body>
</html>
